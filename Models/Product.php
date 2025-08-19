<?php 

namespace Models;

class Product
{
    private ?int $Id;
    private string $Nome_produto;
    private string $Descricao;
    private float $Valor;
    private int $Quantidade_produto;
    private int $Quantidade_vendida = 0;

    private ?int $user_id;


    public function __construct($nome, $descricao,$valor, $quantidadeProdutos, $user_id,?int $id = null) {
        $this->setNomeProduto($nome);
        $this->setDescricao($descricao);
        $this->setValor($valor);
        $this->setQuantidadeProduto($quantidadeProdutos);
        $this->setUserId( $user_id);
        $this->setId($id);
    }

    public function getId(): ?int {
        return $this->Id;
    }

    public function setId(int $id): void {
        $this->Id = $id;
    }

    public function getNomeProduto(): string {
        return $this->Nome_produto;
    }

    public function setNomeProduto(string $nome): void {
        $this->Nome_produto = $nome;
    }

    public function getDescricao(): string {
        return $this->Descricao;
    }

    public function setDescricao(string $descricao): void {
        $this->Descricao = $descricao;
    }

    public function getValor(): float {
        return $this->Valor;
    }

    public function setValor(float $valor): void {
        $this->Valor = $valor;
    }

    public function getQuantidadeProduto(): int {
        return $this->Quantidade_produto;
    }

    public function setQuantidadeProduto(int $quantidade): void {
        $this->Quantidade_produto = $quantidade;
    }

    public function getQuantidadeVendida(): int {
        return $this->Quantidade_vendida;
    }

    public function setQuantidadeVendida(int $quantidade): void {
        $this->Quantidade_vendida = $quantidade;
    }

    public function getUserId(): int {
        return $this->user_id;
    }

    public function setUserId(int $user_id): void {
        $this->user_id = $user_id;
    }
}

?>