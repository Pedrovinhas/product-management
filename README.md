## Visão geral

Stack utilizada:

- [Laravel 13](https://laravel.com/)
- [Vue 3](https://vuejs.org/) + [Vite](https://vite.dev/) + [TypeScript](https://www.typescriptlang.org/)
- [PostgreSQL 17](https://www.postgresql.org/)
- [Docker](https://www.docker.com/) + [Docker Compose](https://docs.docker.com/compose/)
- [PHPUnit](https://phpunit.de/), [PHPStan](https://phpstan.org/) e [Laravel Pint](https://laravel.com/docs/13.x/pint)

---

## Pré-requisitos

- Docker + Docker Compose
- make (opcional)

---

## Variáveis de ambiente

```bash
cp backend/.env.example backend/.env
```

Ajuste as variáveis DB_* e demais configurações conforme necessário.

---

## Rodando em desenvolvimento

### Com Make

```bash
make up          # sobe todos os serviços
make migrate     # executa migrations + seeders
make key         # gera APP_KEY (apenas na primeira vez)
```

Acesse:

| Serviço  | URL                   |
|----------|-----------------------|
| API  | http://localhost      |
| Frontend | http://localhost:5173 |

---

### Sem Make

```bash
# Subir os serviços
docker compose -f docker-compose.dev.yml up -d

# Gerar chave da aplicação (apenas na primeira vez)
docker exec product_app php artisan key:generate

# Executar migrations e seeders
docker exec product_app php artisan migrate --seed
```

---

## Comandos úteis

| Make      | Equivalente Docker                                     |
|-----------|---------------------------------------------------------|
| make down | docker compose -f docker-compose.dev.yml down          |
| make bash | docker exec -it product_app sh                         |
| make logs | docker compose -f docker-compose.dev.yml logs -f app   |
| make test | docker exec product_app ./vendor/bin/phpunit           |
| make pint | docker exec product_app ./vendor/bin/pint              |
| make stan | docker exec product_app ./vendor/bin/phpstan analyse   |
