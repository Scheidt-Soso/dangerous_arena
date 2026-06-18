<?php

require_once 'Console.php';

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

    public function iniciarLuta(): void
    {
        Console::limparTela();
        $this->log("    ╔══════════════════════════════════════╗");
        $this->log("    ║         ⚔  ARENA DE BATALHA  ⚔       ║");
        $this->log("    ╠══════════════════════════════════════╣");
        $this->log("    ║                                      ║");
        $this->log("    ║    " . str_pad($this->p1->getNome() . " (" . $this->p1->getClasse() . ")", 36) . "║");
        $this->log("    ║              VS                      ║");
        $this->log("    ║    " . str_pad($this->p2->getNome() . " (" . $this->p2->getClasse() . ")", 36) . "║");
        $this->log("    ║                                      ║");
        $this->log("    ╠══════════════════════════════════════╣");
        $this->log("    ║    " . str_pad("Level: " . $this->p1->getLevel(), 36) . "║");
        $this->log("    ║    " . str_pad("Level: " . $this->p2->getLevel(), 36) . "║");
        $this->log("    ╚══════════════════════════════════════╝");
        $this->log("");

        $vencedor = $this->iniciarBatalha(999);

        $campeao = $vencedor === 1 ? $this->p1 : $this->p2;
        $this->animacaoVitoria($campeao);
    }

    private function iniciarBatalha(int $maxTurnos): int
    {
        $this->turno = 1;
        $this->logs = [];

        while ($this->p1->estaVivo() && $this->p2->estaVivo() && $this->turno <= $maxTurnos) {
            $this->executarTurno();
            $this->turno++;
        }

        $vencedor = $this->p1->estaVivo() ? 1 : 2;
        $this->finalizarBatalha($vencedor);
        return $vencedor;
    }

    private function executarTurno(): void
    {
        $this->executarTurnoJogador($this->p1, $this->p2);
        if (!$this->p2->estaVivo()) {
            $this->log("{$this->p2->getNome()} foi derrotado!");
            return;
        }

        Console::aguardarEnter("\nPressione Enter para passar o controle...");
        Console::limparTela();

        $this->executarTurnoJogador($this->p2, $this->p1);
        if (!$this->p1->estaVivo()) {
            $this->log("{$this->p1->getNome()} foi derrotado!");
            return;
        }

        Console::aguardarEnter("\nPressione Enter para passar o controle...");
    }

    private function executarTurnoJogador(Personagem $atacante, Personagem $defensor): void
    {
        Console::limparTela();

        echo "--- Turno {$this->turno} ---\n\n";
        echo $this->barraVida($this->p1) . "\n";
        echo $this->barraVida($this->p2) . "\n\n";

        echo "--- Vez de {$atacante->getNome()} ({$atacante->getClasse()}) ---\n";
        echo "HP: {$atacante->getHp()}/{$atacante->getHpMaximo()} | MANA: {$atacante->getMana()}/{$atacante->getManaMaximo()} | ATK: {$atacante->getAtaque()} | DEF: {$atacante->getDefesa()} ({$atacante->getNomeDefesa()})\n";
        echo "\n";

        $this->menuAcao($atacante, $defensor);
    }

    private function menuAcao(Personagem $atacante, Personagem $defensor): void
    {
        echo "Escolha sua ação:\n";
        echo "1 - Atacar\n";
        echo "2 - Defender\n";

        $opcoes = ['1', '2'];
        if ($atacante->poderEspecialDisponivel()) {
            $poder = $atacante->getPoderEspecial();
            echo "3 - Poder Especial: {$poder['nome']} ({$poder['descricao']})\n";
            $opcoes[] = '3';
        }

        $escolha = Console::lerOpcao("Digite o numero: ", $opcoes);

        if ($escolha === '1') {
            $this->menuAtaque($atacante, $defensor);
        } elseif ($escolha === '2') {
            $atacante->ativarDefesa();
            $atacante->ganharMana(0.1);
            $this->log("{$atacante->getNome()} assumiu postura defensiva! Defesa aumentada!");
            echo "\n";
        } elseif ($escolha === '3') {
            $this->executarPoderEspecial($atacante, $defensor);
        }
    }

    private function menuAtaque(Personagem $atacante, Personagem $defensor): void
    {
        $ataques = $atacante->getAtaques();
        $qtd = count($ataques);

        echo "\nEscolha o ataque:\n";
        foreach ($ataques as $i => $ataque) {
            $danoEstimado = (int)($atacante->getAtaque() * $ataque->getMultiplicador());
            echo ($i + 1) . " - {$ataque->getNome()} (dano: ~{$danoEstimado})\n";
        }

        $opcoes = range(1, $qtd);
        $opcoesStr = array_map('strval', $opcoes);
        $escolha = (int) Console::lerOpcao("Digite o numero: ", $opcoesStr) - 1;

        $dano = $atacante->atacar($defensor, $escolha);
        $nomeAtaque = $ataques[$escolha]->getNome();

        if ($dano > 0) {
            $this->log("{$atacante->getNome()} usou {$nomeAtaque} e causou {$dano} de dano em {$defensor->getNome()}!");
        }

        $atacante->ganharMana($ataques[$escolha]->getMultiplicador());
        $this->log("{$atacante->getNome()} ganhou mana! ({$atacante->getMana()}/{$atacante->getManaMaximo()})");

        echo "\n";
    }

    private function executarPoderEspecial(Personagem $atacante, Personagem $defensor): void
    {
        $poder = $atacante->getPoderEspecial();
        $dano = $atacante->usarPoderEspecial($defensor);
        $this->log("{$atacante->getNome()} usou {$poder['nome']}!");
        if ($dano > 0) {
            $this->log("Causou {$dano} de dano em {$defensor->getNome()}!");
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

        $numMana = (int)($p->getMana() / $p->getManaMaximo() * 10);
        $barraMana = str_repeat("▌", $numMana);
        $espacoMana = str_repeat(" ", 10 - $numMana);

        return "{$p->getNome()}: [{$barra}{$espaco}] {$p->getHp()}/{$p->getHpMaximo()}  MP:[{$barraMana}{$espacoMana}] {$p->getMana()}%";
    }

    private function animacaoVitoria(Personagem $campeao): void
    {
        Console::limparTela();
        echo "\n\n\n";

        $texto = $campeao->getNome() . ' (' . $campeao->getClasse() . ')';
        $textoCentralizado = str_pad(substr($texto, 0, 28), 28, ' ', STR_PAD_BOTH);

        $linhas = [
            '  ______________________________',
            ' / \                             \. ,',
            '|   |                            |.,',
            ' \_ |                            |.,',
            '    |                            |.,',
            '    |                            |.,',
            '    |                            |.',
            '    |                            |.',
            '    |' . $textoCentralizado . '|',
            '    |                            |.',
            '    |                            |.',
            '    |                            |.',
            '    |                            |.',
            '    |                            |.',
            '    |                            |.',
            '    |   _________________________|___',
            '    |  /                            /.',
            '    \_/____________________________/.',
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
