<?php

require_once 'Guerreiro.php';
require_once 'Mago.php';
require_once 'Arena.php';
require_once 'Necromante.php';

function limparTela(): void
{
    if (PHP_OS_FAMILY === 'Windows') {
        system('cls');
    } else {
        system('clear');
    }
}

function lerOpcao(string $prompt, array $opcoesValidas): string
{
    do {
        echo $prompt;
        $entrada = trim(fgets(STDIN));
    } while (!in_array($entrada, $opcoesValidas, true));

    return $entrada;
}

function menuPrincipal(): void
{
    limparTela();
    echo "========================================\n";
    echo "       Dangerous Arena \n";
    echo "========================================\n";
    echo "\n";
}

function criarPersonagem(int $numero): Personagem
{
    echo "--- Criação do Personagem $numero ---\n";
    echo "Escolha a classe:\n";
    if ($numero === 1) {
        echo "1 - Guerreiro\n";
        echo "2 - Mago\n";
        echo "3 - Necromante\n";
    } elseif($numero === 2){
        echo "1 - Guerreiro\n";
        echo "2 - Mago\n";
        echo "3 - Necromante\n";
    } else {
        echo "1 - Guerreiro\n";
        echo "2 - Mago\n";
        echo "3 - Necromante\n";
    }

    $classe = lerOpcao("Digite o número da classe: ", ['1', '2', '3']);

    echo "Digite o nome do personagem: ";
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

function exibirStatus(Personagem $p): void
{
    $numBarras = (int)($p->getHp() / $p->getHpMaximo() * 20);
    $barra = str_repeat("█", $numBarras);
    $espaco = str_repeat(" ", 20 - $numBarras);
    echo "{$p->getNome()} ({$p->getClasse()}) LV.{$p->getLevel()}\n";
    echo "HP: [{$barra}{$espaco}] {$p->getHp()}/{$p->getHpMaximo()}\n";
    echo "ATK: {$p->getAtaque()} | DEF: {$p->getDefesa()}\n";
    echo "\n";
}

// --- Execução principal ---
menuPrincipal();

$p1 = criarPersonagem(1);

echo "Personagem 1 criado com sucesso!\n";
exibirStatus($p1);

echo "Pressione Enter para criar o segundo personagem...";
fgets(STDIN);

$p2 = criarPersonagem(2);

echo "Personagem 2 criado com sucesso!\n";
exibirStatus($p2);

echo "========================================\n";
echo "Pressione Enter para iniciar a batalha!";
fgets(STDIN);

limparTela();
 $arena = new Arena($p1, $p2);
$arena->iniciarTorneio();

echo "\nPressione Enter para encerrar...";
fgets(STDIN);
