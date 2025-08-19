<?php

namespace Controllers;



use Bd\IRepository\IUserRepository;
use Models\Dto\UserRequest;
use Models\User;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController
{

    private IUserRepository $_repository;

    public function __construct(IUserRepository $repository)
    {

        $this->_repository = $repository;
    }


    /**
     * @OA\Post(
     *     path="/api/v1/users/Auth",
     *     summary="Autenticar Usuário",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="joao@gmail.com"),
     *             @OA\Property(property="password", type="string", example="123456")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuário logado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="nome", type="string", example="João"),
     *             @OA\Property(property="email", type="string", example="joao@example.com"),
     *             @OA\Property(property="token", type="string", example="eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Dados inválidos",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email e senha são obrigatórios.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Credenciais inválidas",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Email ou senha inválidos.")
     *         )
     *     )
     * )
     */
    public function AuthUser(UserRequest $request)
    {
        $user = $this->_repository->GetUserByEmail($request->email);

        if ($user === null) {
            http_response_code(401);
            echo json_encode(['Error' => "Usuario não exite"]);
            exit;
        }

        if (!password_verify($request->password, $user->getPassword())) {
            http_response_code(401);
            echo json_encode(['Error' => "Senha ou email errados"]);
            exit;
        }

        try {
            $payload = [
                // 'iss' => 'http://localhost/Trabalho/MVC_inscricao',
                // 'aud' => 'http://localhost/Trabalho/MVC_inscricao/swagger-ui/',
                "exp" => time() + (60 * 30),
                "iat" => time(),
                "user" => [
                    "id" => $user->getId(),
                    "email" => $user->getEmail(),
                ]
            ];

            $encode = JWT::encode($payload, $_ENV['KEY'], 'HS256');

            http_response_code(200);

            return json_encode([

                'id' => $user->getId(),
                'nome' => $user->getName(),
                'email' => $user->getEmail(),
                "token" => $encode
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            return json_encode(['message' => 'Erro ao autenticar: ' . $e->getMessage()]);
        }
    }

    public function validaToken($token)
    {
        try {
            $secretKey = $_ENV['KEY'] ?? null;
            if (!$secretKey) {
                return false;
            }

            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            return (array) $decoded;
            
        } catch (\Exception $e) {
            if ($e->getMessage() === "Expired token") {
                http_response_code(401);
                echo json_encode([
                    'Usuario' => 'Deslogado',
                    'Erro' => 'Token Expirou'

                ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                exit;
            }
            return false;
        }
    }
}
