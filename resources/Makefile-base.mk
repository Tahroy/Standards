# --- VARIABLES PAR DÉFAUT (À surcharger dans le projet) ---
EXEC_PHP ?= php
SYMFONY  = $(EXEC_PHP) bin/console

# --- COMMANDES UNIFIÉES ---
.PHONY: help install quality fix migrate cache-clear rector rector-check mago-format mago-format-check mago-lint mago-lint-check mago-analyze mago-analyze-check sf

help: ## Affiche l'aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

rector:
	$(EXEC_PHP) vendor/bin/rector process

rector-check:
	$(EXEC_PHP) vendor/bin/rector process --dry-run

mago-format:
	$(EXEC_PHP) vendor/bin/mago format

mago-format-check:
	$(EXEC_PHP) vendor/bin/mago format --dry-run

mago-lint:
	$(EXEC_PHP) vendor/bin/mago lint --fix

mago-lint-check:
	$(EXEC_PHP) vendor/bin/mago lint

mago-analyze:
	$(EXEC_PHP) vendor/bin/mago analyze --fix

mago-analyze-check:
	$(EXEC_PHP) vendor/bin/mago analyze

quality: ## Analyse complète (Mago + Rector)
	@$(MAKE) mago-lint-check
	@$(MAKE) mago-analyze-check
	@$(MAKE) rector-check

fix: ## Correction automatique
	@$(MAKE) mago-lint
	@$(MAKE) mago-analyze
	@$(MAKE) rector
	@$(MAKE) mago-format

install: ## Installer les dépendances
	composer install

migrate: ## Exécuter les migrations
	$(SYMFONY) doctrine:migrations:migrate -n

cache-clear: ## Vider le cache
	$(SYMFONY) cache:clear

# Catch-all pour passer des arguments à bin/console (ex: make sf cache:clear)
sf:
	$(SYMFONY) $(filter-out $@,$(MAKECMDGOALS))