<?php 
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



header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers:Authorization, Content-Type, x-xsrf-token, x-csrtoken, X-Requested-With");

require_once 'vendor/autoload.php';


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


?>