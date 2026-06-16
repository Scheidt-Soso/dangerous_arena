<?php

abstract class Personagem
{
    protected string $nome;
    protected int $hp;
    protected int $hpMaximo;
    protected int $ataque;
    protected int $defesa;
    protected int $level;

    public function __construct(string $nome, int $level = 1)
    {
        $this->nome = $nome;
        $this->level = $level;
        $this->hpMaximo = $this->calcularHpMaximo();
        $this->hp = $this->hpMaximo;
        $this->ataque = $this->calcularAtaque();
        $this->defesa = $this->calcularDefesa();
    }

    abstract protected function calcularHpMaximo(): int;
    abstract protected function calcularAtaque(): int;
    abstract protected function calcularDefesa(): int;
    abstract public function getClasse(): string;

    public function getAtaques(): array
    {
        return [
            ['nome' => 'Ataque Normal', 'multiplicador' => 1.0],
            ['nome' => 'Ataque Forte', 'multiplicador' => 1.8],
        ];
    }

    public function atacar(Personagem $alvo, int $indiceAtaque = 0): int
    {
        $ataques = $this->getAtaques();
        $ataque = $ataques[$indiceAtaque] ?? $ataques[0];
        $danoBruto = (int)($this->ataque * $ataque['multiplicador']) + random_int(1, 6);
        $danoFinal = $alvo->defender($danoBruto);
        return $danoFinal;
    }

    public function defender(int $dano): int
    {
        $danoReduzido = max($dano - $this->defesa, 1);
        $this->hp -= $danoReduzido;
        if ($this->hp < 0) {
            $this->hp = 0;
        }
        return $danoReduzido;
    }

    public function estaVivo(): bool
    {
        return $this->hp > 0;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getHp(): int
    {
        return $this->hp;
    }

    public function getHpMaximo(): int
    {
        return $this->hpMaximo;
    }

    public function getAtaque(): int
    {
        return $this->ataque;
    }

    public function getDefesa(): int
    {
        return $this->defesa;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function __toString(): string
    {
        return "{$this->getClasse()} {$this->nome} | HP: {$this->hp}/{$this->hpMaximo} | ATK: {$this->ataque} | DEF: {$this->defesa} | LV: {$this->level}";
    }
}
