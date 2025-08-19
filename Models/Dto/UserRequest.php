<?php

namespace   Models\Dto;

readonly class UserRequest
{
    public function __construct(
        public ?string $nome,
        public string $email,
        public ?string $password,
        public ?string $confirmPassword,
    ) {}
}
