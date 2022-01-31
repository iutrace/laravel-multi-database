<?php

namespace Iutrace\MultiDatabases;

use Illuminate\Database\Console\WipeCommand as LaravelWipeCommand;

class WipeCommand extends LaravelWipeCommand
{
    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        if ($this->option('database') != null) { // If database was indicated only wipe that connection
            $this->input->setOption('force', true);
            return parent::handle();
        }

        $connections = config('database.connections');

        if($this->option('force') == false) {
            $answer = $this->output->confirm('This will try to wipe following connections [ '. implode(', ', array_keys($connections)) .' ] are you sure?', false);
            if($answer == 'no') {
                return 1;
            }
        }

        $this->input->setOption('force', true);

        // Run for every connection
        foreach ($connections as $name => $connection) {
            $this->input->setOption('database', $name);
            $this->output->write($name . ': ');
            try {
                parent::handle();
            } catch (\Exception $e) {
                $this->output->writeLn("<fg=red>Could not connect.</>");
            }
        }

        return 0;
    }
}