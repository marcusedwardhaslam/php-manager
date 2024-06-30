<?php

declare(strict_types=1);

namespace PHPManager\PHPManager\Lib\Installers\PHPInstaller;

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
            '--with-iconv=/opt/homebrew/opt/libiconv',
        ]);
    }

    public function make(): Process
    {
        $process = new Process([
            'make -j 8',
        ]);
        $process->setTimeout(null);

        return $process;
    }
}