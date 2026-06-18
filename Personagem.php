<?php

require_once 'Ataque.php';

abstract class Personagem
{
    protected string $nome;
    protected int $hp;
    protected int $hpMaximo;
    protected int $ataque;
    protected int $defesa;
    protected int $level;
    protected int $xp;
    protected float $defesaBuff;
    protected int $mana;
    protected int $manaMaximo;

    public function __construct(string $nome, int $level = 1)
    {
        $this->nome = $nome;
        $this->level = $level;
        $this->hpMaximo = $this->calcularHpMaximo();
        $this->hp = $this->hpMaximo;
        $this->ataque = $this->calcularAtaque();
        $this->defesa = $this->calcularDefesa();
        $this->xp = 0;
        $this->defesaBuff = 1;
        $this->mana = 30;
        $this->manaMaximo = 100;
    }

    abstract protected function calcularHpMaximo(): int;
    abstract protected function calcularAtaque(): int;
    abstract protected function calcularDefesa(): int;
    abstract public function getClasse(): string;

    public function getAtaques(): array
    {
        return [
            new Ataque('Ataque Normal', 1.0),
            new Ataque('Ataque Forte', 1.8),
        ];
    }

    public function getNomeDefesa(): string
    {
        return 'Defesa Padrão';
    }

    protected function getDadoAtaque(): int
    {
        return 6;
    }

    public function atacar(Personagem $alvo, int $indiceAtaque = 0): int
    {
        $ataques = $this->getAtaques();
        $ataque = $ataques[$indiceAtaque] ?? $ataques[0];
        $danoBruto = (int)($this->ataque * $ataque->getMultiplicador()) + random_int(1, $this->getDadoAtaque());
        $danoFinal = $alvo->defender($danoBruto);
        return $danoFinal;
    }

    public function defender(int $dano): int
    {
        $defesaReal = (int)($this->defesa * $this->defesaBuff);
        $this->defesaBuff = 1;
        $danoReduzido = max($dano - $defesaReal, 1);
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
        return "{$this->getClasse()} {$this->nome} | HP: {$this->hp}/{$this->hpMaximo} | MANA: {$this->mana}/{$this->manaMaximo} | ATK: {$this->ataque} | DEF: {$this->defesa} | LV: {$this->level}";
    }

    public function getMana(): int
    {
        return $this->mana;
    }

    public function getManaMaximo(): int
    {
        return $this->manaMaximo;
    }

    protected function getManaGanhoPorAtaque(): int
    {
        return 34;
    }

    public function ganharMana(float $multiplicador): void
    {
        $ganho = (int)(15 * $multiplicador);
        $this->mana = min($this->mana + $ganho, $this->manaMaximo);
    }

    public function poderEspecialDisponivel(): bool
    {
        return $this->mana >= 80;
    }

    public function ativarDefesa(): void
    {
        $this->defesaBuff = 1;
    }

    public function getPoderEspecial(): array
    {
        return [
            'nome' => 'Poder Especial',
            'descricao' => '',
            'multiplicador' => 1.0,
        ];
    }

    public function usarPoderEspecial(Personagem $alvo): int
    {
        $this->mana = 0;
        return 0;
    }

    public function sofrerDanoDireto(int $dano): void
    {
        $this->hp = max($this->hp - $dano, 0);
    }

    public function getXp(): int
    {
        return $this->xp;
    }

    public function gastarXp(int $quantidade): void
    {
        $this->xp = max($this->xp - $quantidade, 0);
    }

    public function recuperarHp(float $fracao): void
    {
        $this->hp = min($this->hp + (int)($this->hpMaximo * $fracao), $this->hpMaximo);
    }

    public function roubarXp(Personagem $alvo, int $quantidade): void
    {
        $roubado = min($quantidade, $alvo->xp);
        $alvo->xp -= $roubado;
        $this->xp += $roubado;
    }
}
