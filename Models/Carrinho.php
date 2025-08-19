<?php 

namespace Models;

use Models\Enums\Status_Carrinho;

class Carrinho
{
    private ?int $Id;
    private float $ValorTotal = 0;
    private Status_Carrinho $Status;
    private int $UserId;

    public function __construct(float $valorTotal, Status_Carrinho $status, int $user_id,?int $id = null ) {
        $this->SetUserId($user_id);
        $this->SetValorTotal($valorTotal);
        $this->setStatusCarrinho($status);
        $this->Id = $id;
    }

    public function setId(int $id) 
    {
        $this->Id = $id;
    }

    public function SetUserId(int $user_id)
    {
        $this->UserId = $user_id;
    }
    public function SetValorTotal(float $valorTotal)
    {
        $this->ValorTotal = $valorTotal;
    }

    public function setStatusCarrinho(Status_Carrinho $status) : void
    {
        $this->Status = $status;
    }

    public function GetId()
    {
       return $this->Id;
    }

    public function GetUserId()
    {
       return $this->UserId;
    }

    public function GetStatusCarrinho()
    {
       return $this->Status;
    }

    public function GetValorTotalCarrinho()
    {
       return $this->ValorTotal;
    }
}




?>