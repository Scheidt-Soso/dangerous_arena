<?php

require_once 'Efeito.php';

class EfeitoParalisia extends Efeito
{
    public function __construct()
    {
        parent::__construct('Congelado', 'Impede o alvo de agir por 1 turno', 1);
    }

    public function aplicar(Personagem $alvo): void
    {
        $alvo->setTurnosParalisado(1);
    }

    public function remover(Personagem $alvo): void
    {
        $alvo->setTurnosParalisado(0);
    }

    public function processarTurno(Personagem $alvo): ?string
    {
        return "{$alvo->getNome()} está congelado e não pode agir!";
    }
}
