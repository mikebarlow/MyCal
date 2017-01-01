<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCalendar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calendars', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('user_id')->unsigned();
            $table->timestamps();
        });

        Schema::create('calendar_extras', function (Blueprint $table) {
            $table->string('slug');
            $table->text('value');
            $table->integer('calendar_id')->unsigned();
            $table->timestamps();

            $table->primary(['slug', 'calendar_id']);
        });

        Schema::table('calendar_extras', function (Blueprint $table) {
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
        Schema::drop('calendar_extras');
        Schema::drop('calendars');
    }
}
