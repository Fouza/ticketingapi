<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_ticket');
            $table->longText('notes')->nullable();
            $table->boolean('read')->default(false);
            $table->string('fichier')->nullable();
            $table->unsignedBigInteger('send_to');
            $table->foreign('id_ticket')->references('id')->on('tickets');
            $table->foreign('send_to')->references('id')->on('users');
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
        Schema::dropIfExists('messages');
    }
}
