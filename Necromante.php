<?php

require_once 'Personagem.php';

class Necromante extends Personagem
{
    protected function calcularHpMaximo(): int
    {
        return 100;
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

    public function getNomeDefesa(): string
    {
        return 'Sombra Protetora';
    }

    public function ativarDefesa(): void
    {
        $this->defesaBuff = 1.5;
    }

    protected function getDadoAtaque(): int
    {
        return 6;
    }

    public function getAtaques(): array
    {
        return [
            new Ataque('Ataque Normal', 1.0),
            new Ataque('Dreno Sombrio', 1.5),
            new Ataque('Maldivão', 1.8),
        ];
    }

    public function getPoderEspecial(): array
    {
        return [
            'nome' => 'Ritual Sombrio',
            'descricao' => 'Recupera 50% do HP máximo e causa dano sombrio',
            'multiplicador' => 2.0,
        ];
    }

    public function usarPoderEspecial(Personagem $alvo): int
    {
        $this->mana = 0;
        $cura = (int)($this->hpMaximo * 0.5);
        $this->hp = min($this->hp + $cura, $this->hpMaximo);
        $danoBruto = (int)($this->ataque * $this->getPoderEspecial()['multiplicador']) + random_int(1, $this->getDadoAtaque());
        return $alvo->defender($danoBruto);
    }

    public function defender(int $dano): int
    {
        if (random_int(1, 100) <= 30) {
            return 0;
        }

        $defesaReal = $this->defesa * $this->defesaBuff;
        $this->defesaBuff = 1;
        $danoReduzido = max((int)($dano - $defesaReal), 1);
        $this->hp -= $danoReduzido;
        if ($this->hp < 0) {
            $this->hp = 0;
        }
        return $danoReduzido;
    }
}
