<?php 

use Controllers\UserController;
use Controllers\CarrinhoController;
use Controllers\AuthController;
use Controllers\ProductController;

$url = rtrim($_SERVER['SCRIPT_URI'], '/');


return [
    
    'GET' => [
        'api/v1/users' => [UserController::class, 'GetAllUsers'],
        'api/v1/user/{id}' => [UserController::class, 'GetUserById'],
        'api/v1/usersEmail/{email}' => [UserController::class, 'GetUserByEmail'],
        'api/v1/users/products' => [ProductController::class, 'GetAllProducts'],
        'api/v1/users/product/{id}' => [ProductController::class, 'GetProductById'],
        'api/v1/users/products/userEmail/{email}' => [ProductController::class,'GetProductByCreatedEmail'],
        'api/v1/users/carrinho' => [CarrinhoController::class,'SeeCarrinho']

    ],

    'POST' => [
        'api/v1/users' => [UserController::class, 'CreateUser'],
        'api/v1/users/Auth' => [AuthController::class, 'AuthUser'],
        'api/v1/user/product' => [ProductController::class, 'CreateProduct'],
        'api/v1/users/carrinho' => [CarrinhoController::class, 'AddProductInCarrinho'],
    ],

    'PUT' => [
        'api/v1/users/{id}' => [UserController::class, 'UpdateUser'],
        'api/v1/users/product/{id}' => [ProductController::class, 'UpdateProduct'],
        'api/v1/users/carrinho/item/{id}' => [CarrinhoController::class, 'UpdateItemCarrinho'],

    ],

    'DELETE' => [
        'api/v1/users/{id}' => [UserController::class, 'DeleteUser'],
        'api/v1/users/product/{id}' => [ProductController::class, 'DeleteProduct'],
        'api/v1/users/carrinho/item/{id}' => [ProductController::class, 'DeleteProductFromCarrrinho'],

    ]
]

?>