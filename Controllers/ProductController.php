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
use Services\Resources\ResourceProduct;
use Services\Response;

class ProductController
{
    private IProductRepository  $_repository;
    private IUserRepository $_user_repository;
    private ICarrinhoRepository $_carrinho_repository;

    public function __construct(IProductRepository $repository, IUserRepository $user_repository)
    {

        $this->_repository = $repository;
        $this->_user_repository = $user_repository;
    }


    /**
     * @OA\Post(
     *     path="/api/v1/user/product",
     *     summary="Criar novo Produto",
     *     tags={"Products"},
     *      security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome","descricao","valor", "quantidade"},
     *             @OA\Property(property="nome", type="string", example=""),
     *             @OA\Property(property="descricao", type="string",  example="Sobre o produto"),
     *             @OA\Property(property="valor", type="number", example="0,00"),
     *             @OA\Property(property="quantidade", type="int",  example="1"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Produto criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Todos os campos são obrigatorios")
     *         )
     *     )
     * )
     */
    public function CreateProduct(ProductRequest $request)
    {
        $user = $this->_user_repository->GetUserById($request->user_id);

        if (empty($request) ||  empty($request->nome) || empty($request->descricao) || empty($request->valor) || empty($request->quantidadeProduto)) {
            http_response_code(400);
            return json_encode(['message' => 'Todos os campos são obrigatorios']);
        }

        if (empty($request->user_id)) {
            http_response_code(400);
            return json_encode(['message' => 'Usuario criador do produto não informado']);
        }

        try {

            $product = new Product($request->nome, $request->descricao, $request->valor, $request->quantidadeProduto, $request->user_id);

            $result = $this->_repository->CreateProduct($product);
            http_response_code(201);
            return json_encode([
                'Dono' => $user->getName(),
                'id' => $result->getId(),
                'nome Produto' => $result->getNomeProduto(),
                'Descricao' => $result->getDescricao(),
                'Valor' => $result->getValor(),
                'Quantidade em Estoque' => $result->getQuantidadeProduto()
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            return json_encode(['message' => 'Erro no servidor ao criar o usuario' . $e->getMessage()]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/users/product",
     *     summary="Deletar produto",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Produto deletado com sucesso"),
     *     @OA\Response(response=404, description="Produto não encontrado"),
     *      @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Não autorizado ou ID ausente")
     *         )
     *     )
     * )
     */
    public function DeleteProduct(int $id, int $userId)
    {
        try {
            $productExist = $this->_repository->GetProductById($id);
            if ($productExist === null) {
                http_response_code(404);
                return json_encode(['Error' => 'Produto não encontrado com este id']);
            }
            if ($productExist->getUserId() !== $userId) {
                http_response_code(401);
                return json_encode(['Error' => 'Você não pode deleter um produto a qual não te pertence']);
            }

            $this->_repository->DeleteProduct($id);
            http_response_code(200);
            return json_encode(['Sucess' => 'Produto Deletado com sucesso']);
        } catch (\Throwable $e) {
            http_response_code(500);
            return json_encode(['message' => 'Erro no servidor ao Deletar o usuario' . $e->getMessage()]);
        }
    }


    /**
     * @OA\Put(
     *     path="/api/v1/users/product",
     *     summary="Atualiza o Produto",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(
     *          type="integer"
     *      )
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *            required={"nome","descricao","valor", "quantidade"},
     *             @OA\Property(property="nome", type="string", example=""),
     *             @OA\Property(property="descricao", type="string",  example="Sobre o produto"),
     *             @OA\Property(property="valor", type="number", example="0,00"),
     *             @OA\Property(property="quantidade", type="int",  example="1")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário Atualizado com sucesso",
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Todos os campos são obrigatorios.")
     *         )
     *     ),
     *      @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Não autorizado ou ID ausente")
     *         )
     *      )
     * )
     */
    public function UpdateProduct(int $id, int $userId, ProductRequest $request)
    {
       
        
            if (empty($request) ||  empty($request->nome) || empty($request->descricao) || empty($request->valor) || empty($request->quantidadeProduto)) {
            http_response_code(400);
            return json_encode(['message' => 'Todos os campos são obrigatorios']);
        }
        try {

             $productExist = $this->_repository->GetProductById($id);
            if ($productExist === null) {
                http_response_code(404);
                return json_encode(['Error' => 'Produto não encontrado com este id']);
            }
            if ($productExist->getUserId() !== $userId) {
                http_response_code(401);
                return json_encode(['Error' => 'Você não pode atualizar um produto a qual não te pertence']);
            }


            $product = new Product(
                $request->nome,
                $request->descricao,
                $request->valor,
                $request->quantidadeProduto,
                $request->user_id,
                $id
            );
            $user = $this->_user_repository->GetUserById($request->user_id);

            $result = $this->_repository->UpdateProduct($product);

            

            http_response_code(200);
            return json_encode([
                'Dono' => $user->getName(),
                'id' => $result->getId(),
                'nome Produto' => $result->getNomeProduto(),
                'Descricao' => $result->getDescricao(),
                'Valor' => $result->getValor(),
                'Quantidade em Estoque' => $result->getQuantidadeProduto()
            ]);
        } 
        catch (\Throwable $e) {
            http_response_code(500);
            return json_encode(['message' => 'Erro no servidor ao Ataualizar o usuario' . $e->getMessage()]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/products", tags={"Products"},security={{"bearerAuth":{}}},
     *      summary="Mostra todos os Produtos",
     *     @OA\Response(response="200", description="Success"),
     *     @OA\Response(response="404", description="Not Found"),
     *      @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Não autorizado ou ID ausente")
     *         )
     *      )
     * )
     */
    public function GetAllProducts()
    {

        try {

           $products = $this->_repository->GetAllProduct();
           $productResponse = ResourceProduct::collect($products);
           
            (new Response()->json( ['Produtos' => $productResponse ]));

        } catch (\Throwable $e) {
            http_response_code(500);
            return json_encode(['message' => 'Erro ao buscar todos os produtos : ' . $e->getMessage()]);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/users/products/userEmail",
     *     summary="Pega produto a partir do email do criador do produto",
     *     tags={"Products"},
     *          security={{"bearerAuth":{}}},
     *         @OA\Parameter(
     *         name="email",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Usuário selecionado com sucesso"),
     *     @OA\Response(response=404, description="Usuário não encontrado"),
     *      @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Não autorizado ou ID ausente")
     *         )
     *      )
     * )
     */
    public function GetProductByCreatedEmail(string $email)
    {
        $user = $this->_user_repository->GetUserByEmail($email);
        if ($user == null) {
            http_response_code(401);
            return json_encode(['Error' => 'Este criador não existe ou não esta cadastrado ']);
        }

        $products = $this->_repository->GetAllProductByUserId($user->getId());
        $result = array_map(function (Product $product) {
            $userOwn = $this->_user_repository->GetUserById($product->getUserId());
            return [
                'Criado por' => $userOwn->getName(),
                'id do Produto' => $product->getId(),
                'nome' => $product->getNomeProduto(),
                'descricao' => $product->getDescricao(),
                'Valor' => $product->getValor(),
                'Produtos em estoque' => $product->getQuantidadeProduto()
            ];
        }, $products);

        http_response_code(200);
        return json_encode($result);
    }


    /**
     * @OA\Get(
     *     path="/api/v1/users/product",
     *     summary="Pega produto pelo Id",
     *     tags={"Products"},
     *     security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Produto selecionado com sucesso"),
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
    public function GetProductById(int $id)
    {
        try {
            $product = $this->_repository->GetProductById($id);

            if ($product == null) {
                http_response_code(404);
                return json_encode(['message' => 'Produto não encontrado com este id']);
            }

            $userOwn = $this->_user_repository->GetUserById($product->getUserId());
            http_response_code(200);
            return json_encode([
                'Criado por' => $userOwn->getName(),
                'id do Produto' => $product->getId(),
                'nome' => $product->getNomeProduto(),
                'descricao' => $product->getDescricao(),
                'Valor' => $product->getValor(),
                'Produtos em estoque' => $product->getQuantidadeProduto()
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            return  json_encode(['message' => 'Erro no servidor ao buscar o produto por ID' . $e->getMessage()]);
        }
    }


}
