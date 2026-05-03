<?php

namespace App\Enums;

enum StockType: string
{
    case Entrada = 'entrada';
    case Saida = 'saída';
    case Ajuste = 'ajuste';
}
