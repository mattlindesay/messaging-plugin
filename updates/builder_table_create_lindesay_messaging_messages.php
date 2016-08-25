<?php namespace mattlindesay\messaging\updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class builder_table_create_mattlindesay_messaging_messages extends Migration
{
    public function up()
    {
        Schema::create('mattlindesay_messaging_messages', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->integer('sender_id')->unsigned();
            $table->integer('recipient_id')->unsigned();
            $table->integer('folder_id')->unsigned();
            $table->boolean('is_read')->default(false);
            $table->string('subject');
            $table->string('body');
            $table->string('uuid', 36);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('mattlindesay_messaging_messages');
    }
}
