<?php

require_once 'Ataque.php';
require_once 'ManaInsuficienteException.php';
require_once 'Efeito.php';

abstract class Personagem
{
    public const CUSTO_PODER_ESPECIAL = 80;
    public const REGENERACAO_MANA_BASE = 15;

    protected string $nome;
    protected int $hp;
    protected int $hpMaximo;
    protected int $ataque;
    protected int $defesa;
    protected int $level;
    protected float $defesaBuff;
    protected int $mana;
    protected int $manaMaximo;
    protected int $turnosParalisado = 0;
    protected array $efeitos = [];

    public function __construct(string $nome, int $level = 1)
    {
        $this->nome = $nome;
        $this->level = $level;
        $this->hpMaximo = $this->calcularHpMaximo();
        $this->hp = $this->hpMaximo;
        $this->ataque = $this->calcularAtaque();
        $this->defesa = $this->calcularDefesa();
        $this->defesaBuff = 1;
        $this->mana = 30;
        $this->manaMaximo = 100;
    }

    abstract protected function calcularHpMaximo(): int;
    abstract protected function calcularAtaque(): int;
    abstract protected function calcularDefesa(): int;
    abstract public function getClasse(): string;

    public function getAtaques(): array
    {
        return [
            new Ataque('Ataque Normal', 1.0),
            new Ataque('Ataque Forte', 1.8),
        ];
    }

    public function getNomeDefesa(): string
    {
        return 'Defesa Padrão';
    }

    protected function getDadoAtaque(): int
    {
        return 6;
    }

    public function atacar(Personagem $alvo, int $indiceAtaque = 0): int
    {
        $ataques = $this->getAtaques();
        $ataque = $ataques[$indiceAtaque] ?? $ataques[0];
        $danoBruto = (int)($this->getAtaque() * $ataque->getMultiplicador()) + random_int(1, $this->getDadoAtaque());
        $danoFinal = $alvo->defender($danoBruto, $this);
        return $danoFinal;
    }

    public function defender(int $dano, ?Personagem $atacante = null): int
    {
        $defesaReal = (int)($this->getDefesa() * $this->defesaBuff);
        $this->defesaBuff = 1;
        $danoReduzido = max($dano - $defesaReal, 0);
        $this->hp -= $danoReduzido;
        if ($this->hp < 0) {
            $this->hp = 0;
        }
        return $danoReduzido;
    }

    public function estaVivo(): bool
    {
        return $this->hp > 0;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getHp(): int
    {
        return $this->hp;
    }

    public function getHpMaximo(): int
    {
        return $this->hpMaximo;
    }

    public function getAtaque(): int
    {
        return $this->ataque;
    }

    public function getDefesa(): int
    {
        return $this->defesa;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function __toString(): string
    {
        $efeitosStr = '';
        if (!empty($this->efeitos)) {
            $nomes = array_map(fn(Efeito $e) => $e->getNome(), $this->efeitos);
            $efeitosStr = ' | EFEITOS: ' . implode(', ', $nomes);
        }
        return "{$this->getClasse()} {$this->nome} | HP: {$this->hp}/{$this->hpMaximo} | MANA: {$this->mana}/{$this->manaMaximo} | ATK: {$this->getAtaque()} | DEF: {$this->getDefesa()} | LV: {$this->level}{$efeitosStr}";
    }

    public function getMana(): int
    {
        return $this->mana;
    }

    public function getManaMaximo(): int
    {
        return $this->manaMaximo;
    }

    public function ganharMana(float $multiplicador): void
    {
        $ganho = (int)(self::REGENERACAO_MANA_BASE * $multiplicador);
        $this->mana = min($this->mana + $ganho, $this->manaMaximo);
    }

    public function gastarMana(int $quantidade): void
    {
        $this->mana = max($this->mana - $quantidade, 0);
    }

    public function poderEspecialDisponivel(): bool
    {
        return $this->mana >= self::CUSTO_PODER_ESPECIAL;
    }

    abstract public function ativarDefesa(): void;

    public function getPoderEspecial(): array
    {
        return [
            'nome' => 'Poder Especial',
            'descricao' => '',
        ];
    }

    public function usarPoderEspecial(Personagem $alvo): int
    {
        if ($this->mana < static::CUSTO_PODER_ESPECIAL) {
            throw new ManaInsuficienteException();
        }
        $this->mana -= static::CUSTO_PODER_ESPECIAL;
        return 0;
    }

    public function getHabilidadeTatica(): ?array
    {
        return null;
    }

    public function executarHabilidadeTatica(Personagem $alvo): string
    {
        return '';
    }

    public function sofrerDanoDireto(int $dano): void
    {
        $this->hp = max($this->hp - $dano, 0);
    }

    public function recuperarHp(int $quantidade): void
    {
        $this->hp = min($this->hp + $quantidade, $this->hpMaximo);
    }

    public function setTurnosParalisado(int $t): void
    {
        $this->turnosParalisado = $t;
    }

    public function estaParalisado(): bool
    {
        return $this->turnosParalisado > 0;
    }

    public function modificarAtaque(int $delta): void
    {
        $this->ataque += $delta;
    }

    public function modificarDefesa(int $delta): void
    {
        $this->defesa = max($this->defesa + $delta, 0);
    }

    public function adicionarEfeito(Efeito $e): void
    {
        foreach ($this->efeitos as $existente) {
            if ($existente->getNome() === $e->getNome()) {
                return;
            }
        }
        $this->efeitos[] = $e;
        $e->aplicar($this);
    }

    public function removerEfeito(string $nome): void
    {
        foreach ($this->efeitos as $i => $e) {
            if ($e->getNome() === $nome) {
                $e->remover($this);
                unset($this->efeitos[$i]);
                $this->efeitos = array_values($this->efeitos);
                return;
            }
        }
    }

    public function processarEfeitos(): array
    {
        $logs = [];
        foreach ($this->efeitos as $i => $efeito) {
            $log = $efeito->processarTurno($this);
            if ($log !== null) {
                $logs[] = $log;
            }
            $efeito->decrementar();
            if ($efeito->expirado()) {
                $efeito->remover($this);
                $logs[] = "Efeito '{$efeito->getNome()}' em {$this->getNome()} expirou.";
                unset($this->efeitos[$i]);
            }
        }
        $this->efeitos = array_values($this->efeitos);
        return $logs;
    }

    public function getEfeitos(): array
    {
        return $this->efeitos;
    }

    public function getEfeitosDescricao(): string
    {
        if (empty($this->efeitos)) {
            return 'Nenhum';
        }
        $partes = [];
        foreach ($this->efeitos as $e) {
            $partes[] = $e->getNome() . ' (' . $e->getTurnosRestantes() . ' turnos)';
        }
        return implode(', ', $partes);
    }

    public function getAcoesInicioTurno(Personagem $defensor): array
    {
        return [];
    }
}
