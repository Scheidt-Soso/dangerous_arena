<?php

require_once __DIR__ . '/src/Interface/Console.php';
require_once __DIR__ . '/src/Combat/Arena.php';

Console::menuPrincipal();

$p1 = Console::criarPersonagem(1);

echo "Personagem 1 criado com sucesso!\n";
Console::exibirStatus($p1);

Console::aguardarEnter("Pressione Enter para criar o segundo personagem...");

$p2 = Console::criarPersonagem(2);

echo "Personagem 2 criado com sucesso!\n";
Console::exibirStatus($p2);

echo "========================================\n";
Console::aguardarEnter("Pressione Enter para iniciar a batalha!");

Console::limparTela();
$arena = new Arena($p1, $p2);
$arena->iniciarLuta();

echo "\nPressione Enter para encerrar...";
fgets(STDIN);
