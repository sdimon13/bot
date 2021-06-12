<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateObjectsEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('objects_events', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('object_id');
            $table->bigInteger('event_id');
            $table->timestamp('event_datetime');
            $table->string('object_name')->nullable();
            $table->integer('object_number')->nullable();
            $table->string('description_name')->nullable();
            $table->string('description_comment')->nullable();
            $table->string('zone_name')->nullable();
            $table->string('device_type')->nullable();
            $table->string('device_name')->nullable();
            $table->string('address')->nullable();
            $table->jsonb('contacts')->nullable();
            $table->boolean('is_processed')->default(false);
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
        Schema::dropIfExists('objects_events');
    }
}
