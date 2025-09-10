<?php

namespace Middleware;

use Controllers\AuthController;
use Services\Response;


class AuthMiddleware
{

    private AuthController $_authController;

    public function __construct(AuthController $authController)
    {
        $this->_authController = $authController;
    }

    public function Authenticated(callable $next)
    {
        $authorization = isset($_SERVER['HTTP_AUTHORIZATION']) ??   '';
        $token = str_replace('Bearer ', '', $authorization);

        if (empty($token) || !$this->_authController->validaToken($token)) {
            (new Response()->json(['error' => 'Não autenticado'], Response::HTTP_UNAUTHORIZED));
            exit;
        }

        return $next();
    }
}
