<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePuntoAgendasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cnj_punto_agenda', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('descripcion', 4000);
            $table->integer('orden');
            $table->unsignedInteger('id_agenda');
            $table->foreign('id_agenda')->references('id')->on('cnj_agenda')->onDelete('cascade');
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
        Schema::dropIfExists('cnj_punto_agenda');
    }
}
