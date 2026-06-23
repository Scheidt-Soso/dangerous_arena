<?php

require_once __DIR__ . '/Efeito.php';

class EfeitoMaldicao extends Efeito
{
    public function __construct()
    {
        parent::__construct('Maldição da Dor', 'Causa 8 de dano por turno, ignora defesa', 3);
    }

    public function aplicar(Personagem $alvo): void
    {
    }

    public function remover(Personagem $alvo): void
    {
    }

    public function processarTurno(Personagem $alvo): ?string
    {
        $alvo->sofrerDanoDireto(8);
        $restam = $this->turnosRestantes - 1;
        return "Maldição da Dor causa 8 de dano em {$alvo->getNome()}. Restam {$restam} turno(s).";
    }
}
