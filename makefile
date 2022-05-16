#!/usr/bin/make -f

help: ## This help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

composer: ## Install composer without dev dependencies
	composer install --ignore-platform-reqs --no-dev

composer-dev: ## Install composer with dev dependencies
	composer install --ignore-platform-reqs

format-check: ## Check code with wp-coding-standards
	vendor/bin/phpcs --standard=WordPress ./ --ignore=*vendor/*

format-fix: ## Fix code with wp-coding-standards
	vendor/bin/phpcbf --standard=WordPress ./ --ignore=*vendor/*
