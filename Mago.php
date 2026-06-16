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

    public function getAtaques(): array
    {
        return [
            ['nome' => 'Ataque Normal', 'multiplicador' => 1.0],
            ['nome' => 'Bola de Fogo', 'multiplicador' => 2.5],
            ['nome' => 'Raio', 'multiplicador' => 1.8],
        ];
    }

    public function atacar(Personagem $alvo, int $indiceAtaque = 0): int
    {
        $ataques = $this->getAtaques();
        $ataque = $ataques[$indiceAtaque] ?? $ataques[0];
        $danoBruto = (int)($this->ataque * $ataque['multiplicador']) + random_int(1, 10);
        $danoFinal = $alvo->defender($danoBruto);
        return $danoFinal;
    }
}
