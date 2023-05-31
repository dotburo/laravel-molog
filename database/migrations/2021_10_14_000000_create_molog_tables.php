<?php

use Dotburo\Molog\MologConstants;
use Dotburo\Molog\Models\Message;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMologTables extends Migration
{
    /**
     * Run the migrations.
     * @return void
     */
    public function up()
    {
        $pkType = config('molog.primary_key_type');

        # Message table.
        Schema::create('messages', function (Blueprint $table) use ($pkType) {
            $table = $this->setPrimaryKey($table, $pkType);

            $table = $this->setNullableMorphColumns($table, $pkType);

            $table->string('context')->nullable()->index();

            $debugLevelCode = Message::levelCode(MologConstants::MSG_DEFAULT_LEVEL);

            $table->unsignedTinyInteger('level')->default($debugLevelCode)->index();

            $table->string('subject', MologConstants::MSG_SUBJECT_MAX_LEN);
            $table->longText('body')->nullable();

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();

            $table->timestamp('created_at', 6);
        });

        # Metrics table.
        Schema::create('gauges', function (Blueprint $table) use ($pkType) {
            $table = $this->setPrimaryKey($table, $pkType);

            $table = $this->setNullableMorphColumns($table, $pkType);

            $table->string('context')->nullable()->index();

            $table->string('key');
            $table->double('value')->default(0);
            $table->string('unit', 10)->nullable();
            $table->enum('type', [MologConstants::GAUGE_FLOAT_TYPE, MologConstants::GAUGE_INT_TYPE])
                ->default(MologConstants::GAUGE_DEFAULT_TYPE);

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('tenant_id')->nullable()->index();

            $table->timestamp('created_at', 6);
        });
    }

    /**
     * Reverse the migrations.
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
        Schema::dropIfExists('gauges');
    }

    /**
     * Set the column for the primary key on the given table.
     * @param Blueprint $table
     * @param string $primaryKeyType
     * @return Blueprint
     */
    private function setPrimaryKey(Blueprint $table, string $primaryKeyType = 'id'): Blueprint
    {
        switch ($primaryKeyType) {
            case 'uuid';
                $table->uuid('id')->primary();
                break;
            default:
                $table->bigIncrements('id');
        }

        return $table;
    }

    /**
     * Define the data type for the morphable relationship identifier.
     * @param Blueprint $table
     * @param string $primaryKeyType
     * @return Blueprint
     */
    private function setNullableMorphColumns(Blueprint $table, string $primaryKeyType = ''): Blueprint
    {
        $name = 'loggable';

        $table->string("{$name}_type")->nullable();

        switch ($primaryKeyType) {
            case 'uuid';
                $table->uuid("{$name}_id")->nullable()->index();
                break;
            default:
                $table->unsignedBigInteger("{$name}_id")->nullable();
        }

        $table->index(["{$name}_type", "{$name}_id"]);

        return $table;
    }
};
