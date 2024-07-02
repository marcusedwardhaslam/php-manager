<?php

declare(strict_types=1);

namespace PHPManager\PHPManager\CLI\Commands;

use Fidry\Console\Command\Command;
use Fidry\Console\Command\Configuration;
use Fidry\Console\IO;
use GuzzleHttp\Client;
use PHPManager\PHPManager\Lib\PHPManagerConfiguration;
use Symfony\Component\Filesystem\Filesystem;
use function Safe\getcwd;

class UninstallCommand implements Command
{
    function __construct(private PHPManagerConfiguration $config, private Filesystem $filesystem)
    {

    }

    public function getConfiguration(): Configuration
    {
        return new Configuration(
            'uninstall',
            'Uninstalls the local version of PHP, Composer and all other project dependencies',
            'This will provide help to the user',
            [],
        );
    }

    public function execute(IO $io): int
    {
        $io->writeln("Removing PHP, Composer and all other project dependencies.");

        $cwd = getcwd();
        $this->filesystem->remove([$cwd . $this->config->phpManagerDirectory, $cwd . $this->config->distDirectory]);
        $io->writeln("Finished");

        return 0;
    }
}