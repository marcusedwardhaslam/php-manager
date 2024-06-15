<?php

namespace PHPManager\PHPManager\CLI\Commands;

use Fidry\Console\Command\Command;
use Fidry\Console\Command\Configuration;
use Fidry\Console\IO;

class InstallCommand implements Command
{
    public function getConfiguration(): Configuration
    {
        return new Configuration(
            'install',
            'Installs a local version of PHP, Composer and project dependencies',
            'This will provide help to the user',
            [],
        );
    }

    public function execute(IO $io): int
    {
        $io->write('Hello, World!');

        return 0;
    }
}