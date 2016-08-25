<?php namespace MattLindesay\Messaging\Components;

use DB;
use Log;
use Auth;
use Event;
use Flash;
use Input;
use Redirect;
use Validator;
use ValidationException;
use ApplicationException;
use RainLab\User\Models\User;
use MattLindesay\Messaging\Models\Message;
use Ramsey\Uuid\Uuid;

class ComposeMessage extends \Cms\Classes\ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Compose Message',
            'description' => 'Create a new message.'
        ];
    }

    /**
     * Message to display when there are no messages.
     * @var string
     */
    public $noMessagesMessage;
    public $recipient;
    public $subject;

    // This array becomes available on the page as {{ component.messages }}
    // public function messages()
    // {
    //     $messages = Message::where('sender_id', Auth::getUser()->id)
    //         ->get();

    //     return $messages;
    // }

    public function defineProperties()
    {
        return [
            'recipient' => [
                'title'        => 'Username of the message recipient',
                'type'         => 'string'
            ],
            'subject' => [
                'title'        => 'Subject for the message',
                'type'         => 'string'
            ],
            'noMessagesMessage' => [
                'title'        => 'mattlindesay.messaging::lang.settings.messages_no_messages',
                'description'  => 'mattlindesay.messaging::lang.settings.messages_no_messages_description',
                'type'         => 'string',
                'default'      => 'No messages found',
                'showExternalParam' => false
            ],
        ];
    }

    /**
     * Prepare page variables
     */
    protected function prepareVars()
    {
        $this->noMessagesMessage = $this->page['noMessagesMessage'] = $this->property('noMessagesMessage');

        $this->recipient = $this->page['recipient'] = Input::get('recipient');

        if (Input::get('subject')) {
            if (preg_match('/^Re: /', Input::get('subject'))) {
                $this->subject = $this->page['subject'] = Input::get('subject');
            } else {
                $this->subject = $this->page['subject'] = 'Re: '.Input::get('subject');
            }
        }
    }

    /**
     * Perform functions after the variables are known
     */
    public function onRender()
    {
        $this->prepareVars();
    }

    /**
     * Send the message
     */
    public function onSendMessage()
    {
        $rules = [
            'recipient' => 'required|exists:users,username',
            'subject'   => 'required|between:0,255',
            'message'   => 'required|between:0,255'
        ];

        $validation = Validator::make(post(), $rules);
        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        // Get message details
        $sender_id = Auth::getUser()->id;
        $recipient_id = User::where('username', post('recipient'))->first()->id;
        $subject = post('subject');
        $body = post('message');

        Log::debug("sending message from $sender_id to $recipient_id. subject=$subject body=$body");

        // Create a message in the Inbox 1 of the recipient and the Sent folder 0 for the sender
        $uuid = Uuid::uuid1();

        try {
            // sent = 0
            $message = Message::create([
                    'user_id' => $sender_id,
                    'sender_id' => $sender_id,
                    'recipient_id' => $recipient_id,
                    'folder_id' => 0,
                    'subject' => $subject,
                    'body' => $body,
                    'uuid' => Uuid::uuid1(),
                ]);

            // inbox = 1
            $message = Message::create([
                    'user_id' => $recipient_id,
                    'sender_id' => $sender_id,
                    'recipient_id' => $recipient_id,
                    'folder_id' => 1,
                    'subject' => $subject,
                    'body' => $body,
                    'uuid' => $uuid,
                ]);
        } catch (Exception $e) {
            throw new ApplicationException('Failed to save message');
        }

        $sender = User::find($sender_id);
        $recipient = User::find($recipient_id);

        Event::fire('messaging.new_message', [
            $sender->username,
            $recipient->email,
            $recipient->name,
            $subject,
            $body,
            $uuid,
        ]);

        // Event::fire('messaging.newmessage', [
        //     'sender' => $sender_id,
        //     'recipient' => $recipient_id,
        //     'name' => 'unknown',
        //     'subject' => $subject,
        //     'body' => $body,
        //     'uuid' => $uuid,
        // ]);

        Flash::success('Message sent');
        return Redirect::to('messages');
    }
}
