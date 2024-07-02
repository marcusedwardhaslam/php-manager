<?php

namespace PHPManager\PHPManager\Lib\Installers\ComposerInstaller;

use Fidry\Console\IO;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PHPManager\PHPManager\Lib\Interfaces\Installer;
use Symfony\Component\Filesystem\Filesystem;

final class ComposerInstaller implements Installer
{
    private string|false $currentDirectory;

    function __construct(
        private readonly string $tmpDirectory,
        private readonly string $targetDirectory,
        private readonly Filesystem $filesystem,
        private readonly ClientInterface $httpClient,
        private readonly ComposerProvider $composerProvider,
    ) {
        $this->currentDirectory = getcwd();
    }

    /**
     * @throws GuzzleException
     */
    public function install(IO $io): void
    {
        $io->writeln('Installing composer...');
        $this->download();
        $this->move();
        $io->writeln('Composer installed successfully');
    }

    /**
     * @throws GuzzleException
     */
    private function download(): void
    {
        $this->filesystem->mkdir($this->tmpDirectory);
        chdir($this->tmpDirectory);

        $downloadPath = $this->tmpDirectory . '/composer';
        $this->httpClient->request('GET', $this->composerProvider->provide(), [
            'sink' => $downloadPath,
        ]);

        chdir($this->currentDirectory);
    }

    private function move(): void
    {
        $this->filesystem->copy($this->tmpDirectory.'/composer', $this->targetDirectory.'/composer');
    }
}