<?php

namespace Services\Resources;

use Services\Resource;

class ResourceProduct extends Resource
{

    public static function toArray($product)
    {
        return [
            'Id do dono do produto' => $product->getUserId(),
            'id do Produto' => $product->getId(),
            'nome' => $product->getNomeProduto(),
            'descricao' => $product->getDescricao(),
            'Valor' => $product->getValor(),
            'Produtos em estoque' => $product->getQuantidadeProduto()
        ];
    }
}
