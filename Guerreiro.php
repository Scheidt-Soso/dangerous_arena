<?php

require_once 'Personagem.php';

class Guerreiro extends Personagem
{
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

    public function getNomeDefesa(): string
    {
        return 'Postura de Ferro';
    }

    public function ativarDefesa(): void
    {
        $this->defesaBuff = 1.5;
    }

    protected function getDadoAtaque(): int
    {
        return 8;
    }

    public function getAtaques(): array
    {
        return [
            new Ataque('Machadada', 0.8),
            new Ataque('Espada de fogo', 1.7),
        ];
    }

    public function getPoderEspecial(): array
    {
        return [
            'nome' => 'Golpe de Fúria',
            'descricao' => 'Ataque devastador que causa o dobro de dano',
            'multiplicador' => 3.5,
        ];
    }

    public function usarPoderEspecial(Personagem $alvo): int
    {
        $this->poderEspecialUsado = true;
        $this->defesaBuff = 0.5;
        $danoBruto = (int)($this->ataque * $this->getPoderEspecial()['multiplicador']) + random_int(1, $this->getDadoAtaque());
        return $alvo->defender($danoBruto);
    }
}
