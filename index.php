<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers:Authorization, Content-Type, x-xsrf-token, x-csrtoken, X-Requested-With");

require_once 'vendor/autoload.php';

use Bd\Repository\ProductRepository;
use Controllers\UserController;
use Bd\Repository\UserRepository;
use Bd\Repository\CarrinhoRepository;
use Bd\Repository_base;
use Controllers\AuthController;
use Controllers\CarrinhoController;
use Controllers\ProductController;
use Models\Dto\UserRequest;
use Models\Dto\ProductRequest;
use Services\Response;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$db = new Repository_base();
$userRepository = new UserRepository($db);
$productRepository = new ProductRepository($db);
$carrinhoRepository = new CarrinhoRepository($db);
$userController = new UserController($userRepository, $carrinhoRepository);
$productController = new ProductController($productRepository, $userRepository);
$AuthController = new AuthController($userRepository);
$CarrinhoController = new CarrinhoController($carrinhoRepository, $productRepository);

$method = $_SERVER['REQUEST_METHOD'];

//var_dump(apache_request_headers());

$authorization = isset($_SERVER['HTTP_AUTHORIZATION']) ?  $_SERVER['HTTP_AUTHORIZATION'] : '';
$token = str_replace('Bearer ', '', $authorization);

$secretKey = $_ENV['KEY'];
$isAuthenticated =  !empty($token) && $AuthController->validaToken($token) !== false;

$headers = apache_request_headers();

$userToken = $AuthController->validaToken($token);

$requestUri = isset($_GET['url']) ? rtrim($_GET['url'], '/') : '';


