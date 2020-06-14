<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupToPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_group_to_permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_group_id')->unsigned();
            $table->bigInteger('permission_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('user_group_to_permissions', function (Blueprint $table) {
            $table->foreign('user_group_id')->references('id')->on('user_groups')
                ->onDelete('cascade');
            $table->foreign('permission_id')->references('id')->on('permissions')
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
        Schema::dropIfExists('user_group_to_permissions');
    }
}
