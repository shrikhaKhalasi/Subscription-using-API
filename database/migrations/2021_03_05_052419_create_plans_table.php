<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->increments('id')->index()->comment('AUTO_INCREMENT');
            $table->string('name',255)->nullable();
            $table->string('price',255)->nullable();
            $table->string('stripe_plan_id')->nullable();
            $table->string('stripe_product_id')->nullable();
            $table->string('currency')->nullable()->comment('Currency Code');
            $table->enum('interval', ['0', '1', '2', '3'])->nullable()->comment('0 - Daily, 1 - Week, 2 - Monthly, 3 - Yearly');
            $table->unsignedInteger('interval_count')->nullable();
            $table->unsignedInteger('total_subscriptions')->nullable();
            $table->enum('is_active', ['0', '1'])->nullable()->comment('0 - No, 1 - Yes');
            $table->enum('is_trial', ['0', '1'])->index()->nullable()->comment('0 - No, 1 - Yes');
            $table->unsignedInteger('trial_period_days')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('plans');
    }
}
