<?php

namespace Iutrace\Database\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

class WipeCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Config::set('database.connections.testing2', config('database.connections.testing'));
        Schema::connection('testing')->create('test', function (Blueprint $table) {
            $table->string('test');
        });

        Schema::connection('testing2')->create('test', function (Blueprint $table) {
            $table->string('test');
        });
    }

    public function testWipeCommandWithDatabase()
    {
        $this->artisan('db:wipe --database testing2')
            ->assertExitCode(0);

        $this->assertTrue(Schema::connection('testing')->hasTable('test'));
        $this->assertFalse(Schema::connection('testing2')->hasTable('test'));
    }

    public function testWipeCommandWithQuestionNo()
    {
        $this->artisan('db:wipe')
            ->expectsQuestion('Do you really wish to run this command?', 'no')
            ->assertExitCode(1);

        $this->assertTrue(Schema::connection('testing')->hasTable('test'));
        $this->assertTrue(Schema::connection('testing2')->hasTable('test'));
    }

    public function testWipeCommandWithQuestionYes()
    {
        $this->artisan('db:wipe')
            ->expectsQuestion('Do you really wish to run this command?', 'yes')
            ->assertExitCode(0);

        $this->assertFalse(Schema::connection('testing')->hasTable('test'));
        $this->assertFalse(Schema::connection('testing2')->hasTable('test'));
    }

    public function testWipeCommandWithForce()
    {
        $this->artisan('db:wipe --force')
            ->assertExitCode(0);

        $this->assertFalse(Schema::connection('testing')->hasTable('test'));
        $this->assertFalse(Schema::connection('testing2')->hasTable('test'));
    }
}
