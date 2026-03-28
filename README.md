# Tahroy Standards

Ce projet contient des configurations standardisées pour Rector, Mago et d'autres outils d'analyse statique.

## Installation

Comme le projet n'est pas sur Packagist, vous devez ajouter le dépôt à votre `composer.json` :

```json
{
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/tahroy/standards"
    }
  ],
  "require-dev": {
    "tahroy/standards": "dev-main"
  }
}
```

Puis lancez :

```bash
composer update tahroy/standards
```

## Rector

Le projet propose une `RectorConfigFactory` pour simplifier la configuration de Rector.

### Utilisation de RectorConfigFactory

Dans votre fichier `rector.php` :

```php
<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Tahroy\Standards\Enum\PhpVersionEnum;
use Tahroy\Standards\Rector\RectorConfigFactory;

return RectorConfigFactory::create(
    paths: [
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ],
    phpVersion: PhpVersionEnum::PHP_83,
    withSymfony: true, // Active les règles Symfony si nécessaire
    skip: [
        // Règles spécifiques à ignorer
    ],
    sets: [
        // Sets additionnels
    ]
);
```

### Paramètres de `create()`

- `paths` : Liste des dossiers à analyser.
- `withSymfony` : Active les règles Symfony et les configurations basées sur Composer.
- `skip` : Liste des règles ou chemins à ignorer.
- `phpVersion` : Utilise `PhpVersionEnum` pour cibler une version spécifique de PHP.
- `sets` : Liste de sets Rector additionnels à charger.

## Mago

Une configuration de base est disponible dans `resources/mago.toml.dist`. 

Pour l'utiliser, copiez-la à la racine de votre projet :

```bash
cp vendor/tahroy/standards/resources/mago.toml.dist mago.toml
```

## Makefile

Un `Makefile-base.mk` est fourni pour unifier les commandes de qualité.

Créez un `Makefile` à la racine de votre projet :

```makefile
include vendor/tahroy/standards/resources/Makefile-base.mk
```

### Personnalisation et Surcharges

Toutes les variables dans `Makefile-base.mk` sont définies avec l'opérateur `?=`, ce qui les rend facultatives et facilement surchargeables.

#### Surcharger des variables

Si votre projet utilise Docker ou une structure différente, définissez les variables **avant** l'inclusion :

```makefile
EXEC_PHP = docker compose exec php php

include vendor/tahroy/standards/resources/Makefile-base.mk
```

#### Surcharger ou étendre une commande

Pour ajouter des étapes à une commande existante, vous pouvez la redéfinir localement. Par exemple, pour ajouter un test de sécurité à `make quality` :

```makefile
include vendor/tahroy/standards/resources/Makefile-base.mk

# On étend la commande quality
quality:
	$(EXEC_PHP) vendor/bin/mago lint
	$(EXEC_PHP) vendor/bin/mago analyze
	$(EXEC_PHP) vendor/bin/rector process --dry-run
	$(EXEC_PHP) vendor/bin/local-php-security-checker
```

### Commandes principales

- `make quality` : Analyse complète (Mago lint/analyze + Rector dry-run).
- `make fix` : Correction automatique via Mago et Rector.
- `make sf cache-clear` : Raccourci pour les commandes Symfony.
- `make help` : Liste toutes les commandes disponibles.
