<?php


use Illuminate\Support\Facades\Schema;

use Illuminate\Database\Schema\Blueprint;

use Illuminate\Database\Migrations\Migration;


class CreateLogActivityTable extends Migration

{

    /**

     * Run the migrations.

     *

     * @return void

     */

    public function up()

    {

        Schema::create('activity_logs', function (Blueprint $table) {

            $table->increments('id');

            $table->string('activity');
            $table->string('description');
            $table->string('url');
            $table->string('method');

            $table->string('ip');

            $table->string('agent')->nullable();

            $table->integer('user_id')->nullable();

            $table->timestamp('created_at')->default(now());

        });

    }


    /**

     * Reverse the migrations.

     *

     * @return void

     */

    public function down()

    {

        Schema::drop('activity_logs');

    }

}