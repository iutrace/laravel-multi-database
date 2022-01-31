<?php


namespace Iutrace\MultiDatabases\Tests;


use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Iutrace\MultiDatabases\MultiDatabaseProvider;

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
        $connections = config('database.connections');
        $this->artisan('db:wipe --database testing2')
            ->assertSuccessful();

        $this->assertTrue(Schema::connection('testing')->hasTable('test'));
        $this->assertFalse(Schema::connection('testing2')->hasTable('test'));
    }

    public function testWipeCommandWithQuestionNo()
    {
        $connections = config('database.connections');
        $this->artisan('db:wipe')
            ->expectsQuestion('This will try to wipe following connections [ ' . implode(', ', array_keys($connections)) . ' ] are you sure?', 'no')
            ->assertFailed();

        $this->assertTrue(Schema::connection('testing')->hasTable('test'));
        $this->assertTrue(Schema::connection('testing2')->hasTable('test'));
    }

    public function testWipeCommandWithQuestionYes()
    {
        $connections = config('database.connections');
        $this->artisan('db:wipe')
            ->expectsQuestion('This will try to wipe following connections [ ' . implode(', ', array_keys($connections)) . ' ] are you sure?', 'yes')
            ->assertSuccessful();

        $this->assertFalse(Schema::connection('testing')->hasTable('test'));
        $this->assertFalse(Schema::connection('testing2')->hasTable('test'));
    }

    public function testWipeCommandWithForce()
    {
        $this->artisan('db:wipe --force')
            ->assertSuccessful();

        $this->assertFalse(Schema::connection('testing')->hasTable('test'));
        $this->assertFalse(Schema::connection('testing2')->hasTable('test'));
    }
}