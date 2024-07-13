<?php

namespace PHPManager\PHPManager\Lib;

final readonly class Configuration
{
    function __construct(
        public string $phpManagerDirectory = '.php-manager',
        public string $distDirectory = 'dist'
    )
    {
    }
}
