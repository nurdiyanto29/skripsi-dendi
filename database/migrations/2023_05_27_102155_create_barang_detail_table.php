<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangDetailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang_detail', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('barang_id');
            $table->bigInteger('penyewa')->nullable(); //user id dari penyewa
            $table->dateTime('mulai')->nullable();
            $table->dateTime('kembali')->nullable();
            $table->boolean('status_sewa')->default(0);
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
        Schema::dropIfExists('barang_detail');
    }
}
