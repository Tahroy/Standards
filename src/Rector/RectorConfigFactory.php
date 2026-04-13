<?php

declare(strict_types=1);


namespace Tahroy\Standards\Rector;

use Rector\CodeQuality\Rector\Empty_\SimplifyEmptyCheckOnEmptyArrayRector;
use Rector\CodeQuality\Rector\Expression\InlineIfToExplicitIfRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\Config\RectorConfig;
use Rector\Configuration\RectorConfigBuilder;
use Rector\Exception\Configuration\InvalidConfigurationException;
use Rector\Symfony\Set\SymfonySetList;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\DeclareStrictTypesRector;
use Rector\TypeDeclaration\Rector\StmtsAwareInterface\SafeDeclareStrictTypesRector;
use Tahroy\Standards\Enum\PhpVersionEnum;

class RectorConfigFactory
{
    /**
     * @param string[] $paths Chemins à analyser
     * @param bool $withSymfony Extra règles Symfony (Constructor Injection, etc.)
     * @param string[] $skip Règles ou chemins à ignorer en plus des défauts
     * @param PhpVersionEnum|null $phpVersion Version de PHP à cibler (par défaut celle du composer.json)
     * @param string[] $sets Sets additionnels à charger
     * @throws InvalidConfigurationException
     */
    public static function create(
        array $paths = [],
        bool $withSymfony = false,
        array $skip = [],
        ?PhpVersionEnum $phpVersion = null,
        array $sets = [],
    ): RectorConfigBuilder {

        $skip = array_merge([
            ExplicitBoolCompareRector::class,
            SimplifyEmptyCheckOnEmptyArrayRector::class,
            InlineIfToExplicitIfRector::class,
            DeclareStrictTypesRector::class,
            SafeDeclareStrictTypesRector::class,
            '**/templates',
        ], $skip);

        $rectorConfig = RectorConfig::configure()
            ->withPaths($paths)
            ->withSkip($skip)
            ->withImportNames(removeUnusedImports: true)
            ->withPreparedSets(
                deadCode: true,
                codeQuality: true,
                typeDeclarations: true,
                privatization: true,
                earlyReturn: true,
                doctrineCodeQuality: true,
                symfonyCodeQuality: true,
                symfonyConfigs: true,
            )
            ->withAttributesSets(symfony: true, doctrine: true)
            ->withSets($sets);

        if ($phpVersion instanceof PhpVersionEnum) {
            self::configurePhpVersion($rectorConfig, $phpVersion);
        } else {
            $rectorConfig->withPhpSets();
        }

        if ($withSymfony) {
            $rectorConfig
                ->withSets([
                    SymfonySetList::SYMFONY_CONSTRUCTOR_INJECTION,
                    SymfonySetList::SYMFONY_CODE_QUALITY,
                ]);
            $rectorConfig->withComposerBased(twig: true, doctrine: true, symfony: true);
        }

        return $rectorConfig;
    }

    private static function configurePhpVersion(RectorConfigBuilder $rectorConfig, PhpVersionEnum $phpVersion): void
    {
        match ($phpVersion) {
            PhpVersionEnum::PHP_53 => $rectorConfig->withPhpSets(php53: true),
            PhpVersionEnum::PHP_54 => $rectorConfig->withPhpSets(php54: true),
            PhpVersionEnum::PHP_55 => $rectorConfig->withPhpSets(php55: true),
            PhpVersionEnum::PHP_56 => $rectorConfig->withPhpSets(php56: true),
            PhpVersionEnum::PHP_70 => $rectorConfig->withPhpSets(php70: true),
            PhpVersionEnum::PHP_71 => $rectorConfig->withPhpSets(php71: true),
            PhpVersionEnum::PHP_72 => $rectorConfig->withPhpSets(php72: true),
            PhpVersionEnum::PHP_73 => $rectorConfig->withPhpSets(php73: true),
            PhpVersionEnum::PHP_74 => $rectorConfig->withPhpSets(php74: true),
            PhpVersionEnum::PHP_80 => $rectorConfig->withPhpSets(php80: true),
            PhpVersionEnum::PHP_81 => $rectorConfig->withPhpSets(php81: true),
            PhpVersionEnum::PHP_82 => $rectorConfig->withPhpSets(php82: true),
            PhpVersionEnum::PHP_83 => $rectorConfig->withPhpSets(php83: true),
            PhpVersionEnum::PHP_84 => $rectorConfig->withPhpSets(php84: true),
            PhpVersionEnum::PHP_85 => $rectorConfig->withPhpSets(php85: true),
        };
    }
}