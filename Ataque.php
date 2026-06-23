<?php

class Ataque
{
    private string $nome;
    private string $descricao;
    private float $multiplicador;

    public function __construct(string $nome, float $multiplicador, string $descricao = '')
    {
        $this->nome = $nome;
        $this->multiplicador = $multiplicador;
        $this->descricao = $descricao;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function getMultiplicador(): float
    {
        return $this->multiplicador;
    }
}
