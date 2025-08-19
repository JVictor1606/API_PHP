<?php


namespace Controllers;

use Bd\IRepository\ICarrinhoRepository;
use Bd\IRepository\IUserRepository;
use Bd\IRepository\IProductRepository;
use Models\Dto\UserRequest;
use Models\Dto\ProductRequest;
use Models\User;
use Models\Product;
use Exception;
use Models\Carrinho;
use Models\CarrinhoItems;
use Models\Enums\Status_Carrinho;
use Models\Enums\Status_item;
use PDO;

class CarrinhoController
{


    private ICarrinhoRepository $_repository;
    private IProductRepository  $_product_repository;
    private IUserRepository $_user_repository;

    public function __construct(ICarrinhoRepository $repository, IProductRepository $product_repository)
    {
        $this->_repository = $repository;
        $this->_product_repository = $product_repository;
    }


    /**
     * @OA\Get(
     *     path="/api/v1/users/carrinho",
     *     summary="Pega carrinho pelo id do usuario logado",
     *     tags={"Carrinho"},
     *     security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Carrinho selecionado com sucesso"),
     *     @OA\Response(response=404, description="Carrinho não encontrado"),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Não autorizado ou ID ausente")
     *         )
     *      )
     * )
     */
    public function SeeCarrinho(int $userId)
    {
        try {
            $carrinho = $this->_repository->GetCarrinhoByUserId($userId);

            if (!$carrinho || $carrinho === null) {
                http_response_code(404);
                return json_encode(['Error' => 'Produto não encontrado com este id']);
                exit;
            }

            if ($carrinho->GetUserId() !== $userId) {
                http_response_code(404);
                return json_encode(['Error' => 'Carrinho pertence a outro usuario']);
                exit;
            }

            if ($carrinho->GetUserId() === null || empty($carrinho->GetUserId())) {
                http_response_code(404);
                return json_encode(['Error' => 'Este usuario não tem carrinho']);
                exit;
            }


            $items = $this->_repository->GetItemsCarrinho($carrinho->GetId());

            if (empty($items) || $items == null) {
                return json_encode([
                    'message' => 'Carrinho está vazio',
                    'valorTotal' => 0,
                    'status' => $carrinho->GetStatusCarrinho()->value,
                    'items' => []
                ]);
            }

            $valorTotal = array_reduce($items, function ($total, CarrinhoItems $item) {
                return $total + $item->getValorTotal();
            }, 0);

            if ($carrinho->GetValorTotalCarrinho() != $valorTotal) {
                $carrinho->SetValorTotal($valorTotal);
                $this->_repository->UpdateCarrinho($carrinho);
            }

            $result = array_map(function (CarrinhoItems $item) {
                return [
                    'id do Produto no carrinho' => $item->getId(),
                    'Status' => $item->getStatusItem()->value,
                    'nome' => $item->getNomeProduto(),
                    'Valor unitario' => $item->getValorUnitario(),
                    'Valor Total' => $item->getValorTotal(),
                    'Quantidade' => $item->getQuantidade(),
                ];
            }, $items);

            return json_encode([
                "Valor do carrinho" => $carrinho->GetValorTotalCarrinho(),
                "Status" => $carrinho->GetStatusCarrinho()->value,
                "itens" => $result
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            return  json_encode(['message' => 'Erro ao ver o carrinho do usuario : ' . $e->getMessage()]);
        }
    }


    /**
     * @OA\Post(
     *     path="/api/v1/users/carrinho",
     *     summary="Adicionar produto no id do usuario logado",
     *     tags={"Carrinho"},
     *     security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *  @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"quantidade"},
     *             @OA\Property(property="quantidade", type="int",  example="1")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Produto adicionado no carrinho selecionado com sucesso"),
     *     @OA\Response(response=404, description="Produto não encontrado"),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Não autorizado ou ID ausente")
     *         )
     *      )
     * )
     */
    public function AddProductInCarrinho(int $userId, int $productId, int $quantidade)
    {
        try {
            $product = $this->_product_repository->GetProductById($productId);

            if ($product === null) {
                http_response_code(404);
                return json_encode(['message' => 'Produto não encontrado com este id']);
                exit;
            }
            $carrinho = $this->_repository->GetCarrinhoByUserId($userId);

            if ($carrinho === null) {
                http_response_code(404);
                return json_encode(['message' => 'Carrinho não encontrado com este id']);
                exit;
            }
            $valorTotal = $quantidade * $product->getValor();

            $item = new CarrinhoItems(
                $product->getNomeProduto(),
                $quantidade,
                $valorTotal,
                $product->getValor(),
                Status_item::PENDENTE,
                $product->getId(),
                $carrinho->GetId()
            );


            $result = $this->_repository->AddProductInsideCarrinho($item);

            $QuantidadeProduto = $product->getQuantidadeProduto() - $quantidade;
            $product->setQuantidadeProduto($QuantidadeProduto);
            $this->_product_repository->UpdateProduct($product);

            $carrinho->setStatusCarrinho(Status_Carrinho::ABERTO);
            $this->_repository->UpdateCarrinho($carrinho);

            http_response_code(200);
            return json_encode([
                'id do Produto no carrinho' => $product->getId(),
                'nome' => $product->getNomeProduto(),
                'Valor unitario' => $product->getValor(),
                'Valor Total' => $valorTotal,
                'Quantidade' => $result->getQuantidade(),
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            return  json_encode(['message' => 'Erro ao buscar ao adicionar o produto no carrinho do usuario :' . $e->getMessage()]);
        }
    }


        /**
     * @OA\Delete(
     *     path="/api/v1/users/carrinho/item",
     *     summary="Deletar Produto do carrinho",
     *     tags={"Carrinho"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Usuário deletado com sucesso"),
     *     @OA\Response(response=404, description="Usuário não encontrado"),
     *      @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Não autorizado ou ID ausente")
     *         )
     *     )
     * )
     */
    public function DeleteProductFromCarrrinho(int $id, int $userId)
    {
        $itemCarrinho = $this->_repository->GetItemCarrinhoById($id);
        $product = $this->_product_repository->GetProductById($itemCarrinho->getProdutoId());
        
        if ($itemCarrinho === null || empty($itemCarrinho)) {
            http_response_code(404);
            return json_encode(['message' => 'Item não encontrado no carrinho com este id']);
        }

        $carrinho = $this->_repository->GetCarrinhoByUserId($userId);
        if($userId !== $carrinho->GetUserId())
        {
            http_response_code(403);
            echo json_encode(['message' => 'Acesso não autorizado']);
        }

        try {
            http_response_code(200);
            $this->_repository->DeleteItemCarrinho($id);

            
            $QuantidadeProduto = $product->getQuantidadeProduto() + $itemCarrinho->getQuantidade();
            $product->setQuantidadeProduto($QuantidadeProduto);
            $this->_product_repository->UpdateProduct($product);

            return json_encode(['Sucess' => 'Item retirado do carrinho com sucesso']);

        } catch (\Throwable $e) {
           http_response_code(500);
            return json_encode(['message' => 'Erro ao Deletar o item do carrinho' . $e->getMessage()]);
        }

    }
}
