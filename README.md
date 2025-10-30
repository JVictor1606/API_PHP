# MVC_inscricao

API REST simples em PHP (estilo MVC) para gestão de usuários, produtos e carrinho.

## Visão geral
Projeto estruturado com controllers, modelos, repositórios e serviços. A aplicação expõe endpoints REST através de [index.php](index.php) e [indexx.php](indexx.php). A documentação OpenAPI pode ser gerada/visualizada via [swagger.php](swagger.php) + [swagger-ui/index.html](swagger-ui/index.html).

Principais controllers e símbolos:
- [`Controllers\UserController`](Controllers/UserController.php) — endpoints CRUD de usuários.
- [`Controllers\AuthController`](Controllers/AuthController.php) — autenticação e JWT.
- [`Controllers\ProductController`](Controllers/ProductController.php) — CRUD de produtos.
- [`Controllers\CarrinhoController`](Controllers/CarrinhoController.php) — operações do carrinho.

Principais repositórios:
- [`Bd\Repository_base`](Bd/Repository_base.php) — conexão com o banco.
- [`Bd\Repository\UserRepository`](Bd/Repository/UserRepository.php)
- [`Bd\Repository\ProductRepository`](Bd/Repository/ProductRepository.php)
- [`Bd\Repository\CarrinhoRepository`](Bd/Repository/CarrinhoRepository.php)

Models e DTOs:
- [Models/User.php](Models/User.php) — entidade usuário
- [Models/Product.php](Models/Product.php) — entidade produto
- [Models/Carrinho.php](Models/Carrinho.php) — entidade carrinho
- [Models/CarrinhoItems.php](Models/CarrinhoItems.php) — item do carrinho
- [Models/Dto/UserRequest.php](Models/Dto/UserRequest.php)
- [Models/Dto/ProductRequest.php](Models/Dto/ProductRequest.php)

Serviços:
- [Services/Response.php](Services/Response.php)
- [Services/Resource.php](Services/Resource.php)
- [Services/Resources/ResourceUser.php](Services/Resources/ResourceUser.php)
- [Services/Resources/ResourceProduct.php](Services/Resources/ResourceProduct.php)
- [Services/Resources/ResourceItensCarrinho.php](Services/Resources/ResourceItensCarrinho.php)

Roteamento (alternativo):
- [Core/Route.php](Core/Route.php)
- [Routes/route.php](Routes/route.php)

Middleware:
- [Middleware/AuthMiddleware.php](Middleware/AuthMiddleware.php)

Arquivos auxiliares:
- [.htaccess](.htaccess) — regra de rewrite para roteamento via index.php
- [swagger.php](swagger.php) — gera YAML do OpenAPI usando annotations nas controllers
- [swagger-ui/](swagger-ui/) — UI estática do Swagger

---

## Requisitos (ambiente de desenvolvimento)
- PHP 8.1+ (recomenda-se PHP 8+)
- MySQL / MariaDB
- Composer
- Servidor web com mod_rewrite (ex.: Apache) ou use `php -S` (com limitações no rewrite)
- Extensões PHP: pdo_mysql, json, mbstring (geralmente habilitadas)

---

## Dependências (definidas em composer.json)
As dependências usadas neste projeto estão listadas em [composer.json](composer.json):

- zircote/swagger-php (^5.0@dev) — gerar anotações OpenAPI (usado por [`swagger.php`](swagger.php))
- doctrine/annotations (2.0.x-dev) — suporte a annotations
- firebase/php-jwt (dev-main) — JWT (usado por [`Controllers\AuthController`](Controllers/AuthController.php))
- vlucas/phpdotenv (^5.6@dev) — carregar variáveis de ambiente (.env)
- larapack/dd (dev-master) — helper de debug (opcional)

Instalação recomendada (na raiz do projeto):
1. Instale o Composer (se ainda não tiver): https://getcomposer.org/download/
2. No diretório do projeto execute:
   ```sh
   composer install