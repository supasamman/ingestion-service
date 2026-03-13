SHELL := /bin/sh

ifneq (,$(wildcard .env))
include .env
export
endif

ifneq (,$(wildcard .env.local))
include .env.local
export
endif

.PHONY: up php-rebuild php phpstan cs-fix rector k6

init:
	cp .env.example .env
	docker compose up -d --build
	docker exec -it symfony_php composer install

up:
	docker compose up -d
	@echo
	@echo "Application is available at: http://localhost:$(APP_HTTP_PORT)/"

down:
	docker compose down

php-rebuild:
	docker compose up -d --no-deps --build php
	@echo
	@echo "Application is available at: http://localhost:$(APP_HTTP_PORT)/"

php:
	docker compose exec php bash

test:
	docker exec -it symfony_php php bin/phpunit

phpstan:
	docker compose exec php php vendor/bin/phpstan analyse -c phpstan.neon.dist

cs-fix:
	docker compose exec php php vendor/bin/php-cs-fixer fix

rector:
	docker compose exec php php vendor/bin/rector process