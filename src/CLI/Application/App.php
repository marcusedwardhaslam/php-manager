<?php

namespace PHPManager\PHPManager\CLI\Application;

use Fidry\Console\Application\BaseApplication;
use PHPManager\PHPManager\CLI\Commands\InstallCommand;
use Symfony\Component\Filesystem\Filesystem;

class App extends BaseApplication
{

    public function getName(): string
    {
        return 'PHP Manager';
    }

    public function getVersion(): string
    {
        return '0.1.0';
    }

    public function getCommands(): array
    {
        return [
            new InstallCommand(
                new Filesystem(),
            ),
        ];
    }
}