<?php

namespace PHPManager\PHPManager\Tests;

use PHPManager\PHPManager\Demo;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Demo::class)]
final class DemoTest extends TestCase
{
    public function test_dummy(): void
    {
        $this->addToAssertionCount(1);
    }
}
