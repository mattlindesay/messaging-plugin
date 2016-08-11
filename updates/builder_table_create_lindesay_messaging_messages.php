<?php namespace Lindesay\Messaging\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateLindesayMessagingMessages extends Migration
{
    public function up()
    {
        Schema::create('lindesay_messaging_messages', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('sender_id')->unsigned();
            $table->integer('receiver_id')->unsigned();
            $table->string('message');
            $table->timestamps();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('lindesay_messaging_messages');
    }
}
