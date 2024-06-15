<?php

declare(strict_types=1);

namespace PHPManager\PHPManager\Lib\PHPInstaller;

use Symfony\Component\Process\Process;

final class PHPInstallerExecutor
{
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
            '--without-iconv',
        ]);
    }

    public function make(): Process
    {
        $process = new Process([
            'make',
        ]);
        $process->setTimeout(null);

        return $process;
    }
}