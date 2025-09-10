<?php

namespace Services\Resources;

use Services\Resource;

class ResourceItensCarrinho extends Resource
{

    public static function toArray($item)
    {
        return [
            'id do Produto no carrinho' => $item->getId(),
            'Status' => $item->getStatusItem()->value,
            'nome' => $item->getNomeProduto(),
            'Valor unitario' => $item->getValorUnitario(),
            'Valor Total' => $item->getValorTotal(),
            'Quantidade' => $item->getQuantidade(),
        ];
    }
}
