<?php

require_once 'Personagem.php';
require_once 'EfeitoParalisia.php';

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
        return 'Barreira Arcana';
    }

    public function ativarDefesa(): void
    {
        $this->defesaBuff = 2.0;
    }

    protected function getDadoAtaque(): int
    {
        return 10;
    }

    public function getAtaques(): array
    {
        return [
            new Ataque('Raio Arcano', 1.5, 'Ataque mágico simples.'),
            new Ataque('Chuva de Meteoros', 1.0, 'Invoca três meteoros consecutivos.'),
        ];
    }

    public function atacar(Personagem $alvo, int $indiceAtaque = 0): int
    {
        if ($indiceAtaque === 1) {
            $danoTotal = 0;
            for ($i = 1; $i <= 3; $i++) {
                $danoBruto = (int)($this->getAtaque() * 1.0) + random_int(1, 8);
                $danoParcial = $alvo->defender($danoBruto, $this);
                $danoTotal += $danoParcial;
                echo "  Meteoro {$i}: {$danoParcial} de dano.\n";
            }
            return $danoTotal;
        }

        return parent::atacar($alvo, $indiceAtaque);
    }

    public function defender(int $dano, ?Personagem $atacante = null): int
    {
        $defesaReal = (int)($this->getDefesa() * $this->defesaBuff);
        $this->defesaBuff = 1;
        $danoReduzido = max((int)($dano - $defesaReal), 0);
        $danoReduzido = (int)($danoReduzido * 0.8);
        $this->hp -= $danoReduzido;
        if ($this->hp < 0) {
            $this->hp = 0;
        }
        return $danoReduzido;
    }

    public function getPoderEspecial(): array
    {
        return [
            'nome' => 'Prisão de Gelo',
            'descricao' => 'Dano mágico moderado e congela o alvo por 1 turno',
        ];
    }

    public function usarPoderEspecial(Personagem $alvo): int
    {
        parent::usarPoderEspecial($alvo);
        $danoFinal = (int)($this->getAtaque() * 1.8) + random_int(1, $this->getDadoAtaque());
        $danoFinal = max($danoFinal, 1);
        $alvo->sofrerDanoDireto($danoFinal);
        $alvo->adicionarEfeito(new EfeitoParalisia());
        return $danoFinal;
    }

    public function getHabilidadeTatica(): ?array
    {
        return [
            'nome' => 'Drenar Mana',
            'descricao' => 'Remove 30 de mana do alvo e adiciona 30 ao próprio mago',
        ];
    }

    public function executarHabilidadeTatica(Personagem $alvo): string
    {
        $manaRoubada = min(30, $alvo->getMana());
        $alvo->gastarMana($manaRoubada);
        $this->mana = min($this->mana + $manaRoubada, $this->manaMaximo);
        return "{$this->getNome()} drenou {$manaRoubada} de mana de {$alvo->getNome()}!";
    }
}
