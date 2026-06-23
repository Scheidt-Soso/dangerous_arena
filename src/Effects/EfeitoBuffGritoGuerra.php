<?php

require_once __DIR__ . '/Efeito.php';

class EfeitoBuffGritoGuerra extends Efeito
{
    public function __construct()
    {
        parent::__construct('Grito de Guerra', 'Aumenta ataque em 10 e defesa em 5', 3);
    }

    public function aplicar(Personagem $alvo): void
    {
        $alvo->modificarAtaque(10);
        $alvo->modificarDefesa(5);
    }

    public function remover(Personagem $alvo): void
    {
        $alvo->modificarAtaque(-10);
        $alvo->modificarDefesa(-5);
    }
}
