<?php

namespace PHPManager\PHPManager\CLI\Commands;

use Fidry\Console\Command\Command;
use Fidry\Console\Command\Configuration as CommandConfiguration;
use Fidry\Console\IO;
use Fidry\CpuCoreCounter\CpuCoreCounter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPManager\PHPManager\Lib\Installers\ComposerInstaller\ComposerInstaller;
use PHPManager\PHPManager\Lib\Installers\ComposerInstaller\ComposerProvider;
use PHPManager\PHPManager\Lib\Installers\PHPInstaller\PHPInstaller;
use PHPManager\PHPManager\Lib\Installers\PHPInstaller\PHPInstallerExecutor;
use PHPManager\PHPManager\Lib\Installers\PHPInstaller\PHPSrcProvider;
use PHPManager\PHPManager\Lib\Configuration;
use Safe\Exceptions\DirException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Filesystem\Filesystem;
use function min;
use function Safe\getcwd;
use const PHP_INT_MAX;

class InstallCommand implements Command
{
    private const MAX_CORE_COUNT_OPTION = 'max-core-count';

    public function __construct(
        private Configuration           $configuration,
        private readonly Filesystem     $filesystem,
        private readonly CpuCoreCounter $coreCounter,
    )
    {
    }

    public function getConfiguration(): CommandConfiguration
    {
        return new CommandConfiguration(
            'install',
            'Installs a local version of PHP, Composer and project dependencies',
            'This will provide help to the user',
            [],
            [
                new InputOption(
                    self::MAX_CORE_COUNT_OPTION,
                    null,
                    InputOption::VALUE_REQUIRED,
                    'The maximum number of cores to use for compilation. Defaults to the maximum number of cores available on the host system.',
                ),
            ],
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

        $coreCount = min(
            $io->getOption(self::MAX_CORE_COUNT_OPTION)->asNullablePositiveInteger() ?? PHP_INT_MAX,
            $this->coreCounter->getCount(),
        );

        $phpInstaller = new PHPInstaller(
            $cwd . '/' . $this->configuration->distDirectory . '/build',
            $cwd . '/' . $this->configuration->phpManagerDirectory,
            $this->filesystem,
            new Client(),
            new PHPSrcProvider(),
            new PHPInstallerExecutor($coreCount),
        );
        $phpInstaller->install($io);

        $composerInstaller = new ComposerInstaller(
            $cwd . '/' . $this->configuration->distDirectory . '/composer',
            $cwd . '/' . $this->configuration->phpManagerDirectory,
            $this->filesystem,
            new Client(),
            new ComposerProvider(),
        );
        $composerInstaller->install($io);

        return 0;
    }
}
