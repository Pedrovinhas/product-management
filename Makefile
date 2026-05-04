APP  := product_app
DC   := docker compose -f docker-compose.dev.yml

.DEFAULT_GOAL := help

.PHONY: help up down build rebuild push restart bash \
        migrate migrate-fresh migrate-rollback \
	test pint stan install key logs \
	frontend-install frontend-dev frontend-build frontend-logs

help:
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

# Docker

up: 
	$(DC) up -d

down: 
	$(DC) down

build: ## Rebuild images (no cache)
	$(DC) build --no-cache

rebuild: build up

push: ## Build production images and push to Docker Hub (requires DOCKERHUB_USERNAME in .env)
	docker build --target runner -t $${DOCKERHUB_USERNAME}/product-app:latest .
	docker build --target web    -t $${DOCKERHUB_USERNAME}/product-nginx:latest .
	docker push $${DOCKERHUB_USERNAME}/product-app:latest
	docker push $${DOCKERHUB_USERNAME}/product-nginx:latest

restart: down up

# Backend 

bash:
	docker exec -it $(APP) sh

migrate:
	docker exec $(APP) php artisan migrate --seed

migrate-fresh:
	docker exec $(APP) php artisan migrate:fresh --seed

migrate-rollback:
	docker exec $(APP) php artisan migrate:rollback

test: 
	docker exec -t \
		-e DB_CONNECTION=sqlite \
		-e DB_DATABASE=":memory:" \
		-e DB_HOST="" \
		-e DB_PORT="" \
		-e DB_USERNAME="" \
		-e DB_PASSWORD="" \
		-e CACHE_STORE=array \
		-e SESSION_DRIVER=array \
		-e QUEUE_CONNECTION=sync \
		$(APP) ./vendor/bin/phpunit --colors=always

pint:
	docker exec $(APP) ./vendor/bin/pint

stan:
	docker exec $(APP) ./vendor/bin/phpstan analyse --memory-limit=512M

install:
	docker exec $(APP) composer install

key:
	docker exec $(APP) php artisan key:generate

logs:
	$(DC) logs -f app

# Front-end (Vue)

frontend-install: 
	$(DC) run --rm frontend npm install

frontend-dev: 
	$(DC) up -d frontend

frontend-build: 
	$(DC) run --rm frontend npm run build

frontend-logs: 
	$(DC) logs -f frontend
