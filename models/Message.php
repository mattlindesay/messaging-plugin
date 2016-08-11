<?php namespace Lindesay\Messaging\Models;

use Model;

/**
 * Model
 */
class Message extends Model
{
    use \October\Rain\Database\Traits\Validation;

    /*
     * Validation
     */
    public $rules = [
    ];

    public $fillable = ['sender_id','receiver_id','message'];

    /*
     * Disable timestamps by default.
     * Remove this line if timestamps are defined in the database table.
     */
    //public $timestamps = false;

    /**
     * @var string The database table used by the model.
     */
    public $table = 'lindesay_messaging_messages';

    /**
     * @var array Relations
     */
    public $belongsTo = [
        'sender' => ['RainLab\User\Models\User', 'table' => 'users'],
        'receiver' => ['RainLab\User\Models\User', 'table' => 'users'],
    ];
}
