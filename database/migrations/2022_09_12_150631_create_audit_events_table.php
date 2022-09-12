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
        Schema::create('audit_events', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique();
            $table->integer('author_id');
            $table->integer('entity_id');
            $table->string('entity_type');
            // details
            $table->longText('custom_message')->nullable();
            $table->integer('target_id');
            $table->string('target_type');
            $table->string('target_details');
            $table->ipAddress('ip_address');
            $table->string('entity_path');
            $table->string('author_name');
            $table->json('details');
            //
            $table->timestamp('imported_at')->useCurrent();
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
        Schema::dropIfExists('audit_events');
    }
};
