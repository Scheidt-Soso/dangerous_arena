# Dangerous Arena ⚔

Combate por turnos entre personagens com classes, habilidades e efeitos temporários — implementado em PHP Orientado a Objetos.

## Execução

```bash
php index.php
```

---

## Sumário

- [Visão Geral](#visão-geral)
- [Classes](#classes)
  - [Guerreiro](#guerreiro)
  - [Mago](#mago)
  - [Necromante](#necromante)
- [Mecânicas](#mecânicas)
  - [Atributos](#atributos)
  - [Mana](#mana)
  - [Defesa](#defesa)
  - [Efeitos Temporários](#efeitos-temporários)
- [Arquitetura](#arquitetura)

---

## Visão Geral

Cada jogador seleciona um personagem entre três classes disponíveis. Os turnos alternam entre os jogadores: ataque, defesa, poder especial ou habilidade tática. O combate termina quando a vida de um personagem chega a zero.

```
Guerreiro (Postura Defensiva)  VS  Mago (Barreira Arcana)
HP: [████████████████████] 100/100      HP: [████████████████████] 100/100
```

---

## Classes

### Guerreiro

Perfil **tanque**: alta resistência, buffs e contra-ataque.

| Atributo | Cálculo | Nível 1 |
|----------|---------|:-------:|
| HP | 100 | 100 |
| Ataque | 15 + (level × 3) | 18 |
| Defesa | 20 + (level × 4) | 24 |

| Ação | Nome | Efeito |
|------|------|--------|
| Ataque 1 | Golpe Pesado (1.8×) | Dano físico alto |
| Ataque 2 | Impacto Sísmico (1.2×) | Reduz defesa do alvo em 5 por 2 turnos |
| Defesa | Postura Defensiva | `defesaBuff = 2.0` até receber ataque |
| Poder Especial | Grito de Guerra (80 mana) | ATK +10, DEF +5 por 3 turnos |
| Passiva | Vingança | Reflete 50% do dano recebido ao atacante |

---

### Mago

Perfil **dano mágico**: alto ataque, controle e manipulação de mana.

| Atributo | Cálculo | Nível 1 |
|----------|---------|:-------:|
| HP | 100 | 100 |
| Ataque | 25 + (level × 5) | 30 |
| Defesa | 8 + (level × 2) | 10 |

| Ação | Nome | Efeito |
|------|------|--------|
| Ataque 1 | Raio Arcano (1.5×) | Ataque mágico simples |
| Ataque 2 | Chuva de Meteoros (1.0×) | 3 meteoros independentes; cada um passa pela defesa |
| Defesa | Barreira Arcana | `defesaBuff = 2.0` + redução extra de 20% |
| Poder Especial | Prisão de Gelo (80 mana) | Dano moderado + congela o alvo por 1 turno |
| Tática | Drenar Mana | Remove 30 de mana do alvo e recupera 30 |

---

### Necromante

Perfil **desgaste**: dano contínuo, sustentação e invocação.

| Atributo | Cálculo | Nível 1 |
|----------|---------|:-------:|
| HP | 100 | 100 |
| Ataque | 20 + (level × 4) | 24 |
| Defesa | 10 + (level × 2) | 12 |

| Ação | Nome | Efeito |
|------|------|--------|
| Ataque 1 | Toque Sombrio (1.5×) | Ataque sombrio simples |
| Ataque 2 | Roubo de Vida (1.2×) | Recupera 50% do dano causado como HP |
| Defesa | Manto das Sombras | `defesaBuff = 1.5` — 30% de chance de reduzir dano à metade |
| Poder Especial | Maldição da Dor (80 mana) | 8 de dano por turno durante 3 turnos (ignora defesa) |
| Tática | Exército Sombrio | Esqueletos causam 5 de dano automático por turno durante 3 turnos |

---

## Mecânicas

### Atributos

- **Vida**: começa no máximo. Nunca ultrapassa o valor inicial. Ao chegar a zero, o personagem é derrotado.
- **Ataque**: base do dano causado. Pode ser modificado temporariamente por buffs.
- **Defesa**: reduz o dano recebido (`dano - defesa`). O resultado nunca é negativo.

### Mana

- Máximo de 100. Cada personagem começa com 30.
- **Regeneração**: ao atacar (15 × multiplicador do ataque) e ao defender (10%).
- **Poder Especial**: custa 80 de mana. Só pode ser usado se houver mana suficiente.

### Defesa

Cada classe tem uma postura defensiva própria. Ao escolher **Defender**, o personagem ativa um bônus de defesa (`defesaBuff`) que se mantém até receber o próximo ataque.

### Efeitos Temporários

Os efeitos são aplicados por ataques e habilidades. Todos possuem duração definida, decremento automático por turno e remoção ao expirar.

| Efeito | Duração | Origem |
|--------|:-------:|--------|
| Defesa Reduzida (–5) | 2 turnos | Impacto Sísmico |
| Grito de Guerra (ATK +10, DEF +5) | 3 turnos | Grito de Guerra |
| Congelado (perde o turno) | 1 turno | Prisão de Gelo |
| Maldição da Dor (8 de dano/turno) | 3 turnos | Maldição da Dor |
| Exército Sombrio (5 de dano/turno) | 3 turnos | Exército Sombrio |

---

## Arquitetura

```
projeto/
├── index.php                  # Ponto de entrada
├── Personagem.php             # Classe abstrata base
├── Guerreiro.php              # Classe concreta
├── Mago.php                   # Classe concreta
├── Necromante.php             # Classe concreta
├── Ataque.php                 # Objeto de ataque (nome, descrição, multiplicador)
├── Arena.php                  # Gerenciamento do combate (turnos, menu, logs)
├── Console.php                # Interface com o terminal
├── ManaInsuficienteException.php  # Exceção personalizada
├── Efeito.php                 # Classe abstrata para efeitos
├── EfeitoDebuffDefesa.php
├── EfeitoBuffGritoGuerra.php
├── EfeitoParalisia.php
├── EfeitoMaldicao.php
└── README.md
```

Princípios aplicados:

- **Classe abstrata**: `Personagem` define o contrato para todas as classes.
- **Herança**: `Guerreiro`, `Mago` e `Necromante` estendem `Personagem`.
- **Encapsulamento**: atributos protegidos com acesso por métodos públicos.
- **Polimorfismo**: `atacar()`, `defender()`, `usarPoderEspecial()`, `executarHabilidadeTatica()` e `getAcoesInicioTurno()` são sobrescritos em cada classe — o loop principal em `Arena` invoca sem usar `if`/`switch` por classe.
- **Exceção personalizada**: `ManaInsuficienteException` para erro de mana.
- **Constantes**: `CUSTO_PODER_ESPECIAL` e `REGENERACAO_MANA_BASE`.
- **Efeitos**: sistema genérico com duração, decremento e remoção automática.
