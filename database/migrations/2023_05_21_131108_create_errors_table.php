<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('errors', function (Blueprint $table) {
            $table->id();
            $table->string('file')->nullable();
            $table->string('group')->nullable();
            $table->integer("day");
            $table->integer("week");
            $table->integer("class");
            $table->text('value')->nullable();
            $table->string('teacher')->nullable();
            $table->string('classroom')->nullable();
            $table->text('discipline')->nullable();
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
        Schema::dropIfExists('errors');
    }
};
