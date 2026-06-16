<?php

require_once 'Personagem.php';

class Arena
{
    private Personagem $p1;
    private Personagem $p2;
    private int $turno;
    private array $logs;

    public function __construct(Personagem $p1, Personagem $p2)
    {
        $this->p1 = $p1;
        $this->p2 = $p2;
        $this->turno = 1;
        $this->logs = [];
    }

    public function iniciarTorneio(): void
    {
        $vitoriasP1 = 0;
        $vitoriasP2 = 0;
        $rodada = 1;

        $this->log("========================================");
        $this->log("  DANGEROUS ARENA - MELHOR DE 3");
        $this->log("========================================");
        $this->log("");
        $this->log("{$this->p1} vs {$this->p2}");
        $this->log("");

        while ($vitoriasP1 < 2 && $vitoriasP2 < 2) {
            $this->log("=========== RODADA $rodada ===========");
            $this->log("");

            $this->p1->gastarXp(10);
            $this->p2->gastarXp(10);
            $this->log("{$this->p1->getNome()} XP: {$this->p1->getXp()}");
            $this->log("{$this->p2->getNome()} XP: {$this->p2->getXp()}");
            $this->log("");

            if ($rodada > 1) {
                $this->p1->recuperarHp(0.5);
                $this->p2->recuperarHp(0.5);
                $this->log("HP recuperado parcialmente (50%)!");
                $this->log("");
            }

            $vencedor = $this->iniciarBatalha();

            if ($vencedor === 1) {
                $vitoriasP1++;
            } else {
                $vitoriasP2++;
            }

            $this->log("");
            $this->log("Placar: {$this->p1->getNome()} {$vitoriasP1} x {$vitoriasP2} {$this->p2->getNome()}");
            $this->log("");

            if ($vitoriasP1 < 2 && $vitoriasP2 < 2) {
                echo "Pressione Enter para a proxima rodada...";
                fgets(STDIN);
                limparTela();
            }

            $rodada++;
        }

        $campeao = $vitoriasP1 > $vitoriasP2 ? $this->p1 : $this->p2;
        $this->animacaoVitoria($campeao);
    }

    private function iniciarBatalha(): int
    {
        $this->turno = 1;
        $this->logs = [];

        while ($this->p1->estaVivo() && $this->p2->estaVivo()) {
            $this->executarTurno();
            $this->turno++;
        }

        $vencedor = $this->p1->estaVivo() ? 1 : 2;
        $this->finalizarBatalha($vencedor);
        return $vencedor;
    }

    private function executarTurno(): void
    {
        limparTela();

        echo "--- Turno {$this->turno} ---\n\n";
        echo $this->barraVida($this->p1) . "\n";
        echo $this->barraVida($this->p2) . "\n\n";

        $this->menuAtaque($this->p1, $this->p2);
        if (!$this->p2->estaVivo()) {
            $this->log("{$this->p2->getNome()} foi derrotado!");
            return;
        }

        echo "\nPressione Enter para passar o controle...";
        fgets(STDIN);
        limparTela();

        echo "--- Turno {$this->turno} ---\n\n";
        echo $this->barraVida($this->p1) . "\n";
        echo $this->barraVida($this->p2) . "\n\n";

        $this->menuAtaque($this->p2, $this->p1);
        if (!$this->p1->estaVivo()) {
            $this->log("{$this->p1->getNome()} foi derrotado!");
            return;
        }

        echo "\nPressione Enter para passar o controle...";
        fgets(STDIN);
    }

    private function menuAtaque(Personagem $atacante, Personagem $defensor): void
    {
        $ataques = $atacante->getAtaques();

        echo "--- Vez de {$atacante->getNome()} ({$atacante->getClasse()}) ---\n";
        echo "HP: {$atacante->getHp()}/{$atacante->getHpMaximo()} | ATK: {$atacante->getAtaque()} | DEF: {$atacante->getDefesa()} | XP: {$atacante->getXp()}\n";
        echo "\nEscolha o ataque:\n";

        foreach ($ataques as $i => $ataque) {
            $danoEstimado = (int)($atacante->getAtaque() * $ataque['multiplicador']);
            echo ($i + 1) . " - {$ataque['nome']} (dano: ~{$danoEstimado})\n";
        }

        $opcoes = ['1', '2', '3'];
        $escolha = (int) lerOpcao("Digite o numero: ", $opcoes) - 1;

        $dano = $atacante->atacar($defensor, $escolha);
        $nomeAtaque = $ataques[$escolha]['nome'];

        if ($dano > 0) {
            $this->log("{$atacante->getNome()} usou {$nomeAtaque} e causou {$dano} de dano em {$defensor->getNome()}!");
        }

        if ($escolha > 0 && $atacante->getXp() < 50) {
            $xpRoubado = 10;
            $atacante->roubarXp($defensor, $xpRoubado);
            $this->log("{$atacante->getNome()} roubou {$xpRoubado} XP de {$defensor->getNome()}!");
        }

        echo "\n";
    }

    private function finalizarBatalha(int $vencedor): void
    {
        $this->log(str_repeat("=", 40));
        $this->log("=== FIM DE BATALHA ===");

        if ($vencedor === 1) {
            $this->log("VENCEDOR: {$this->p1->getNome()} ({$this->p1->getClasse()})");
            $this->log("HP restante: {$this->p1->getHp()}/{$this->p1->getHpMaximo()}");
        } else {
            $this->log("VENCEDOR: {$this->p2->getNome()} ({$this->p2->getClasse()})");
            $this->log("HP restante: {$this->p2->getHp()}/{$this->p2->getHpMaximo()}");
        }

        $this->log("Total de turnos: " . ($this->turno - 1));
        $this->log(str_repeat("=", 40));
    }

    private function barraVida(Personagem $p): string
    {
        $numBarras = (int)($p->getHp() / $p->getHpMaximo() * 20);
        $barra = str_repeat("█", $numBarras);
        $espaco = str_repeat(" ", 20 - $numBarras);
        return "{$p->getNome()}: [{$barra}{$espaco}] {$p->getHp()}/{$p->getHpMaximo()}";
    }

    private function animacaoVitoria(Personagem $campeao): void
    {
        limparTela();
        echo "\n\n\n";

        $linhas = [
            "    ╔══════════════════════════════════════╗",
            "    ║                                      ║",
            "    ║         VITORIA DO TORNEIO!          ║",
            "    ║                                      ║",
            "    ║    {$campeao->getNome()} ({$campeao->getClasse()})     ║",
            "    ║                                      ║",
            "    ║         EH O CAMPEAO!                ║",
            "    ║                                      ║",
            "    ╚══════════════════════════════════════╝",
        ];

        foreach ($linhas as $linha) {
            echo $linha . "\n";
            usleep(150000);
        }

        usleep(500000);
        echo "\n\n";
    }

    private function log(string $mensagem): void
    {
        $this->logs[] = $mensagem;
        echo $mensagem . PHP_EOL;
    }

    public function getLog(): array
    {
        return $this->logs ?? [];
    }
}
