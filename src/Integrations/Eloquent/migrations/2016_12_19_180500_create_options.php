<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('options', function (Blueprint $table) {
            $table->string('slug');
            $table->text('value');
            $table->integer('calendar_id')->unsigned();
            $table->timestamps();

            $table->primary(['slug', 'calendar_id']);
        });

        Schema::table('options', function (Blueprint $table) {
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
        Schema::drop('options');
    }
}
