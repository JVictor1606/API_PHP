<?php

namespace Models;

use Models\Enums\Status_item;

class CarrinhoItems
{

    private ?int $Id;
    private string $NomeProduto;
    private int $Quantidade;
    private float $ValorTotal;
    private float $ValorUnitario;
    private Status_item $StatusItem;
    private int $ProdutoId;
    private string $CarrinhoId;

    public function __construct(
        string $nomeProduto,
        int $quantidade,
        float $valorTotal,
        float $valorUnitario,
        Status_item $statusItem,
        int $produtoId,
        int $carrinhoId,
        ?int $id = null
    ) {
        $this->setNomeProduto($nomeProduto);
        $this->setQuantidade($quantidade);
        $this->setValorTotal($valorTotal);
        $this->setValorUnitario($valorUnitario);
        $this->setStatusItem($statusItem);
        $this->setProdutoId($produtoId);
        $this->setCarrinhoId($carrinhoId);
        $this->setId($id);
    }

    
    public function setId(?int $id): void
    {
        $this->Id = $id;
    }

    public function setNomeProduto(string $nomeProduto): void
    {
        $this->NomeProduto = $nomeProduto;
    }

    public function setQuantidade(int $quantidade): void
    {
        $this->Quantidade = $quantidade;
    }

    public function setValorTotal(float $valorTotal): void
    {
        $this->ValorTotal = $valorTotal;
    }
     public function setValorUnitario(float $valorUnitario): void
    {
        $this->ValorUnitario = $valorUnitario;
    }

    public function setStatusItem(Status_item $statusItem): void
    {
        $this->StatusItem = $statusItem;
    }

    public function setProdutoId(int $produtoId): void
    {
        $this->ProdutoId = $produtoId;
    }

    public function setCarrinhoId(int $carrinhoId): void
    {
        $this->CarrinhoId = $carrinhoId;
    }

    
    public function getId(): ?int
    {
        return $this->Id;
    }

    public function getNomeProduto(): string
    {
        return $this->NomeProduto;
    }

    public function getQuantidade(): int
    {
        return $this->Quantidade;
    }

    public function getValorTotal(): float
    {
        return $this->ValorTotal;
    }

    public function getValorUnitario(): float
    {
        return $this->ValorUnitario;
    }

    public function getStatusItem(): Status_item
    {
        return $this->StatusItem;
    }

    public function getProdutoId(): int
    {
        return $this->ProdutoId;
    }

    public function getCarrinhoId(): int
    {
        return $this->CarrinhoId;
    }
}
