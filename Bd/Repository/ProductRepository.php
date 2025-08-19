<?php


namespace Bd\Repository;

use Bd\IRepository\IProductRepository;
use Bd\Repository_base;
use Exception;
use Models\Product;
use Models\Carrinho;
use Models\CarrinhoItems;
use Models\User;
use PDO;

class ProductRepository extends Repository_base implements IProductRepository
{

    private PDO $_conn;

    public function __construct(Repository_base $db)
    {
        $this->_conn = $db->GetConnection();
    }

    public function CreateProduct(Product $newProduct): Product
    {
        try {
            $sql = "INSERT INTO Produtos (nome_produto, descricao, valor, quantidade_produto, quantidade_vendida, user_id) values (:nome_produto, :descricao, :valor, :quantidade_produto, :quantidade_vendida, :user_id)";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':nome_produto', $newProduct->getNomeProduto());
            $stmt->bindValue(':descricao', $newProduct->getDescricao());
            $stmt->bindValue(':valor', $newProduct->getValor());
            $stmt->bindValue(':quantidade_produto', $newProduct->getQuantidadeProduto());
            $stmt->bindValue(':quantidade_vendida', $newProduct->getQuantidadeVendida());
            $stmt->bindValue(':user_id', $newProduct->getUserId());
            $stmt->execute();

            $lastId = $this->_conn->lastInsertId();
            $newProduct->setId((int)$lastId);

            return $newProduct;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao criar o produto no banco de dados:' . $e->getMessage());
        }
    }


    public function UpdateProduct(Product $product): Product
    {
        try {
            $sql = "UPDATE Produtos SET nome_produto = :nome_produto, descricao = :descricao, valor = :valor, quantidade_produto = :quantidade_produto, quantidade_vendida = :quantidade_vendida WHERE id_Produto = :id_Produto and user_id = :user_id";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':nome_produto', $product->getNomeProduto());
            $stmt->bindValue(':descricao', $product->getDescricao());
            $stmt->bindValue(':valor', $product->getValor());
            $stmt->bindValue(':quantidade_produto', $product->getQuantidadeProduto());
            $stmt->bindValue(':quantidade_vendida', $product->getQuantidadeVendida());
            $stmt->bindValue(':id_Produto', $product->getId());
            $stmt->bindValue(':user_id', $product->getUserId());
            $stmt->execute();

            return $product;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao atualizar o produto no banco de dados:' . $e->getMessage());
        }
    }

    public function DeleteProduct(int $id): bool
    {
         try {
            $sql = "DELETE FROM Produtos WHERE id_Produto = :id_Produto";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':id_Produto', $id);
            $stmt->execute();

            return true;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao Deleter o produto no banco de dados:' . $e->getMessage());
            return false;
        }
    }

    public function GetAllProduct(): array
    {
        try {
            $stmt = $this->_conn->query("SELECT * FROM Produtos ORDER BY valor");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function ($product) {
                return new Product($product['nome_produto'], $product['descricao'], $product['valor'], $product['quantidade_produto'], $product['user_id'], $product['id_Produto']);
            }, $results);
        } catch (\Throwable $e) {
            throw new Exception('Erro ao pegar todos os usuarios no banco de dados:' . $e->getMessage());
        }
    }

    public function GetProductById(int $id) : ?Product
    {
         try {
            $sql = "SELECT * FROM Produtos WHERE id_Produto = :id_Produto";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':id_Produto', $id);
            $stmt->execute();

            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            return $product ? new Product($product['nome_produto'], $product['descricao'], $product['valor'], $product['quantidade_produto'], $product['user_id'], $product['id_Produto']) : null;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao pegar o usuario no banco de dados pelo ID:' . $e->getMessage());
        }
    }

    public function GetAllProductByUserId(int $userId): array
    {
        try {
            $stmt = $this->_conn->prepare("SELECT id_Produto, nome_produto, descricao, valor, quantidade_produto, quantidade_vendida, user_id FROM Produtos where user_id = :user_id ORDER BY valor ");
            $stmt->bindValue(':user_id', $userId);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function ($product) {
                return new Product($product['nome_produto'], $product['descricao'], $product['valor'], $product['quantidade_produto'], $product['user_id'], $product['id_Produto']);
            }, $results);

        } catch (\Throwable $e) {
            throw new Exception('Erro ao pegar todos os Produtos no carrinho do usuario no banco de dados:' . $e->getMessage());
        }
    }


}
