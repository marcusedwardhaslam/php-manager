<?php

namespace PHPManager\PHPManager\Lib\ComposerInstaller;

use PHPManager\PHPManager\Lib\Interfaces\Provider;

class ComposerProvider implements Provider
{
    public function provide(): string
    {
        return 'https://getcomposer.org/download/latest-stable/composer.phar';
    }
}