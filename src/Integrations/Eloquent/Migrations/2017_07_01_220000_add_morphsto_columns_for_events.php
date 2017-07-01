<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * @codeCoverageIgnore
 */
class AddMorphstoColumnsForEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->string("taggable_type")
                ->nullable()
                ->after('calendar_id');

            $table->unsignedInteger("taggable_id")
                ->nullable()
                ->after('taggable_type');

            $table->index(["taggable_id", "taggable_type"], null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('taggable_type');
            $table->dropColumn('taggable_id');
        });
    }
}
