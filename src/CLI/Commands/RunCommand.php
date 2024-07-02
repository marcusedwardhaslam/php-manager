<?php

namespace PHPManager\PHPManager\CLI\Commands;

use Fidry\Console\Command\Command;
use Fidry\Console\Command\Configuration;
use Fidry\Console\IO;
use PHPManager\PHPManager\Lib\PHPManagerConfiguration;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\PhpProcess;
use Symfony\Component\Process\Process;

class RunCommand implements Command
{

    const string BINARY = 'binary';
    const POSARGS = 'posargs';

    function __construct(private PHPManagerConfiguration $config)
    {

    }

    public function getConfiguration(): Configuration
    {
        return new Configuration(
            'run',
            'Runs a command in the php-manager environment',
            'This will provide help to the user',
            [
                new InputArgument(
                    self::BINARY,
                    InputArgument::REQUIRED,
                    'The binary to run'
                ),
                new InputArgument(
                    self::POSARGS,
                    InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                    'Arguments to pass to the binary. Use <info>--</info> to separate from composer arguments'
                ),
            ],
        );
    }

    /**
     * @inheritDoc
     */
    public function execute(IO $io): int
    {
        // TODO: Implement execute() method.
        $bin = $io->getTypedArgument(self::BINARY)->asString();
        $process = new Process(["./{$this->config->phpManagerDirectory}/php/php", $bin, ...$io->getTypedArgument(self::POSARGS)->asRaw()]);
        if ($io->isVerbose()) {
            $process->start(
                function ($type, $buffer) use ($io) {
                    $io->write($buffer);
                }
            );
            $exitCode = $process->wait();
        } else {
            $exitCode = $process->run();
            $io->writeln($process->getOutput());
        }

        if ($exitCode !== 0) {
            throw new ProcessFailedException($process);
        }

        return 0;
    }
}