switch ($method) {
    case 'POST':
        if ($requestUri === 'api/v1/users') {

            $body = json_decode(file_get_contents('php://input'), true);


            if ($body === null || !isset($body['nome'], $body['email'], $body['password'])) {
                http_response_code(400);
                echo json_encode(['message' => 'Incompleto: nome, email e senha são obrigatórios']);
                exit;
            }


            $userRequest = new UserRequest(
                nome: $body['nome'],
                email: $body['email'],
                password: $body['password'],
                confirmPassword: $body['confirmPassword'] ?? null
            );

            echo $userController->CreateUser($userRequest);
            break;
        } elseif ($requestUri === 'api/v1/users/Auth') {
            try {
                $body = json_decode(file_get_contents('php://input'), true);


                if ($body === null || !isset($body['email'], $body['password'])) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Incompleto: email e senha são obrigatórios']);;
                    break;
                }


                $userRequest = new UserRequest(
                    nome: $body['nome'] ?? null,
                    email: $body['email'],
                    password: $body['password'],
                    confirmPassword: $body['confirmPassword'] ?? null
                );

                echo $AuthController->AuthUser($userRequest);
            } catch (\Throwable $e) {
                http_response_code(500);
                echo json_encode(['message' => 'Endpoint não encontrado']);
            }
        } elseif ($requestUri === 'api/v1/user/product'  && $isAuthenticated) {
            $body = json_decode(file_get_contents('php://input'), true);

            $valor = (float) str_replace(',', '.', $body['valor']);

            $ProductRequest = new ProductRequest(
                nome: $body['nome'],
                descricao: $body['descricao'],
                valor: $valor,
                quantidadeProduto: $body['quantidade'],
                quantidadeVendida: null,
                user_id: $userToken['user']->id,
            );

            echo $productController->CreateProduct($ProductRequest);
            break;
        } elseif ($requestUri === 'api/v1/users/carrinho' && $_GET['id']  && $isAuthenticated) {
            try {
                $id = (int)$_GET['id'];
                $userId =  $userToken['user']->id;
                $body = json_decode(file_get_contents('php://input'), true);

                if (!isset($body['quantidade'])) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Quantidade é obrigatória']);
                    break;
                }

                $quantidade = (int)$body['quantidade'];



                echo $CarrinhoController->AddProductInCarrinho($userId, $id, $quantidade);
            } catch (\Throwable $e) {
                http_response_code(500);
                echo json_encode(['Error' => 'Erro no servidor ao adicionar o produto no carrinho: ' . $e->getMessage()]);
            }
            break;
        } else {
            http_response_code(500);
            echo json_encode(['message' => 'Endpoint não encontrado']);
        }
        break;


    case 'PUT':
        try {
            if ($requestUri === 'api/v1/users' && isset($_GET['id'])  && $isAuthenticated) {
                $id = (int)$_GET['id'];
                $body = json_decode(file_get_contents('php://input'), true);

                if (!$body || !isset($body['nome'], $body['email'], $body['password'])) {
                    http_response_code(400);
                    echo json_encode(['Error' => 'Incompleto: nome, email e senha são obrigatórios']);
                    exit;
                }

                $userRequest = new UserRequest(
                    nome: $body['nome'],
                    email: $body['email'],
                    password: $body['password'],
                    confirmPassword: $body['confirmPassword'] ?? null
                );

                echo $userController->UpdateUser($id, $userRequest);
                exit;
            } elseif ($requestUri === 'api/v1/users/product' && isset($_GET['id'])  && $isAuthenticated) {
                $id = (int)$_GET['id'];
                $userTokenId = $userToken['user']->id;

                $body = json_decode(file_get_contents('php://input'), true);

                $valor = (float) str_replace(',', '.', $body['valor']);

                $ProductRequest = new ProductRequest(
                    nome: $body['nome'],
                    descricao: $body['descricao'],
                    valor: $valor,
                    quantidadeProduto: $body['quantidade'],
                    quantidadeVendida: null,
                    user_id: $body['user_id'] ?? $userToken['user']->id,
                );

                echo $productController->UpdateProduct($id, $userTokenId, $ProductRequest);
                break;
            } elseif ($requestUri === 'api/v1/users/carrinho/item' && isset($_GET['id'])  && $isAuthenticated) {
                try {
                    $id = (int)$_GET['id'];
                    $userId =  $userToken['user']->id;
                    $body = json_decode(file_get_contents('php://input'), true);

                    if (!isset($body['quantidade'])) {
                        http_response_code(400);
                        echo json_encode(['message' => 'Quantidade é obrigatória']);
                        break;
                    }
                    $quantidade = (int)$body['quantidade'];

                    echo $CarrinhoController->UpdateItemCarrinho($userId, $id, $quantidade);
                } catch (\Throwable $e) {
                    http_response_code(500);
                    return json_encode(['message' => 'Erro no servidor ao Atualizar o item no o carrinho do usuario : ' . $e->getMessage()]);
                }
            }
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Endpoint não encontrado ou ID ausente']);
        }
        break;
    case 'DELETE':
        try {
            if ($requestUri === 'api/v1/users' && $_GET['id']  && $isAuthenticated) {
                $id = (int)$_GET['id'];
                echo $userController->DeleteUser($id);
                break;
            } elseif ($requestUri === 'api/v1/users/product' && $_GET['id']  && $isAuthenticated) {
                $id = (int)$_GET['id'];
                $userTokenId = $userToken['user']->id;
                echo $productController->DeleteProduct($id, $userTokenId);
                break;
            } elseif ($requestUri === 'api/v1/users/carrinho/item'  && $_GET['id'] && $isAuthenticated) {
                try {
                    $id = (int)$_GET['id'];
                    $userId = $userToken['user']->id;
                    echo $CarrinhoController->DeleteProductFromCarrrinho($id, $userId);
                } catch (\Throwable $e) {
                    http_response_code(500);
                    return json_encode(['message' => 'Erro no servidor ao deletar o item no o carrinho do usuario : ' . $e->getMessage()]);
                }
            }
            break;
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Endpoint não encontrado ou ID ausente']);
        }
        break;
    case 'GET':
        if ($requestUri === 'api/v1/users'  && $isAuthenticated) {
            http_response_code(200);
            echo $userController->GetAllUsers();
        } elseif ($requestUri === 'api/v1/user' && $_GET['id']  && $isAuthenticated) {
            try {
                $id = (int)$_GET['id'];
                $idToken = $userToken['user']->id;
                echo $userController->GetUserById($idToken);
            } catch (\Throwable $e) {
                http_response_code(500);
                return json_encode(['message' => 'Erro no servidor ao buscar o usuario por ID: ' . $e->getMessage()]);
            }
        } elseif ($requestUri === 'api/v1/usersEmail' && $_GET['email'] && $isAuthenticated) {
            $body = json_decode(file_get_contents('php://input'), true);
            $email = $_GET['email'];

            if (empty($email)) {
                http_response_code(400);
                echo json_encode(['message' => 'Insira o email para buscar o usuario']);
                break;
            }

            echo $userController->GetUserByEmail($email);
            break;
        }
        //EndPoints Gets Products
        elseif ($requestUri === 'api/v1/users/products/userEmail' && $isAuthenticated) {
            try {
                $body = json_decode(file_get_contents('php://input'), true);
                $email = $_GET['email'];

                if (empty($email)) {
                    http_response_code(400);
                    echo json_encode(['message' => 'Insira o email para buscar o usuario']);
                    break;
                }
                echo $productController->GetProductByCreatedEmail($email);
            } catch (\Throwable $e) {
                http_response_code(500);
                return json_encode(['message' => 'Erro no servidor ao buscar todos os produtos pelo email do criador: ' . $e->getMessage()]);
            }
        } elseif ($requestUri === 'api/v1/users/products' && $isAuthenticated) {
            try {
                echo $productController->GetAllProducts();
            } catch (\Throwable $e) {
                http_response_code(500);
                return json_encode(['message' => 'Erro no servidor ao buscar todos os produtos : ' . $e->getMessage()]);
            }
        } elseif ($requestUri === 'api/v1/users/product' && $_GET['id'] && $isAuthenticated) {
            try {
                $id = (int)$_GET['id'];
                echo $productController->GetProductById($id);
            } catch (\Throwable $e) {
                http_response_code(500);
                return json_encode(['message' => 'Erro no servidor ao buscar todos os produtos : ' . $e->getMessage()]);
            }
        } elseif ($requestUri === 'api/v1/users/carrinho'  && $isAuthenticated ) {
            try {
               
                $userId = $userToken['user']->id;
                
                echo $CarrinhoController->SeeCarrinho($userId);
            } catch (\Throwable $e) {
                http_response_code(500);
                return json_encode(['message' => 'Erro no servidor ao buscar o carrinho do usuario : ' . $e->getMessage()]);
            }
        }
        break;
    default:
        http_response_code(405);
        echo json_encode(['message' => 'Método não permitido']);
        break;
}
