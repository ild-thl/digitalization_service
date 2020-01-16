<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeyAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_assignments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('parent')->nullable();
            $table->string('tag');
            $table->unsignedBigInteger('elmo_key_id');
            $table->foreign('elmo_key_id')->references('id')->on('elmo_keys');
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
        Schema::dropIfExists('key_assignments');
    }
}
