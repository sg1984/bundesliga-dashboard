<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('results', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('season_id');
            $table->unsignedInteger('team_id');
            $table->unsignedInteger('won')->default(0);
            $table->unsignedInteger('lost')->default(0);
            $table->unsignedInteger('draw')->default(0);
            $table->unsignedInteger('goals_pro')->default(0);
            $table->unsignedInteger('goals_against')->default(0);
            $table->integer('goals_diff')->default(0);
            $table->unsignedInteger('points')->default(0);
            $table->timestamps();

            $table->foreign('season_id')->references('id')->on('seasons')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('teams')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('results');
    }
}
