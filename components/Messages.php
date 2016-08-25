<?php namespace MattLindesay\Messaging\Components;

use DB;
use Log;
use Auth;
use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use MattLindesay\Messaging\Models\Message;

class Messages extends ComponentBase
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
    public $folder;

    // This array becomes available on the page as {{ component.messages }}
    public function messages()
    {
        if ($this->property('folder') == 'sent') {
            $folder_id = 0;
        } else {
            $folder_id = 1;
        }

        $messages = Message::listFrontEnd([
            'page'       => $this->property('pageNumber'),
            'sort'       => $this->property('sortOrder'),
            'perPage'    => $this->property('postsPerPage'),
            'user_id'    => Auth::getUser()->id,
            'folder_id'  => $folder_id,
            'search'     => trim(input('search'))
        ]);

        /*
         * Mark all messages as read, now that the user has visited their messages page 
         */
        Message::where('user_id', Auth::getUser()->id)
            ->update(['is_read' => true]);

        /*
         * Encode the subject
         */
        $messages->each(function ($message) {
            $message->encodedSubject = urlencode($message->subject);
        });

        return $messages;
    }

    public function unreadMessageCount($user_id)
    {
        $count = Message::where('user_id', user_id)
            -where('is_read', false)
            ->count();

        return $count;
    }

    public function defineProperties()
    {
        return [
            'noMessagesMessage' => [
                'title'        => 'mattlindesay.messaging::lang.settings.messages_no_messages',
                'description'  => 'mattlindesay.messaging::lang.settings.messages_no_messages_description',
                'type'         => 'string',
                'default'      => 'No messages found',
                'showExternalParam' => false
            ],
            'pageNumber' => [
                'title'       => 'rainlab.blog::lang.settings.posts_pagination',
                'description' => 'rainlab.blog::lang.settings.posts_pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            'categoryFilter' => [
                'title'       => 'rainlab.blog::lang.settings.posts_filter',
                'description' => 'rainlab.blog::lang.settings.posts_filter_description',
                'type'        => 'string',
                'default'     => ''
            ],
            'postsPerPage' => [
                'title'             => 'rainlab.blog::lang.settings.posts_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'rainlab.blog::lang.settings.posts_per_page_validation',
                'default'           => '10',
            ],
            'noPostsMessage' => [
                'title'        => 'rainlab.blog::lang.settings.posts_no_posts',
                'description'  => 'rainlab.blog::lang.settings.posts_no_posts_description',
                'type'         => 'string',
                'default'      => 'No posts found',
                'showExternalParam' => false
            ],
            'sortOrder' => [
                'title'       => 'rainlab.blog::lang.settings.posts_order',
                'description' => 'rainlab.blog::lang.settings.posts_order_description',
                'type'        => 'dropdown',
                'default'     => 'created_at desc'
            ],
            'postPage' => [
                'title'       => 'rainlab.blog::lang.settings.posts_post',
                'description' => 'rainlab.blog::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'blog/post',
                'group'       => 'Links',
            ],
        ];
    }

    protected function prepareVars()
    {
        $this->noMessagesMessage = $this->page['noMessagesMessage'] = $this->property('noMessagesMessage');
        $this->folder = $this->page['folder'] = $this->property('folder');
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
    }

    public function onRender()
    {
        $this->prepareVars();
    }
}
