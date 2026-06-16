<?php

require_once 'Personagem.php';

class Necromante extends Personagem
{
    protected function calcularHpMaximo(): int
    {
        return 55 + ($this->level * 16);
    }

    protected function calcularAtaque(): int
    {
        return 20 + ($this->level * 4);
    }

    protected function calcularDefesa(): int
    {
        return 10 + ($this->level * 2);
    }

    public function getClasse(): string
    {
        return 'Necromante';
    }

    public function getAtaques(): array
    {
        return [
            ['nome' => 'Ataque Normal', 'multiplicador' => 1.0],
            ['nome' => 'Dreno Sombrio', 'multiplicador' => 1.5],
            ['nome' => 'Maldivao', 'multiplicador' => 1.8],
        ];
    }
}
