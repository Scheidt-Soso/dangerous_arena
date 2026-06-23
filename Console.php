<?php

require_once 'Guerreiro.php';
require_once 'Mago.php';
require_once 'Necromante.php';

class Console
{
    public static function limparTela(): void
    {
        if (PHP_OS_FAMILY === 'Windows') {
            system('cls');
        } else {
            system('clear');
        }
    }

    public static function lerOpcao(string $prompt, array $opcoesValidas): string
    {
        do {
            echo $prompt;
            $entrada = trim(fgets(STDIN));
        } while (!in_array($entrada, $opcoesValidas, true));

        return $entrada;
    }

    public static function menuPrincipal(): void
    {
        self::limparTela();
        echo "========================================\n";
        echo "       Dangerous Arena \n";
        echo "========================================\n";
        echo "\n";
    }

    public static function criarPersonagem(int $numero): Personagem
    {
        echo "--- Criação do Personagem $numero ---\n";
        echo "Escolha a classe:\n";
        echo "1 - Guerreiro\n";
        echo "2 - Mago\n";
        echo "3 - Necromante\n";
        echo "0 - Ver descrição das classes\n";

        $classe = self::lerOpcao("Digite o número do seu personagem: ", ['1', '2', '3', '0']);

        while ($classe === '0') {
            self::mostrarDescricaoClasses();
            echo "\nEscolha a classe:\n";
            echo "1 - Guerreiro\n";
            echo "2 - Mago\n";
            echo "3 - Necromante\n";
            echo "0 - Ver descrição das classes\n";
            $classe = self::lerOpcao("Digite o número do seu personagem: ", ['1', '2', '3', '0']);
        }

        echo "Escolha o nome do seu participante: ";
        $nome = trim(fgets(STDIN));

        if (empty($nome)) {
            $nome = $classe === '1' ? 'Guerreiro' : ($classe === '2' ? 'Mago' : 'Necromante');
        }

        $personagem = $classe === '1'
            ? new Guerreiro($nome)
            : ($classe === '2'
                ? new Mago($nome)
                : new Necromante($nome));

        echo PHP_EOL;
        return $personagem;
    }

    public static function mostrarDescricaoClasses(): void
    {
        self::limparTela();
        $classes = [
            new Guerreiro('Exemplo'),
            new Mago('Exemplo'),
            new Necromante('Exemplo'),
        ];

        foreach ($classes as $p) {
            echo "════════════════════════════════════\n";
            echo "  CLASSE: {$p->getClasse()}\n";
            echo "────────────────────────────────────\n";
            echo "  HP: {$p->getHpMaximo()} | ATK: {$p->getAtaque()} | DEF: {$p->getDefesa()}\n";
            echo "────────────────────────────────────\n";
            echo "  Defesa: {$p->getNomeDefesa()}\n";
            echo "────────────────────────────────────\n";
            echo "  Ataques:\n";
            foreach ($p->getAtaques() as $i => $a) {
                $dano = (int)($p->getAtaque() * $a->getMultiplicador());
                echo "    " . ($i + 1) . ". {$a->getNome()} (dano: ~{$dano})\n";
                if ($a->getDescricao() !== '') {
                    echo "       {$a->getDescricao()}\n";
                }
            }
            $especial = $p->getPoderEspecial();
            echo "────────────────────────────────────\n";
            echo "  Poder Especial: {$especial['nome']}\n";
            echo "  {$especial['descricao']} (Custo: " . Personagem::CUSTO_PODER_ESPECIAL . " mana)\n";

            $tatica = $p->getHabilidadeTatica();
            if ($tatica !== null) {
                echo "────────────────────────────────────\n";
                echo "  Habilidade Tática: {$tatica['nome']}\n";
                echo "  {$tatica['descricao']}\n";
            }
            echo "════════════════════════════════════\n\n";
        }

        self::aguardarEnter("Pressione Enter para voltar...");
        self::limparTela();
    }

    public static function exibirStatus(Personagem $p): void
    {
        $numBarras = (int)($p->getHp() / $p->getHpMaximo() * 20);
        $barra = str_repeat("█", $numBarras);
        $espaco = str_repeat(" ", 20 - $numBarras);
        echo "{$p->getNome()} ({$p->getClasse()}) LV.{$p->getLevel()}\n";
        echo "HP: [{$barra}{$espaco}] {$p->getHp()}/{$p->getHpMaximo()}\n";
        echo "ATK: {$p->getAtaque()} | DEF: {$p->getDefesa()}\n";
        echo "\n";
    }

    public static function aguardarEnter(string $mensagem = "Pressione Enter para continuar..."): void
    {
        echo $mensagem;
        fgets(STDIN);
    }
}
