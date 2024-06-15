<?php

namespace PHPManager\PHPManager\CLI\Commands;

use Fidry\Console\Command\Command;
use Fidry\Console\Command\Configuration;
use Fidry\Console\IO;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPManager\PHPManager\Lib\Installers\ComposerInstaller\ComposerInstaller;
use PHPManager\PHPManager\Lib\Installers\ComposerInstaller\ComposerProvider;
use PHPManager\PHPManager\Lib\Installers\PHPInstaller\PHPInstaller;
use PHPManager\PHPManager\Lib\Installers\PHPInstaller\PHPInstallerExecutor;
use PHPManager\PHPManager\Lib\Installers\PHPInstaller\PHPSrcProvider;
use Safe\Exceptions\DirException;
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

    /**
     * @throws DirException
     * @throws GuzzleException
     */
    public function execute(IO $io): int
    {
        $io->writeln('Let\'s install PHP!');

        $cwd = getcwd();

        $phpInstaller = new PHPInstaller(
            $cwd .'/dist/build',
            $cwd .'/.php-manager',
            $this->filesystem,
            new Client(),
            new PHPSrcProvider(),
            new PHPInstallerExecutor(),
        );
        $phpInstaller->install($io);

        $composerInstaller = new ComposerInstaller(
            $cwd .'/dist/composer',
            $cwd .'/.php-manager',
            $this->filesystem,
            new Client(),
            new ComposerProvider(),
        );
        $composerInstaller->install($io);

        return 0;
    }
}