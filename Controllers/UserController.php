<?php

namespace Controllers;

/**
 * @OA\Info(
 *     title="MY API PHP", 
 *     version="1.0.0"
 * )
 * @OA\Server(
 *     url="http://localhost/Trabalho/MVC_inscricao/",
 *     description="Servidor local"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

use Bd\IRepository\ICarrinhoRepository;
use Bd\IRepository\IUserRepository;
use Models\Dto\UserRequest;
use Models\User;
use Models\Carrinho;
use Exception;
use Models\Enums\Status_Carrinho;

class UserController
{

    private IUserRepository $_repository;
    private ICarrinhoRepository $_carrinho_repository;
    private Status_Carrinho $status_carrinho;

    public function __construct(IUserRepository $repository, ICarrinhoRepository $carrinho_repository)
    {

        $this->_repository = $repository;
        $this->_carrinho_repository = $carrinho_repository;
    }


    /**
     * @OA\Post(
     *     path="/api/v1/users",
     *     summary="Criar novo usuário",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"nome","email","password"},
     *             @OA\Property(property="nome", type="string", example=""),
     *             @OA\Property(property="email", type="string", format="email", example="@gmail.com"),
     *             @OA\Property(property="password", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Usuário criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="UserName"),
     *             @OA\Property(property="email", type="string", example="userEmail@email.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Nome, email e senha são obrigatórios.")
     *         )
     *     )
     * )
     */
    public function CreateUser(UserRequest $request)
    {
        if (empty($request->nome) || empty($request->email) || empty($request->password)) {
            http_response_code(400);
            return json_encode(['message' => 'Nome, email e senha são obrigatórios.']);
        }

        $existUser = $this->_repository->GetUserByEmail($request->email);
        if ($existUser != null) {
            http_response_code(400);
            return json_encode(['Error' => 'Ja existe um usuario cadastrado com este Email']);
        }
        try {
            $password = password_hash($request->password, PASSWORD_DEFAULT);
            $user = new User($request->nome, $request->email,$password);

            $result = $this->_repository->CreateUser($user);

            $carrinho = new Carrinho(0.0, Status_Carrinho::FECHADO, $user->getId());
            $this->_carrinho_repository->CreateCarrinho($carrinho);

            

            http_response_code(201);
            return json_encode([

                'id' => $result->getId(),
                'nome' => $result->getName(),
                'email' => $result->getEmail()
            ]);



        } catch (\Throwable $e) {
            http_response_code(500);
            return json_encode(['message' => 'Erro no servidor ao criar o usuario' . $e->getMessage()]);
        }
    }



     /**
     * @OA\Put(
     *     path="/api/v1/users",
     *     summary="Atualiza o usuário",
     *     tags={"Users"},
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
     *             required={"nome","email","password"},
     *             @OA\Property(property="nome", type="string", example=""),
     *             @OA\Property(property="email", type="string", format="email", example="@gmail.com"),
     *             @OA\Property(property="password", type="string", example="")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário Atualizado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="UserName"),
     *             @OA\Property(property="email", type="string", example="userEmail@email.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Nome, email e senha são obrigatórios.")
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
    public function UpdateUser(int $id, UserRequest $request)
    {
        if (empty($request->nome) || empty($request->email) || empty($request->password)) {
            http_response_code(400);
            return json_encode(['message' => 'Nome, email e senha são obrigatórios.']);
        }

        try {
            $user = $this->_repository->GetUserById($id);
            if ($user == null) {
                http_response_code(404);
                return json_encode(['Error' => 'Usuario não encontrado com este id']);
            }

            $existUser = $this->_repository->GetUserByEmail($request->email);
            if ($existUser != null && $existUser->getId() !== $id) {
                http_response_code(400);
                return json_encode(['Error' => 'Este email já está em uso por outro usuário']);
            }


            $password = password_hash($request->password, PASSWORD_DEFAULT);
            $updatedUser = new User(
                $request->nome,
                $request->email,
                $password,
                $id
            );

            $result = $this->_repository->UpdateUser($updatedUser);

            http_response_code(200);
            return json_encode([
                'id' => $result->getId(),
                'nome' => $result->getName(),
                'email' => $result->getEmail()
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            return json_encode(['message' => 'Erro no servidor ao Ataualizar o usuario' . $e->getMessage()]);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/users",
     *     summary="Deletar usuário",
     *     tags={"Users"},
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
    public function DeleteUser(int $id)
    {
        try {
            $userExist = $this->_repository->GetUserById($id);
            if ($userExist == null) {
                http_response_code(404);
                return json_encode(['Error' => 'Usuario não encontrado com este id']);
            }

            $this->_repository->DeleteUser($id);
            http_response_code(200);
            return json_encode(['Sucess' => 'Usuario Deletado com sucesso']);
        } catch (\Throwable $e) {
            http_response_code(500);
            return json_encode(['message' => 'Erro no servidor ao Deletar o usuario' . $e->getMessage()]);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/users", tags={"Users"},security={{"bearerAuth":{}}},
     *      summary="Mostra todos osusuário",
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
    public function GetAllUsers()
    {
        try {
            $users = $this->_repository->GetAllUser();

            $result = array_map(function (User $user) {
                return [
                    'id' => $user->getId(),
                    'nome' => $user->getName(),
                    'email' => $user->getEmail()
                ];
            }, $users);

            http_response_code(200);
            return json_encode($result);
        } catch (\Throwable $e) {
            http_response_code(500);
            return json_encode(['message' => 'Erro ao buscar todos os usuarios' . $e->getMessage()]);
        }
    }


    /**
     * @OA\Get(
     *     path="/api/v1/user",
     *     summary="Pega usuário pleo Id",
     *     tags={"Users"},
     *     security={{"bearerAuth":{}}},
     *  @OA\Parameter(
     *         name="id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Usuário selecionado com sucesso"),
     *     @OA\Response(response=404, description="Usuário não encontrado"),
     *     @OA\Response(
     *         response=401,
     *         description="Não autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Não autorizado ou ID ausente")
     *         )
     *      )
     * )
     */
    public function GetUserById(int $id)
    {
        try {
            $user = $this->_repository->GetUserById($id);
            
            if ($user == null) {
                http_response_code(404);
                return json_encode(['message' => 'Usuario não encontrado com este id']);
            }

            http_response_code(200);
            return json_encode([
                'id' => $user->getId(),
                'nome' => $user->getName(),
                'email' => $user->getEmail()
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
           return  json_encode(['message' => 'Erro no servidor ao buscar o usuario por ID' . $e->getMessage()]);
        }
    }


        /**
     * @OA\Get(
     *     path="/api/v1/usersEmail",
     *     summary="Pega usuário pelo Email",
     *     tags={"Users"},
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
    public function GetUserByEmail(string $email)
    {
        try {
            $user = $this->_repository->GetUserByEmail($email);

            if ($user == null) {
                http_response_code(404);
                return json_encode(['message' => 'Usuario não encontrado com este email']);
            }

            http_response_code(200);
            return json_encode([
                'id' => $user->getId(),
                'nome' => $user->getName(),
                'email' => $user->getEmail()
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
           return json_encode(['message' => 'Erro no servidor ao buscar o usuario por EMAIL' . $e->getMessage()]);
        }
    }
}
