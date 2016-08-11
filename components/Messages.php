<?php namespace Lindesay\Messaging\Components;

use DB;
use Auth;
use Lindesay\Messaging\Models\Message;

class Messages extends \Cms\Classes\ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Message List',
            'description' => 'Display a list of messages for user.'
        ];
    }

    /**
     * Message to display when there are no messages.
     * @var string
     */
    public $noMessagesMessage;

    // This array becomes available on the page as {{ component.messages }}
    public function messages()
    {

        $messages = Message::where('sender_id', Auth::getUser()->id)
            ->get();

        return $messages;
    }

    public function defineProperties()
    {
        return [
            'noMessagesMessage' => [
                'title'        => 'lindesay.messaging::lang.settings.messages_no_messages',
                'description'  => 'lindesay.messaging::lang.settings.messages_no_messages_description',
                'type'         => 'string',
                'default'      => 'No messages found',
                'showExternalParam' => false
            ],
        ];
    }

    protected function prepareVars()
    {
        $this->noMessagesMessage = $this->page['noMessagesMessage'] = $this->property('noMessagesMessage');
    }

    public function onRun()
    {
        $this->prepareVars();
    }
}
