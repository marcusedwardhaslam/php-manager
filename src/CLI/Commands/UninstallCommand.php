<?php

declare(strict_types=1);

namespace PHPManager\PHPManager\CLI\Commands;

use Fidry\Console\Command\Command;
use Fidry\Console\Command\Configuration as CommandConfiguration;
use Fidry\Console\IO;
use GuzzleHttp\Client;
use PHPManager\PHPManager\Lib\Configuration;
use Symfony\Component\Filesystem\Filesystem;
use function Safe\getcwd;

class UninstallCommand implements Command
{
    function __construct(
        private Configuration $config,
        private Filesystem    $filesystem,
    )
    {
    }

    public function getConfiguration(): CommandConfiguration
    {
        return new CommandConfiguration(
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
        $phpManagerPath = $cwd . '/' . $this->config->phpManagerDirectory;
        $distPath = $cwd . '/' . $this->config->distDirectory;
        $paths = [$phpManagerPath, $distPath];

        foreach ($paths as $path) {
            $io->writeln("Removing $path");
            $this->filesystem->remove($path);
        }

        $io->writeln("Finished");
        return 0;
    }
}
