<?php

namespace mattlindesay\messaging;

// Requires
// October.Drivers
// Rainlab.Forum
// Rainlab.User

use Mail;
use Event;
use System\Classes\PluginBase;
use MattLindesay\Messaging\Models\Message;

class Plugin extends PluginBase
{
    public function registerComponents()
    {
        return [
            'MattLindesay\Messaging\Components\Messages' => 'messages',
            'MattLindesay\Messaging\Components\ComposeMessage' => 'composemessage',
        ];
    }

    public function registerSettings()
    {
    }

    public function registerMarkupTags()
    {
        return [
            'functions' => [
                'unreadMessageCount' => [$this, 'unreadMessageCount'],
            ],
        ];
    }

    public function unreadMessageCount($user_id)
    {
        $count = Message::where('user_id', $user_id)
            ->where('folder_id', '!=', 1)
            ->where('is_read', false)
            ->count();

        return $count;
    }

    public function register()
    {
        $this->registerConsoleCommand('mattlindesay.mailrouter', 'MattLindesay\Messaging\Console\MailRouter');
    }

    public function registerMailTemplates()
    {
        return [
            'mattlindesay.messaging::mail.new_message' => 'New message received for a user.',
        ];
    }

    public function boot()
    {
        Event::listen('messaging.new_message', function (
            $sender,
            $recipient,
            $name,
            $subject,
            $body,
            $uuid
        ) {
            $vars = [
                'sender' => $sender,
                'recipient' => $recipient,
                'name' => $name,
                'subject' => $subject,
                'body' => $body,
                'uuid' => $uuid,
            ];

            Mail::send('mattlindesay.messaging::mail.new_message', $vars, function ($message) use ($recipient, $name, $subject) {
                $message->to($recipient, $name);
                $message->subject($subject);
            });

        });
    }
}
