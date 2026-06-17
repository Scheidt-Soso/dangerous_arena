<?php

class Ataque
{
    private string $nome;
    private float $multiplicador;

    public function __construct(string $nome, float $multiplicador)
    {
        $this->nome = $nome;
        $this->multiplicador = $multiplicador;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getMultiplicador(): float
    {
        return $this->multiplicador;
    }
}
