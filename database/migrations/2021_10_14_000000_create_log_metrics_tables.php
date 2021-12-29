<?php

use Dotburo\LogMetrics\LogMetricsConstants;
use Dotburo\LogMetrics\Models\Message;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogMetricsTables extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        # Message table.
        Schema::create('messages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->unsignedBigInteger('tenant_id')->nullable()->index();

            $table->string('loggable_id')->nullable()->index();
            $table->string('loggable_type')->nullable()->index();

            $table->string('context')->nullable()->index();

            $debugLevelCode = Message::levelCode(LogMetricsConstants::DEBUG);

            $table->unsignedTinyInteger('level')->default($debugLevelCode)->index();
            $table->longText('body');

            $table->timestamp('created_at', 3)->nullable();
        });

        # Metrics table.
        Schema::create('metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->unsignedBigInteger('tenant_id')->nullable()->index();

            $table->string('loggable_id')->nullable()->index();
            $table->string('loggable_type')->nullable()->index();

            $table->string('context')->nullable()->index();

            $table->enum('type', ['float', 'int'])->default('float');
            $table->string('key');
            $table->double('value')->default(0);
            $table->string('unit', 10)->nullable();

            $table->timestamp('created_at', 3)->nullable();
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
