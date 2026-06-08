<?php

require_once 'Personagem.php';

class Guerreiro extends Personagem
{
    private bool $posturaDefensiva = false;

    protected function calcularHpMaximo(): int
    {
        return 80 + ($this->level * 20);
    }

    protected function calcularAtaque(): int
    {
        return 15 + ($this->level * 3);
    }

    protected function calcularDefesa(): int
    {
        return 20 + ($this->level * 4);
    }

    public function getClasse(): string
    {
        return 'Guerreiro';
    }

    public function ativarPosturaDefensiva(): void
    {
        $this->posturaDefensiva = true;
    }

    public function defender(int $dano): int
    {
        $defesaReal = $this->defesa;
        if ($this->posturaDefensiva) {
            $defesaReal = (int)($defesaReal * 1.5);
            $this->posturaDefensiva = false;
        }
        $danoReduzido = max($dano - $defesaReal, 1);
        $this->hp -= $danoReduzido;
        if ($this->hp < 0) {
            $this->hp = 0;
        }
        return $danoReduzido;
    }

    public function estaEmPosturaDefensiva(): bool
    {
        return $this->posturaDefensiva;
    }
}
