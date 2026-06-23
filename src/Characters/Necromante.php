<?php

require_once __DIR__ . '/Personagem.php';
require_once __DIR__ . '/../Effects/EfeitoMaldicao.php';

class Necromante extends Personagem
{
    private int $exercitoSombrioTurnos = 0;

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
        return 'Manto das Sombras';
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
            new Ataque('Toque Sombrio', 1.5, 'Ataque sombrio simples.'),
            new Ataque('Roubo de Vida', 1.2, 'Causa dano e recupera 50% do dano causado.'),
        ];
    }

    public function atacar(Personagem $alvo, int $indiceAtaque = 0): int
    {
        $dano = parent::atacar($alvo, $indiceAtaque);

        if ($indiceAtaque === 1 && $dano > 0) {
            $cura = (int)($dano * 0.5);
            $this->recuperarHp($cura);
            $this->addMensagem("{$this->getNome()} recuperou {$cura} de HP (Roubo de Vida)!");
        }

        return $dano;
    }

    public function defender(int $dano, ?Personagem $atacante = null): int
    {
        if (random_int(1, 100) <= 30) {
            $danoReduzido = (int)($dano * 0.5);
            $this->hp -= $danoReduzido;
            if ($this->hp < 0) {
                $this->hp = 0;
            }
            $this->addMensagem("Manto das Sombras reduziu o dano de {$dano} para {$danoReduzido}!");
            return $danoReduzido;
        }

        return parent::defender($dano, $atacante);
    }

    public function getPoderEspecial(): array
    {
        return [
            'nome' => 'Maldição da Dor',
            'descricao' => 'Amaldiçoa o alvo por 3 turnos (8 de dano por turno, ignora defesa)',
        ];
    }

    public function usarPoderEspecial(Personagem $alvo): int
    {
        parent::usarPoderEspecial($alvo);
        $alvo->adicionarEfeito(new EfeitoMaldicao());
        return 8;
    }

    public function getHabilidadeTatica(): ?array
    {
        return [
            'nome' => 'Exército Sombrio',
            'descricao' => 'Causa 5 de dano imediato e invoca esqueletos por mais 2 turnos (5 de dano automático por turno)',
        ];
    }

    public function executarHabilidadeTatica(Personagem $alvo): string
    {
        $this->exercitoSombrioTurnos = 2;
        $alvo->sofrerDanoDireto(5);
        return "{$this->getNome()} invocou o Exército Sombrio! 5 de dano imediato, esqueletos atacarão por mais 2 turnos.";
    }

    public function getAcoesInicioTurno(Personagem $defensor): array
    {
        $logs = [];
        if ($this->exercitoSombrioTurnos > 0) {
            $defensor->sofrerDanoDireto(5);
            $logs[] = "Esqueleto ataca! 5 de dano causado a {$defensor->getNome()}.";
            $this->exercitoSombrioTurnos--;
            if ($this->exercitoSombrioTurnos <= 0) {
                $logs[] = "Os esqueletos desapareceram.";
            }
        }
        return $logs;
    }

    public function getEfeitosDescricao(): string
    {
        $descricao = parent::getEfeitosDescricao();
        if ($this->exercitoSombrioTurnos > 0) {
            $extra = "Exército Sombrio ({$this->exercitoSombrioTurnos} turnos)";
            $descricao = $descricao === 'Nenhum' ? $extra : $descricao . ', ' . $extra;
        }
        return $descricao;
    }
}
