<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->integer('cross_player_id')->unsigned()->nullable();
            $table->integer('zero_player_id')->unsigned()->nullable();
            $table->integer('active_player_id')->nullable();
            $table->integer('winner_id')->nullable();
            $table->integer('status');
            $table->string('password')->nullable();
            $table->text('cellCollection');
            $table->timestamps();

            $table->foreign('cross_player_id')->references('id')->on('users');
            $table->foreign('zero_player_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropForeign(['cross_player_id', 'zero_player_id']);
        });
        Schema::dropIfExists('games');
    }
}
