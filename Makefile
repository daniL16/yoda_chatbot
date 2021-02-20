#!/bin/bash

UID = $(shell id -u)
DOCKER_BE = symfony-api-platform-be

help: ## Show this help message
	@echo 'usage: make [target]'
	@echo
	@echo 'targets:'
	@egrep '^(.+)\:\ ##\ (.+)' ${MAKEFILE_LIST} | column -t -c 2 -s ':#'

run: ## Start the containers
	docker network create symfony-api-platform-network || true
	U_ID=${UID} docker-compose up -d --build

ssh: ## ssh's into the be container
	U_ID=${UID} docker exec -it --user ${UID} ${DOCKER_BE} bash
