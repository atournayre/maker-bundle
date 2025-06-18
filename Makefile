# Executables
PHP      = php
COMPOSER = composer

# Misc
.DEFAULT_GOAL = help
.PHONY        : help composer vendor qa tests phpstan rector quality fix

help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

## —— Composer 🧙 ——————————————————————————————————————————————————————————————
composer: ## Run composer, pass the parameter "c=" to run a given command, example: make composer c='req symfony/orm-pack'
	@$(eval c ?=)
	@$(COMPOSER) $(c)

vendor: ## Install vendors according to the current composer.lock file
vendor: c=install --prefer-dist --no-progress --no-scripts --no-interaction
vendor: composer

## —— QA ———————————————————————————————————————————————————————————————————————
qa: fix rector quality phpstan tests ## Run all QA tools

tests: ## Run tests
	@$(PHP) vendor/bin/phpunit

phpstan: ## Run PHPStan
	@$(PHP) vendor/bin/phpstan analyse --configuration=tools/phpstan/phpstan.neon --memory-limit=4G

phpstan-update-baseline: ## Update PHPStan baseline
	@$(PHP) vendor/bin/phpstan analyse --configuration=tools/phpstan/phpstan.neon --memory-limit=4G --generate-baseline=tools/phpstan/baseline.neon

rector: ## Run Rector
	@$(PHP) vendor/bin/rector process src --config=tools/rector.php

rector-no-cache: ## Run Rector
	@$(PHP) vendor/bin/rector process src --config=tools/rector.php --clear-cache

quality: ## Run Swiss Knife
	@#$(PHP) vendor/bin/swiss-knife privatize-constants src tests \
#		--exclude-path=src/Resources/skeleton
	@#$(PHP) vendor/bin/swiss-knife check-conflicts .
	@#$(PHP) vendor/bin/swiss-knife check-commented-code src --line-limit 5
	@#$(PHP) vendor/bin/swiss-knife find-multi-classes src

fix: ## Run PHP-CS-Fixer
	@$(PHP) vendor/bin/php-cs-fixer fix --config=tools/phpcsfixer/php-cs-fixer.php
