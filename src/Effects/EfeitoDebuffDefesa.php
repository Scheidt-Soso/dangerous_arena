<?php

require_once __DIR__ . '/Efeito.php';

class EfeitoDebuffDefesa extends Efeito
{
    public function __construct()
    {
        parent::__construct('Defesa Reduzida', 'Reduz a defesa em 5 pontos', 2);
    }

    public function aplicar(Personagem $alvo): void
    {
        $alvo->modificarDefesa(-5);
    }

    public function remover(Personagem $alvo): void
    {
        $alvo->modificarDefesa(5);
    }
}
