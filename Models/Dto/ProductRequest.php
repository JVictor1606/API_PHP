<?php 

namespace   Models\Dto;

readonly class ProductRequest
{
    public function __construct(
        public string $nome,
        public string $descricao,
        public float $valor,
        public int $quantidadeProduto,
        public ?int $quantidadeVendida,
        public ?int $user_id
    ) {}
}
?>