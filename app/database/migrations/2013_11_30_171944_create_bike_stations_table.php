<?php

use Illuminate\Database\Migrations\Migration;

class CreateBikeStationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bike_stations', function($table) {
	        $table->increments('id');
	        $table->string('bike_share_id');
	        $table->foreign('bike_share_id')->references('id')->on('bike_shares');

	        $table->string('stationId');
	        $table->string('name');
	        $table->double('lat', 9, 6);
	        $table->double('lng', 9, 6);
	        $table->boolean('available')->default(true);
	        $table->integer('bikes');
	        $table->integer('emptyDocks');
	        $table->integer('totalDocks');

	        $table->timestamps();
	    });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		//
	}

}