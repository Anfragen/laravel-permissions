<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('group');
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();

            $table->unique(['group', 'name']);
        });

        Schema::create('model_role', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->morphs('model');
            $table->foreignId('role_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['model_type', 'model_id', 'role_id']);
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignId('role_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['role_id', 'permission_id']);
        });

        Schema::create('model_permission', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->morphs('model');
            $table->foreignId('permission_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['model_type', 'model_id', 'permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');

        Schema::dropIfExists('permissions');

        Schema::dropIfExists('model_role');

        Schema::dropIfExists('permission_role');

        Schema::dropIfExists('model_permission');
    }
};
