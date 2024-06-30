<?php

declare(strict_types=1);

namespace PHPManager\PHPManager\Lib\Installers\PHPInstaller;

use Fidry\Console\IO;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use PharData;
use PHPManager\PHPManager\Lib\Interfaces\Installer;
use PHPManager\PHPManager\Lib\Interfaces\Provider;
use Safe\Exceptions\DirException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use function Safe\chdir;

final class PHPInstaller implements Installer
{
    private const PHP_SRC_TARGZ = 'https://www.php.net/distributions/php-8.3.8.tar.gz';
    private string|false $currentDirectory;

    private static function executeProcess(Process $process, IO $io): void
    {
        $io->newLine();
        $io->writeln('$ '.$process->getCommandLine());

        if ($io->isVerbose()) {
            $process->start(
                fn ($steamName, $output) => $io->write($output),
            );
            $exitCode = $process->wait();
        } else {
            $process->start();

            // TODO
            // $io->write(Ascii::COMPILING);

            $exitCode = $process->wait();
        }

        if ($exitCode !== 0) {
            throw new ProcessFailedException($process);
        }
    }

    public function __construct(
        private readonly string               $tmpDirectory,
        private readonly string               $targetDirectory,
        private readonly Filesystem           $filesystem,
        private readonly ClientInterface      $httpClient,
        private readonly Provider             $phpSrcProvider,
        private readonly PHPInstallerExecutor $processExecutor,
    ) {
        $this->currentDirectory = getcwd();
    }

    /**
     * @throws GuzzleException
     * @throws DirException
     */
    public function install(IO $io): void
    {
        $this->downloadSrc();
        $this->compile($io);
        $this->move();
    }

    /**
     * @throws GuzzleException
     * @throws DirException
     */
    private function downloadSrc(): void
    {
        $this->filesystem->mkdir($this->tmpDirectory);
        chdir($this->tmpDirectory);

        $response = $this->httpClient->request(
            'GET',
            $this->phpSrcProvider->provide(),
        );

        $phpTarGzPath = $this->tmpDirectory . '/php-src.tar.gz';
        $this->filesystem->dumpFile(
            $phpTarGzPath,
            $response->getBody()->getContents(),
        );
        // TODO: check signature

        $phpTarGz = new PharData($phpTarGzPath);
        $phpTarGz->extractTo($this->tmpDirectory);
    }

    /**
     * @throws DirException
     */
    private function compile(IO $io): void
    {
        chdir($this->tmpDirectory.'/php-8.3.8');

        self::executeProcess($this->processExecutor->buildConfiguration(), $io);
        self::executeProcess($this->processExecutor->configure(), $io);
        self::executeProcess($this->processExecutor->make(), $io);

        chdir($this->currentDirectory);
    }

    private function move(): void
    {
        $this->filesystem->mkdir($this->targetDirectory);
        // TODO: Dynamically pull origin directory (PHP version?)
        $this->filesystem->mirror(
            $this->tmpDirectory.'/php-8.3.8/sapi/cli',
            $this->targetDirectory.'/php',
        );
    }
}
