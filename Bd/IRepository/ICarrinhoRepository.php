<?php

namespace Bd\IRepository;

use Models\Carrinho;
use Models\CarrinhoItems;

interface ICarrinhoRepository
{
    public function CreateCarrinho(Carrinho $carrinho): Carrinho;
    public function UpdateCarrinho(Carrinho $carrinho): Carrinho;
    public function DeleteCarrinho(int $id): bool;
    public function AddProductInsideCarrinho(CarrinhoItems $item): CarrinhoItems;
    public function GetCarrinhoByUserId(int $id) : Carrinho;
    public function GetCarrinhoById(int $id) : Carrinho;
    public function GetItemsCarrinho(int $carrinhoId) : array;
}
