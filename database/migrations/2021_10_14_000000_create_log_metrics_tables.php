<?php

use Dotburo\LogMetrics\Message;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        # Message table.
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('tenant_id')->nullable()->index();

            $table->string('context')->index();
            $table->unsignedBigInteger('context_id')->nullable()->index();

            $table->unsignedTinyInteger('level')->default(Message::levelCode('debug'))->index();
            $table->longText('body');

            $table->timestamp('created_at')->nullable();
        });

        # Metrics table.
        Schema::create('metrics', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('tenant_id')->nullable()->index();

            $table->string('context')->index();
            $table->unsignedBigInteger('context_id')->nullable()->index();

            $table->enum('type', ['float', 'int'])->default('float');
            $table->string('key');
            $table->double('value');
            $table->string('unit', 10)->nullable();

            $table->timestamp('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('metrics');
    }
};