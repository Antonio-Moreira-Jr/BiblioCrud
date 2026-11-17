# BiblioCrud

Cadastro de livros (CRUD de **Livro**, **Autor** e **Assunto**) com relatório de livros agrupados por autor.
Projeto desenvolvido para o teste técnico TJJUD 5.

## Tecnologias

| Camada | Escolha |
|---|---|
| Linguagem | PHP 8.2 (PDO, sem framework), autoload PSR-4 via Composer |
| Banco | MySQL 8.0 (InnoDB, utf8mb4) |
| Sessão e cache | Redis (sessões PHP e cache do painel, via phpredis) |
| Servidor web | Nginx + PHP-FPM |
| Interface | Bootstrap 5.3 + Bootstrap Icons |
| Relatório | View `vw_relatorio_livros` no MySQL + Dompdf (PDF) |
| Testes | PHPUnit 11 |
| CI | GitHub Actions |
| Ambiente | Docker Compose |

## Pré-requisitos

- Docker e Docker Compose
- Git

## Como executar

```bash
# 1. Clonar
git clone https://github.com/Antonio-Moreira-Jr/BiblioCrud.git
cd BiblioCrud

# 2. Variáveis de ambiente (edite as senhas)
cp .env.example .env

# 3. Subir os containers
docker compose up -d --build

# 4. Instalar dependências PHP
docker compose exec php composer install
```

### Banco de dados (scripts aplicados manualmente)

Os scripts ficam em `database/` e **não rodam automaticamente**, para que possam ser lidos e
revisados antes da execução. Aplique-os em ordem:

| Arquivo | O que faz | Obrigatório |
|---|---|---|
| `01_schema.sql` | Cria o banco, tabelas, chaves e índices | Sim |
| `02_views.sql` | Cria a view `vw_relatorio_livros` | Sim |

Conexão: host `127.0.0.1`, porta `3306`, usuário `root` e a senha de `MYSQL_ROOT_PASSWORD` do seu `.env`.
Abra cada arquivo e execute como script (Alt+X no DBeaver), na ordem acima.

### Testes

```bash
docker compose exec php composer test               # tudo
docker compose exec php composer test:unit          # só unitários
docker compose exec php composer test:integration   # Redis real (pulado se indisponível)
```

| Suíte | O que cobre |
|---|---|
| `tests/Unit/CacheRememberTest` | Comportamento do cache (acerto, invalidação, TTL) com um cache em memória |
| `tests/Integration/RedisCacheTest` | `RedisCache` contra um Redis real, incluindo falha de conexão |

### Integração contínua

O workflow `.github/workflows/ci.yml` roda a cada push na `main` e em pull requests:
valida o `composer.json`, faz lint de sintaxe PHP, executa os testes (com um serviço Redis)
e constrói a imagem Docker do PHP.

### Acesso

Abra <http://localhost:8000>.

| Rota | Descrição |
|---|---|
| `/` | Painel com totais e últimos livros |
| `/livros` | CRUD de livros (vários autores e assuntos por livro) |
| `/autores` | CRUD de autores |
| `/assuntos` | CRUD de assuntos |
| `/relatorios` | Relatório de livros por autor (tela e impressão) |
| `/relatorios/pdf` | Mesmo relatório em PDF |

## Modelo de dados

Segue o modelo do enunciado (`Livro`, `Autor`, `Assunto`, `Livro_Autor`, `Livro_Assunto`), com os ajustes abaixo.

| Ajuste | Motivo |
|---|---|
| `Livro.Valor DECIMAL(10,2)` | Exigido pelo teste. `DECIMAL` evita erro de arredondamento com dinheiro |
| `UNIQUE` em `Autor.Nome` e `Assunto.Descricao` | Garante no banco a regra que a aplicação já valida (erro 1062) |
| `ON DELETE RESTRICT` nas FKs de Autor e Assunto | Impede apagar autor/assunto em uso (erro 1451) |
| `ON DELETE CASCADE` nas FKs de Livro | Excluir o livro remove seus vínculos |
| `CHECK` em `Edicao`, `AnoPublicacao` e `Valor` | Rejeita dados inválidos mesmo fora da aplicação (MySQL 8.0.16+) |
| `idx_livro_titulo` | A listagem ordena por título |

### Relatório

A consulta do relatório vem da view `vw_relatorio_livros`: uma linha por par (autor, livro).
Um livro com dois autores aparece sob cada um deles, e os assuntos são concatenados. A aplicação
agrupa as linhas por autor e soma o total em centavos (inteiro) para não ter erro de ponto flutuante.

## Estrutura

```
database/            scripts SQL (schema, view, seed)
docker/              Dockerfile do PHP e configuração do Nginx
public/index.php     ponto de entrada
src/
  biblioteca/        conexão com o banco (PDO singleton)
  cache/             interface, RedisCache, NullCache e fábrica
  controllers/       fluxo das requisições e tratamento de erros
  models/            acesso a dados (SQL com prepared statements)
  views/             templates PHP + Bootstrap
  rotas.php          mapeamento de URLs
tests/               Unit/, Integration/ e Support/
.github/workflows/   pipeline de CI
```

## Decisões técnicas

- **PDO com prepared statements** (`ATTR_EMULATE_PREPARES => false`) e `ERRMODE_EXCEPTION`.
- **Transações** no cadastro, edição e exclusão de livro, porque envolvem três tabelas.
- **Escape de saída** com `htmlspecialchars` em todas as views.
- **Configuração por variáveis de ambiente**; nenhuma senha fica no repositório (`.env.example` é o modelo).
- **Redis em dois papéis:**
  1. *Sessões PHP* (`session.save_handler = redis`): preservam o que foi digitado após erro de validação.
  2. *Cache do painel inicial* (`App\Cache`): totais, distribuição por assunto e últimos livros ficam
     5 minutos no Redis (`dashboard:v1`). Todo cadastro, edição ou exclusão de livro, autor ou assunto
     apaga a chave, então o painel nunca fica desatualizado. Se o Redis cair, a aplicação cai para
     `NullCache` e continua funcionando, apenas sem cache.

## Próximos passos

- [x] Testes automatizados (PHPUnit)
- [x] Pipeline de CI no GitHub Actions
- [x] Cache do painel no Redis