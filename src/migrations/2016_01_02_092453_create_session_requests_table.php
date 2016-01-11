<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionRequestsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sessiontracker_session_requests', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('session_id')->nullable();
			$table->timestamps();
			$table->string('route')->nullable();
			$table->text('uri')->nullable();
			$table->string('name')->nullable();
			$table->string('method')->nullable();
			$table->text('parameters')->nullable();
			$table->tinyInteger('type');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sessiontracker_session_request');
	}

}
