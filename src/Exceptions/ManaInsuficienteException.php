<?php

class ManaInsuficienteException extends Exception
{
    public function __construct(string $mensagem = "Mana insuficiente para usar o poder especial!")
    {
        parent::__construct($mensagem);
    }
}
