.PHONY: help setup start stop restart build migrate seed test clean logs shell npm-dev npm-build

# Variables
DOCKER_COMPOSE = docker-compose

# Colors
GREEN  := $(shell tput -Txterm setaf 2)
YELLOW := $(shell tput -Txterm setaf 3)
WHITE  := $(shell tput -Txterm setaf 7)
RESET  := $(shell tput -Txterm sgr0)

# Help command to list all available commands
help: ## Show this help
	@echo ''
	@echo 'Usage:'
	@echo '  ${YELLOW}make${RESET} ${GREEN}<target>${RESET}'
	@echo ''
	@echo 'Targets:'
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  ${YELLOW}%-15s${RESET} ${GREEN}%s${RESET}\n", $$1, $$2}' $(MAKEFILE_LIST)

# Initial setup
setup: ## Set up project with Docker (build, start, migrate, seed, storage-link)
	cp -n .env.example .env || true
	$(DOCKER_COMPOSE) build
	$(DOCKER_COMPOSE) up -d
	$(DOCKER_COMPOSE) exec php php artisan key:generate
	$(DOCKER_COMPOSE) exec php php artisan migrate --force
	$(DOCKER_COMPOSE) exec php php artisan db:seed
	$(DOCKER_COMPOSE) exec php php artisan storage:link

# Docker commands
start: ## Start Docker containers
	$(DOCKER_COMPOSE) up -d

stop: ## Stop Docker containers
	$(DOCKER_COMPOSE) stop

restart: stop start ## Restart Docker containers

build: ## Rebuild Docker containers
	$(DOCKER_COMPOSE) build

# Laravel commands
migrate: ## Run database migrations
	$(DOCKER_COMPOSE) exec php php artisan migrate

seed: ## Seed the database
	$(DOCKER_COMPOSE) exec php php artisan db:seed

test: ## Run tests
	$(DOCKER_COMPOSE) exec php php artisan test

cache-clear: ## Clear Laravel cache
	$(DOCKER_COMPOSE) exec php php artisan cache:clear
	$(DOCKER_COMPOSE) exec php php artisan config:clear
	$(DOCKER_COMPOSE) exec php php artisan route:clear
	$(DOCKER_COMPOSE) exec php php artisan view:clear

clean: ## Remove Docker containers and volumes
	$(DOCKER_COMPOSE) down -v

logs: ## View Docker container logs
	$(DOCKER_COMPOSE) logs -f

shell: ## Open shell in PHP container
	$(DOCKER_COMPOSE) exec php /bin/bash

# Frontend commands
npm-dev: ## Run npm dev server
	npm run dev

npm-build: ## Build frontend assets
	npm run build

# Default target
.DEFAULT_GOAL := help 