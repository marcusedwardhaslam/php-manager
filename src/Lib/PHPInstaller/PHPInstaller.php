<?php

declare(strict_types=1);

namespace PHPManager\PHPManager\Lib\PHPInstaller;

use Fidry\Console\IO;
use GuzzleHttp\ClientInterface;
use PharData;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use function Safe\chdir;

final class PHPInstaller
{
    private const PHP_SRC_TARGZ = 'https://www.php.net/distributions/php-8.3.8.tar.gz';

    public function __construct(
        private readonly string $tmpDirectory,
        private readonly string $targetDirectory,
        private readonly Filesystem $filesystem,
        private readonly ClientInterface $httpClient,
        private readonly PHPSrcProvider $phpSrcProvider,
        private readonly ProcessExecutor $processExecutor,
    ) {
    }

    public function install(IO $io): void
    {
        $this->downloadSrc();
        $this->compile($io);
    }

    private function downloadSrc(): void
    {
        return;
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

    private function compile(IO $io): void
    {
        chdir($this->tmpDirectory.'/php-8.3.8');

        self::executeProcess($this->processExecutor->buildConfiguration(), $io);
        self::executeProcess($this->processExecutor->configure(), $io);
        self::executeProcess($this->processExecutor->make(), $io);
    }

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
}