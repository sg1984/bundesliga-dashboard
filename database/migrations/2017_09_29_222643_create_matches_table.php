<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('group_id');
            $table->dateTime('date_time');
            $table->boolean('is_finished')->default(false);
            $table->boolean('is_today')->default(false);
            $table->unsignedInteger('home_team_id');
            $table->unsignedInteger('visitor_team_id');
            $table->unsignedInteger('score_home_team')->nullable();
            $table->unsignedInteger('score_home_team')->nullable();
            $table->unsignedInteger('match_id_api');
            $table->timestamps();

            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->foreign('home_team_id')->references('id')->on('teams')->onDelete('cascade');
            $table->foreign('visitor_team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches');
    }
}
