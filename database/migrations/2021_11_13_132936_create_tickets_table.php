<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->longText('description');
            $table->longText('notes')->nullable();
            $table->date('deadline');
            $table->string('etat')->default('todo');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('createdBy');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('createdBy')->references('id')->on('users');

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
        Schema::dropIfExists('tickets');
    }
}
