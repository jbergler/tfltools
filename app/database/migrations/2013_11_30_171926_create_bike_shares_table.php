<?php

use Illuminate\Database\Migrations\Migration;

class CreateBikeSharesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bike_shares', function($table) {
	        $table->string('id')->primary();
	        $table->string('name');
	        $table->string('descriptions');
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