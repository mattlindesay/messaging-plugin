<?php

namespace mattlindesay\messaging\console;

use Illuminate\Console\Command;

class MailRouter extends Command
{
    /**
     * @var string The console command name.
     */
    protected $name = 'messaging:mailrouter';

    /**
     * @var string The console command description.
     */
    protected $description = 'Process incoming emails and forward on to appropriate users';

    /**
     * Execute the console command.
     */
    public function fire()
    {
        $this->output->writeln('Hello world!');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [];
    }
}
