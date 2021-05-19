<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->id('card_id');
            $table->integer('stack_id');
            $table->integer('previous_stack_id')->nullable();
            $table->char('card_priority', 1);
            $table->string('card_name');
            $table->string('card_author');
            $table->integer('card_progress')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->boolean('checked_by_developer')->default(0)->nullable();
            $table->boolean('checked_by_outsourcer')->default(0)->nullable();
            $table->boolean('checked_by_client')->default(0)->nullable();
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
        Schema::dropIfExists('cards');
    }
}
