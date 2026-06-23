<?php

abstract class Efeito
{
    protected string $nome;
    protected string $descricao;
    protected int $turnosRestantes;

    public function __construct(string $nome, string $descricao, int $duracao)
    {
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->turnosRestantes = $duracao;
    }

    abstract public function aplicar(Personagem $alvo): void;
    abstract public function remover(Personagem $alvo): void;

    public function processarTurno(Personagem $alvo): ?string
    {
        return null;
    }

    public function decrementar(): void
    {
        $this->turnosRestantes--;
    }

    public function expirado(): bool
    {
        return $this->turnosRestantes <= 0;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function getTurnosRestantes(): int
    {
        return $this->turnosRestantes;
    }
}
