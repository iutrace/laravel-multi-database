<?php

namespace Iutrace\Database\Tests;

use Iutrace\MultiDatabases\MultiDatabaseProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            \Iutrace\Database\MultiDatabaseProvider::class,
        ];
    }
}
