<?php


namespace Iutrace\MultiDatabases;

use Illuminate\Support\ServiceProvider;

class MultiDatabaseProvider extends ServiceProvider
{
    public function boot(){
        $this->app->extend('command.db.wipe', function (){
            return new WipeCommand();
        });
    }
}