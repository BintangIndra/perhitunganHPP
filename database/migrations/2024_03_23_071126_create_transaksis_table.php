<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('description');
            $table->date('date');
            $table->integer('qty');
            $table->decimal('cost', 8, 2); // Assuming 8 digits in total, 2 after the decimal point
            $table->decimal('price', 8, 2);
            $table->decimal('total_cost', 8, 2);
            $table->integer('qty_balance');
            $table->decimal('value_balance', 8, 2);
            $table->decimal('hpp', 8, 4); // Assuming 8 digits in total, 4 after the decimal point
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
        Schema::dropIfExists('transaksis');
    }
}
