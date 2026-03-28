# --- VARIABLES PAR DÉFAUT (À surcharger dans le projet) ---
EXEC_PHP ?= php
CONSOLE  ?= bin/console
SYMFONY  = $(EXEC_PHP) $(CONSOLE)

# --- COMMANDES UNIFIÉES ---
.PHONY: help install quality fix migrate cache-clear

help: ## Affiche l'aide
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

quality: ## Analyse complète (Mago + Rector + PHPStan)
	$(EXEC_PHP) vendor/bin/mago lint
	$(EXEC_PHP) vendor/bin/mago analyze
	$(EXEC_PHP) vendor/bin/rector process --dry-run

fix: ## Correction automatique
	$(EXEC_PHP) vendor/bin/mago lint --fix
	$(EXEC_PHP) vendor/bin/mago analyze --fix
	$(EXEC_PHP) vendor/bin/rector process
	$(EXEC_PHP) vendor/bin/mago format

migrate: ## Exécuter les migrations
	$(SYMFONY) doctrine:migrations:migrate -n

cache-clear: ## Vider le cache
	$(SYMFONY) cache:clear

# Catch-all pour passer des arguments à bin/console (ex: make sf cache:clear)
sf:
	$(SYMFONY) $(filter-out $@,$(MAKECMDGOALS))