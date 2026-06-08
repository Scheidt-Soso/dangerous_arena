<?php

require_once 'Personagem.php';

class Mago extends Personagem
{
    protected function calcularHpMaximo(): int
    {
        return 50 + ($this->level * 15);
    }

    protected function calcularAtaque(): int
    {
        return 25 + ($this->level * 5);
    }

    protected function calcularDefesa(): int
    {
        return 8 + ($this->level * 2);
    }

    public function getClasse(): string
    {
        return 'Mago';
    }

    public function atacar(Personagem $alvo): int
    {
        $danoBruto = $this->ataque + random_int(1, 10);
        $danoFinal = $alvo->defender($danoBruto);
        return $danoFinal;
    }
}
