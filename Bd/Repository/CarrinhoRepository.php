<?php

namespace Bd\Repository;

use Bd\IRepository\ICarrinhoRepository;
use Bd\Repository_base;
use Exception;
use Models\Product;
use Models\Carrinho;
use Models\CarrinhoItems;
use Models\Enums\Status_Carrinho;
use Models\Enums\Status_item;
use Models\User;
use PDO;

class CarrinhoRepository extends Repository_base implements ICarrinhoRepository
{
    private PDO $_conn;

    public function __construct(Repository_base $db)
    {
        $this->_conn = $db->GetConnection();
    }

    public function CreateCarrinho(Carrinho $carrinho): Carrinho
    {
        try {
            $sql = "INSERT INTO Carrinho (valorTotal, status, user_id) values (:valorTotal, :status, :user_id)";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':valorTotal', $carrinho->GetValorTotalCarrinho());
            $stmt->bindValue(':status', $carrinho->GetStatusCarrinho()->value);
            $stmt->bindValue(':user_id', $carrinho->getUserId());
            $stmt->execute();

            $lastId = $this->_conn->lastInsertId();
            $carrinho->setId((int)$lastId);

            return $carrinho;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao criar o carrinho no banco de dados:' . $e->getMessage());
        }
    }

    public function UpdateCarrinho(Carrinho $carrinho): Carrinho
    {
        try {
            $sql = "UPDATE Carrinho SET valorTotal = :valorTotal, status = :status WHERE  user_id = :user_id";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':valorTotal', $carrinho->GetValorTotalCarrinho());
            $stmt->bindValue(':status', $carrinho->GetStatusCarrinho()->value);
            $stmt->bindValue(':user_id', $carrinho->getUserId());
            $stmt->execute();
            $stmt->execute();

            return $carrinho;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao atualizar o Carrinho no banco de dados:' . $e->getMessage());
        }
    }

    public function DeleteCarrinho(int $id): bool
    {
        try {
            $sql = "DELETE FROM Carrinho WHERE id_Carrinho = :id_Croduto";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':id_Carrinho', $id);
            $stmt->execute();

            return true;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao Deleter o carrinho no banco de dados:' . $e->getMessage());
            return false;
        }
    }

    public function AddProductInsideCarrinho(CarrinhoItems $item): CarrinhoItems
    {
        try {
            $sql = "INSERT INTO carrinho_items (nome_item, quantidade_comprada, valorTotal, valorUnitario, status, produto_id,carrinho_id) values (:nome_item, :quantidade_comprada, :valorTotal, :valorUnitario, :status, :produto_id, :carrinho_id)";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':nome_item', $item->getNomeProduto());
            $stmt->bindValue(':quantidade_comprada', $item->getQuantidade());
            $stmt->bindValue(':valorTotal', $item->getValorTotal());
            $stmt->bindValue(':valorUnitario', $item->getValorUnitario());
            $stmt->bindValue(':status', $item->getStatusItem()->value);
            $stmt->bindValue(':produto_id', $item->getProdutoId());
            $stmt->bindValue(':carrinho_id', $item->getCarrinhoId());
            $stmt->execute();


            $lastId = $this->_conn->lastInsertId();
            $item->setId((int)$lastId);

            return $item;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao adicionar o item no carrinho no banco de dados:' . $e->getMessage());
        }
    }

    public function DeleteItemCarrinho(int $id): bool
    {
        try {
            $sql = "DELETE FROM carrinho_items WHERE id_CarrinhoItems = :id_CarrinhoItems";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':id_CarrinhoItems', $id);
            $stmt->execute();

            return true;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao Deleter o item do carrinho no banco de dados:' . $e->getMessage());
            return false;
        }
    }
        public function UpdateItemCarrinho(CarrinhoItems $item): CarrinhoItems
    {
        try {
            $sql = "UPDATE carrinho_items SET quantidade_comprada = :quantidade_comprada,valorTotal = :valorTotal, status = :status WHERE  produto_id = :produto_id";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':valorTotal', $item->getValorTotal());
            $stmt->bindValue(':status', $item->getStatusItem()->value);
            $stmt->bindValue(':quantidade_comprada', $item->getQuantidade());
            $stmt->bindValue(':produto_id', $item->getProdutoId());
            $stmt->execute();
            $stmt->execute();

            return $item;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao atualizar o item do Carrinho no banco de dados:' . $e->getMessage());
        }
    }

    public function GetCarrinhoByUserId(int $userid): Carrinho
    {
        try {
            $sql = "SELECT * FROM Carrinho WHERE user_id = :user_id";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':user_id', $userid);
            $stmt->execute();

            $carrinho = $stmt->fetch(PDO::FETCH_ASSOC);
            $status = Status_Carrinho::from($carrinho['status']);

            return $carrinho ? new Carrinho($carrinho['valorTotal'], $status,  $carrinho['user_id'], $carrinho['id_Carrinho']) : null;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao pegar o carrinho no banco de dados pelo ID do usuario:' . $e->getMessage());
        }
    }

    public function GetCarrinhoById(int $id): Carrinho
    {
        try {
            $sql = "SELECT * FROM Carrinho WHERE id_Carrinho = :id_Carrinho";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':id_Carrinho', $id);
            $stmt->execute();

            $carrinho = $stmt->fetch(PDO::FETCH_ASSOC);
            $status = Status_Carrinho::from($carrinho['status']);
            return $carrinho ? new Carrinho($carrinho['valorTotal'], $status,  $carrinho['user_id'], $carrinho['id_Carrinho']) : null;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao pegar o carrinho no banco de dados pelo ID:' . $e->getMessage());
        }
    }

    public function GetItemCarrinhoById(int $id): ?CarrinhoItems
    {
        try {
            $sql = "SELECT * FROM carrinho_items WHERE id_CarrinhoItems = :id_CarrinhoItems";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':id_CarrinhoItems', $id);
            $stmt->execute();

            $Itemcarrinho = $stmt->fetch(PDO::FETCH_ASSOC);


            $status = isset($Itemcarrinho['status']) ? Status_item::tryFrom($Itemcarrinho['status']) : null;
            
            return $Itemcarrinho ? new CarrinhoItems($Itemcarrinho['nome_item'], $Itemcarrinho['quantidade_comprada'],  $Itemcarrinho['valorTotal'], $Itemcarrinho['valorUnitario'],  $status, $Itemcarrinho['produto_id'], $Itemcarrinho['carrinho_id'], $Itemcarrinho['id_CarrinhoItems']) : null;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao pegar o item do carrinho no banco de dados pelo ID:' . $e->getMessage());
        }
    }

    public function GetItemsCarrinho(int $carrinhoId): array
    {
        try {
            $sql = "SELECT * FROM carrinho_items WHERE carrinho_id = :carrinho_id order by valorTotal DESC";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':carrinho_id', $carrinhoId);
            $stmt->execute();

            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return array_map(function ($Itemcarrinho) {
                $status = Status_item::from($Itemcarrinho['status']);
                return new CarrinhoItems($Itemcarrinho['nome_item'], $Itemcarrinho['quantidade_comprada'],  $Itemcarrinho['valorTotal'], $Itemcarrinho['valorUnitario'],  $status, $Itemcarrinho['produto_id'], $Itemcarrinho['carrinho_id'], $Itemcarrinho['id_CarrinhoItems']);
            }, $results ?? []);
        } catch (\Throwable $e) {
            throw new Exception('Erro ao pegar todos os items do carrinho no banco de dados:' . $e->getMessage());
        }
    }
}
