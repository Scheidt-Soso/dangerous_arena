<?php

require_once 'Guerreiro.php';
require_once 'Mago.php';
require_once 'Arena.php';

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
    } else {
        echo "1 - Guerreiro\n";
        echo "2 - Mago\n";
    }

    $classe = lerOpcao("Digite o número da classe: ", ['1', '2']);

    echo "Digite o nome do personagem: ";
    $nome = trim(fgets(STDIN));

    if (empty($nome)) {
        $nome = $classe === '1' ? 'Guerreiro' : 'Mago';
    }

    $personagem = $classe === '1'
        ? new Guerreiro($nome)
        : new Mago($nome);

    echo PHP_EOL;
    return $personagem;
}

function exibirStatus(Personagem $p): void
{
    $barra = str_repeat("█", (int)($p->getHp() / $p->getHpMaximo() * 20));
    $espaco = str_repeat(" ", 20 - strlen($barra));
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
$arena->iniciarBatalha();

echo "\nPressione Enter para encerrar...";
fgets(STDIN);
