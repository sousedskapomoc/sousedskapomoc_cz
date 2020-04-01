<?php declare(strict_types=1);

namespace SousedskaPomoc\Tests;

use Tester\Assert;
use Tester\TestCase;

require __DIR__ . '/bootstrap.php';

class SimpleTest extends TestCase
{
    public function testDummy()
    {
        Assert::equal(1, 1);
    }
}

(new SimpleTest())->run();