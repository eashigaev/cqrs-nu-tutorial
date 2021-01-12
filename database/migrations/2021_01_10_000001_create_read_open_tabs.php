<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReadOpenTabs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('read_open_tabs_tabs', function (Blueprint $table) {
            $table->id();
            $table->string('tab_id')->unique();
            $table->integer('table_number')->unique();
            $table->string('waiter');
            $table->timestamps();
        });

        Schema::create('read_open_tabs_items', function (Blueprint $table) {
            $table->id();
            $table->string('tab_id')->index();
            $table->integer('menu_number');
            $table->string('description');
            $table->float('price');
            $table->boolean('prepared');
            $table->boolean('served');
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
        Schema::dropIfExists('read_open_tabs_items');
        Schema::dropIfExists('read_open_tabs_tabs');
    }
}
