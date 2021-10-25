#!/bin/bash

help: # Show help
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//'

build: ## Build and run
	docker-compose up -d --build
	docker-compose exec -T php composer install --no-scripts --no-interaction --optimize-autoloader
run: ## Up docker containers
	docker-compose up -d
composer-install: ## Run composer install
	docker-compose exec -T php composer install --no-scripts --no-interaction --optimize-autoloader
composer-update: ## Run composer update in php container
	docker-compose exec -T php composer update --no-scripts --no-interaction --optimize-autoloader
code-analyse: ## Analyse code
	docker-compose exec php vendor/bin/phpstan analyse -l 8 src tests
tests: ## Run all tests
	docker-compose exec -T php bin/phpunit
