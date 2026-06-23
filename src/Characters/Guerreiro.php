<?php

require_once __DIR__ . '/Personagem.php';
require_once __DIR__ . '/../Effects/EfeitoDebuffDefesa.php';
require_once __DIR__ . '/../Effects/EfeitoBuffGritoGuerra.php';

class Guerreiro extends Personagem
{
    protected function calcularHpMaximo(): int
    {
        return 100;
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
        return 'Postura Defensiva';
    }

    public function ativarDefesa(): void
    {
        $this->defesaBuff = 2.0;
    }

    protected function getDadoAtaque(): int
    {
        return 8;
    }

    public function getAtaques(): array
    {
        return [
            new Ataque('Golpe Pesado', 1.8, 'Ataque físico forte.'),
            new Ataque('Impacto Sísmico', 1.2, 'Reduz a defesa do alvo em 5 por 2 turnos.'),
        ];
    }

    public function atacar(Personagem $alvo, int $indiceAtaque = 0): int
    {
        $dano = parent::atacar($alvo, $indiceAtaque);

        if ($indiceAtaque === 1) {
            $alvo->adicionarEfeito(new EfeitoDebuffDefesa());
        }

        return $dano;
    }

    public function defender(int $dano, ?Personagem $atacante = null): int
    {
        $danoFinal = parent::defender($dano, $atacante);

        if ($atacante !== null && $danoFinal > 0) {
            $danoRefletido = (int)($danoFinal * 0.5);
            if ($danoRefletido > 0) {
                $atacante->sofrerDanoDireto($danoRefletido);
                $this->addMensagem("⚔ {$this->getNome()} refletiu {$danoRefletido} de dano com Vingança!");
            }
        }

        return $danoFinal;
    }

    public function getPoderEspecial(): array
    {
        return [
            'nome' => 'Grito de Guerra',
            'descricao' => 'Aumenta ataque em 10 e defesa em 5 por 3 turnos',
        ];
    }

    public function usarPoderEspecial(Personagem $alvo): int
    {
        parent::usarPoderEspecial($alvo);
        $this->adicionarEfeito(new EfeitoBuffGritoGuerra());
        return 0;
    }
}
