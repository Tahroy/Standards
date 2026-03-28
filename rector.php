<?php

declare(strict_types=1);

use Tahroy\Standards\Enum\PhpVersionEnum;
use Tahroy\Standards\Rector\RectorConfigFactory;

return RectorConfigFactory::create(
    paths: [__DIR__ . '/src',],
    phpVersion: PhpVersionEnum::PHP_83,
);
