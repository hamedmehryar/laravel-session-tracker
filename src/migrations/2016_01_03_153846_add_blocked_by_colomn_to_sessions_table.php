<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBlockedByColomnToSessionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('sessiontracker_sessions', function(Blueprint $table)
		{
			$table->integer('blocked_by')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('sessions', function(Blueprint $table)
		{
			$table->dropColumn('blocked_by');
		});
	}

}
