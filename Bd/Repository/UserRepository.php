<?php

namespace Bd\Repository;




use Bd\IRepository\IUserRepository;
use Bd\Repository_base;
use Exception;
use Models\User;
use PDO;

class UserRepository extends Repository_base implements IUserRepository
{

    private PDO $_conn;

    public function __construct(Repository_base $db)
    {
        $this->_conn = $db->GetConnection();
    }

    public function CreateUser(User $newUser): User
    {
        try {
            $sql = "INSERT INTO Usuarios (nome, email, senha) values (:name, :email, :password)";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':name', $newUser->getName());
            $stmt->bindValue(':email', $newUser->getEmail());
            $stmt->bindValue(':password', $newUser->getPassword());
            $stmt->execute();

            $lastId = $this->_conn->lastInsertId();
            $newUser->setId((int)$lastId);

            return $newUser;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao criar o usuario no banco de dados:' . $e->getMessage());
        }
    }

    public function UpdateUser(User $user): User
    {
        try {
            $sql = "UPDATE Usuarios SET nome = :name, email = :email, senha = :password WHERE id = :id";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':name', $user->getName());
            $stmt->bindValue(':email', $user->getEmail());
            $stmt->bindValue(':password', $user->getPassword());
            $stmt->bindValue(':id', $user->getId());
            $stmt->execute();

            return $user;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao atualizar o usuario no banco de dados:' . $e->getMessage());
        }
    }

    public function DeleteUser(int $id): bool
    {
        try {
            $sql = "DELETE FROM Usuarios WHERE id = :id";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            return true;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao Deleter o usuario no banco de dados:' . $e->getMessage());
            return false;
        }
    }

    public function GetAllUser(): array
    {
        try {
            $stmt = $this->_conn->query("SELECT * FROM Usuarios ORDER BY nome");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(function ($user) {
                return new User($user['nome'], $user['email'], $user['senha'], $user['id']);
            }, $results);
        } catch (\Throwable $e) {
            throw new Exception('Erro ao pegar todos os usuarios no banco de dados:' . $e->getMessage());
        }
    }

    public function GetUserById(int $id): ?User
    {
        try {
            $sql = "SELECT * FROM Usuarios WHERE id = :id";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ? new User($user['nome'], $user['email'], $user['senha'], $user['id']) : null;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao pegar o usuario no banco de dados pelo ID:' . $e->getMessage());
        }
    }

    public function GetUserByEmail(string $email): ?User
    {
        try {
            $sql = "SELECT * FROM Usuarios WHERE email = :email";
            $stmt = $this->_conn->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();

            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ? new User($user['nome'], $user['email'], $user['senha'] ,$user['id']) : null;
        } catch (\Throwable $e) {
            throw new Exception('Erro ao pegar o usuario no banco de dados pelo EMAIL:' . $e->getMessage());
        }
    }
}
