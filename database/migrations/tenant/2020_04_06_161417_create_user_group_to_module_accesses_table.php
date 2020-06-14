<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupToModuleAccessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_group_to_module_accesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_group_id')->unsigned();
            $table->bigInteger('module_access_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('user_group_to_module_accesses', function (Blueprint $table) {
            $table->foreign('user_group_id')->references('id')->on('user_groups')
                ->onDelete('cascade');
            $table->foreign('module_access_id')->references('id')->on('module_accesses')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_group_to_module_accesses');
    }
}
