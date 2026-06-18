<?php

require_once 'Personagem.php';

class Mago extends Personagem
{
    protected function calcularHpMaximo(): int
    {
        return 100;
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

    public function getNomeDefesa(): string
    {
        return 'Escudo Arcano';
    }

    public function ativarDefesa(): void
    {
        $this->defesaBuff = 2;
    }

    protected function getDadoAtaque(): int
    {
        return 10;
    }

    public function getAtaques(): array
    {
        return [
            new Ataque('Ataque Normal', 1.0),
            new Ataque('Bola de Fogo', 2.5),
            new Ataque('Raio', 1.8),
        ];
    }

    public function getPoderEspecial(): array
    {
        return [
            'nome' => 'Tempestade Arcana',
            'descricao' => 'Dano mágico que ignora completamente a defesa',
            'multiplicador' => 2.0,
        ];
    }

    public function usarPoderEspecial(Personagem $alvo): int
    {
        $this->mana = 0;
        $danoFinal = (int)($this->ataque * $this->getPoderEspecial()['multiplicador']) + random_int(1, $this->getDadoAtaque());
        $danoFinal = max($danoFinal, 1);
        $alvo->sofrerDanoDireto($danoFinal);
        return $danoFinal;
    }

    public function defender(int $dano): int
    {
        $defesaReal = $this->defesa * $this->defesaBuff;
        $this->defesaBuff = 1;
        $danoReduzido = max((int)($dano - $defesaReal), 1);
        $danoReduzido = (int)($danoReduzido * 0.8);
        $this->hp -= $danoReduzido;
        if ($this->hp < 0) {
            $this->hp = 0;
        }
        return $danoReduzido;
    }
}
