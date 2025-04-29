# MP Denúncia

Sistema de denúncias desenvolvido com Laravel e conexão com a OpenAI.

## Requisitos

- Docker
- Docker Compose
- Git

## Instalação

1. Clone o repositório:
```bash
git clone [URL_DO_REPOSITÓRIO]
cd mpdenuncia
```

2. Copie o arquivo de ambiente:
```bash
cp .env.example .env
```

3. Inicie os containers Docker:
```bash
docker-compose up -d
```

## Configuração do Banco de Dados

1. Execute as migrations:
```bash
docker exec -it mpdenuncia-postgres-1 bash
```

2. Rode as migrations:
```bash
php artisan migrate
```

## Acessando a Aplicação

A aplicação estará disponível em:
- Frontend: http://localhost:8002/login

## Estrutura do Projeto

- `app/` - Contém a lógica principal da aplicação
- `config/` - Arquivos de configuração
- `database/` - Migrations e seeds
- `public/` - Arquivos públicos
- `resources/` - Views e assets
- `routes/` - Definição de rotas
- `storage/` - Arquivos de armazenamento
- `tests/` - Testes automatizados

## Como Usar

1. Cadastre um processo no sistema
2. Faça upload do PDF do processo
3. Digite o número CNJ do processo
4. Interaja com a IA para analisar o processo

A IA irá ajudar na análise do processo, fornecendo insights e informações relevantes sobre o caso.
