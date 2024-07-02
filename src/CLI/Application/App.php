<?php

namespace PHPManager\PHPManager\CLI\Application;

use Fidry\Console\Application\BaseApplication;
use Fidry\CpuCoreCounter\CpuCoreCounter;
use Fidry\CpuCoreCounter\Finder\FinderRegistry;
use PHPManager\PHPManager\CLI\Commands\InstallCommand;
use PHPManager\PHPManager\CLI\Commands\RunCommand;
use PHPManager\PHPManager\CLI\Commands\UninstallCommand;
use PHPManager\PHPManager\Lib\PHPManagerConfiguration;
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
        $config = new PHPManagerConfiguration();
        $fileSystem = new FileSystem();
        return [
            new InstallCommand(
                $config,
                $fileSystem,
                new CpuCoreCounter(FinderRegistry::getDefaultLogicalFinders()),
            ),
            new RunCommand(
                $config,
            ),
            new UninstallCommand(
                $config,
                $fileSystem,
                new Filesystem(),
            ),
        ];
    }
}