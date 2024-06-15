<?php

namespace PHPManager\PHPManager\CLI\Commands;

use Fidry\Console\Command\Command;
use Fidry\Console\Command\Configuration;
use Fidry\Console\IO;
use GuzzleHttp\Client;
use PHPManager\PHPManager\Lib\PHPInstaller\PHPInstaller;
use PHPManager\PHPManager\Lib\PHPInstaller\PHPSrcProvider;
use PHPManager\PHPManager\Lib\PHPInstaller\ProcessExecutor;
use Symfony\Component\Filesystem\Filesystem;
use function Safe\getcwd;

class InstallCommand implements Command
{
    public function __construct(
        private readonly Filesystem $filesystem,
    ) {
    }

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
        $io->write('Let\'s install PHP!');

        $cwd = getcwd();

        $installer = new PHPInstaller(
            $cwd .'/dist/build',
            $cwd .'/.php-manager',
            $this->filesystem,
            new Client(),
            new PHPSrcProvider(),
            new ProcessExecutor(),
        );
        $installer->install($io);

        return 0;
    }
}