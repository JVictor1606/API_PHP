<?php 

namespace Models\Enums;


enum Status_Carrinho: string{

    case FECHADO = 'FECHADO';
    case ABERTO = 'ABERTO';
    case FINALIZADO = 'FINALIZADO';
    case CANCELADO = 'CANCELADO';

}


?>