<?php

namespace PHPManager\PHPManager\Lib;

final readonly class PHPManagerConfiguration
{
    function __construct(public string $phpManagerDirectory = '/.php-manager', public string $distDirectory = '/dist')
    {
    }
}