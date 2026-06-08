<?php

require_once 'Personagem.php';

class Arena
{
    private Personagem $personagem1;
    private Personagem $personagem2;
    private int $turno;
    private array $log;

    public function __construct(Personagem $p1, Personagem $p2)
    {
        $this->personagem1 = $p1;
        $this->personagem2 = $p2;
        $this->turno = 1;
        $this->log = [];
    }

    public function iniciarBatalha(): void
    {
        $this->log("=== ARENA DE COMBATE ===");
        $this->log("");
        $this->log("Desafiante 1: {$this->personagem1}");
        $this->log("Desafiante 2: {$this->personagem2}");
        $this->log(str_repeat("-", 40));

        while ($this->personagem1->estaVivo() && $this->personagem2->estaVivo()) {
            $this->executarTurno();
            $this->turno++;
        }

        $this->finalizarBatalha();
    }

    private function executarTurno(): void
    {
        $this->log("--- Turno {$this->turno} ---");

        $acao1 = $this->decidirAcao($this->personagem1);
        $acao2 = $this->decidirAcao($this->personagem2);

        if ($acao1 === 'especial' && $this->personagem1 instanceof Guerreiro) {
            $this->personagem1->ativarPosturaDefensiva();
            $this->log("{$this->personagem1->getNome()} assumiu postura defensiva! Defesa aumentada em 50% neste turno.");
            $acao1 = 'defender';
        }

        $dano = $this->personagem1->atacar($this->personagem2);
        if ($dano > 0) {
            $this->log("{$this->personagem1->getNome()} atacou {$this->personagem2->getNome()} causando {$dano} de dano!");
        }

        if (!$this->personagem2->estaVivo()) {
            $this->log("{$this->personagem2->getNome()} foi derrotado!");
            return;
        }

        if ($acao2 === 'especial' && $this->personagem2 instanceof Guerreiro) {
            $this->personagem2->ativarPosturaDefensiva();
            $this->log("{$this->personagem2->getNome()} assumiu postura defensiva! Defesa aumentada em 50% neste turno.");
            $acao2 = 'defender';
        }

        $dano2 = $this->personagem2->atacar($this->personagem1);
        if ($dano2 > 0) {
            $this->log("{$this->personagem2->getNome()} atacou {$this->personagem1->getNome()} causando {$dano2} de dano!");
        }

        if (!$this->personagem1->estaVivo()) {
            $this->log("{$this->personagem1->getNome()} foi derrotado!");
            return;
        }

        $this->log("");
    }

    private function decidirAcao(Personagem $p): string
    {
        if ($p instanceof Guerreiro && $p->getHp() < $p->getHpMaximo() * 0.3) {
            return 'especial';
        }
        return 'atacar';
    }

    private function finalizarBatalha(): void
    {
        $this->log(str_repeat("=", 40));
        $this->log("=== FIM DE BATALHA ===");

        if ($this->personagem1->estaVivo()) {
            $this->log("VENCEDOR: {$this->personagem1->getNome()} ({$this->personagem1->getClasse()})");
            $this->log("HP restante: {$this->personagem1->getHp()}/{$this->personagem1->getHpMaximo()}");
        } elseif ($this->personagem2->estaVivo()) {
            $this->log("VENCEDOR: {$this->personagem2->getNome()} ({$this->personagem2->getClasse()})");
            $this->log("HP restante: {$this->personagem2->getHp()}/{$this->personagem2->getHpMaximo()}");
        } else {
            $this->log("EMPATE! Ambos foram derrotados!");
        }

        $this->log("Total de turnos: " . ($this->turno - 1));
        $this->log(str_repeat("=", 40));
    }

    private function log(string $mensagem): void
    {
        $this->log[] = $mensagem;
        echo $mensagem . PHP_EOL;
    }

    public function getLog(): array
    {
        return $this->log;
    }
}
