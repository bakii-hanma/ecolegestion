<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique()->nullable();
            $table->string('phone');
            $table->string('phone_2')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->text('address');
            $table->string('profession')->nullable();
            $table->string('workplace')->nullable();
            $table->enum('relationship', ['father', 'mother', 'guardian', 'other'])->default('father');
            $table->boolean('is_primary_contact')->default(false);
            $table->boolean('can_pickup')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
