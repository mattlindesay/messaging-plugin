<?php namespace mattlindesay\messaging\console;

# Reference: https://www.sitepoint.com/piping-emails-laravel-application/

use Log;
use MimeMailParser\Parser;
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
        // read from stdin
        $fd = fopen("php://stdin", "r");
        $rawEmail = "";
        while (!feof($fd)) {
            $rawEmail .= fread($fd, 1024);
        }
        fclose($fd);

        $parser = new Parser();
        $parser->setText($rawEmail);
        
        $to = $parser->getHeader('to');
        $from = $parser->getHeader('from');
        $subject = $parser->getHeader('subject');
        $text = $parser->getMessageBody('text');

        Log::info('email received');
        Log::info('subject = '.$subject);
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
