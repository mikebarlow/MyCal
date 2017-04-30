<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * @codeCoverageIgnore
 */
class CreateEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('calendar_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('event_extras', function (Blueprint $table) {
            $table->string('slug');
            $table->text('value');
            $table->integer('event_id')->unsigned();
            $table->timestamps();

            $table->primary(['slug', 'event_id']);
        });

        Schema::table('event_extras', function (Blueprint $table) {
            $table->foreign('event_id')
                ->references('id')->on('events');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->foreign('calendar_id')
                ->references('id')->on('calendars');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('event_extras');
        Schema::drop('events');
    }
}
