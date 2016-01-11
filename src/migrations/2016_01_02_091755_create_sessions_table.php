<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sessions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->timestamps();
			$table->timestamp('end_date')->nullable();
			$table->timestamp('last_activity')->nullable();
			$table->string('ip')->nullable();
			$table->string('browser')->nullable();
			$table->string('browser_version')->nullable();
			$table->string('platform')->nullable();
			$table->string('platform_version')->nullable();
			$table->tinyInteger('mobile')->nullable();
			$table->string('device')->nullable();
			$table->string('location')->nullable();
			$table->tinyInteger('robot')->nullable();
			$table->integer('user_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('sessions');
	}

}
