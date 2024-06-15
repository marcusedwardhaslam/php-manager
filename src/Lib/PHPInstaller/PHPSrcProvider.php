<?php

declare(strict_types=1);

namespace PHPManager\PHPManager\Lib\PHPInstaller;

final class PHPSrcProvider
{
    private const PHP_SRC_TARGZ = 'https://www.php.net/distributions/php-8.3.8.tar.gz';

    public function provide(): string
    {
        return self::PHP_SRC_TARGZ;
    }
}