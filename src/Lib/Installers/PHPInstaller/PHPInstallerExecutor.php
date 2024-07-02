<?php

declare(strict_types=1);

namespace PHPManager\PHPManager\Lib\Installers\PHPInstaller;

use Symfony\Component\Process\Process;

final class PHPInstallerExecutor
{
    /**
     * @param positive-int $cpuCoreCount
     */
    public function __construct(
        private readonly int $cpuCoreCount,
    ) {

    }

    public function buildConfiguration(): Process
    {
        return new Process([
            './buildconf',
        ]);
    }

    public function configure(): Process
    {
        return new Process([
            './configure',
            '--with-iconv=/opt/homebrew/opt/libiconv',
        ]);
    }

    public function make(): Process
    {
        $process = new Process([
            'make',
            '--jobs='.$this->cpuCoreCount,
        ]);
        $process->setTimeout(null);

        return $process;
    }
}