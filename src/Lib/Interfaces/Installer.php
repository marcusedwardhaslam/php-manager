<?php

namespace PHPManager\PHPManager\Lib\Interfaces;

use Fidry\Console\IO;

interface Installer
{
    public function install(IO $io): void;
}