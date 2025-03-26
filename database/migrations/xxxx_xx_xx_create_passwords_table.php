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
        // Check if table exists first to avoid errors
        if (!Schema::hasTable('passwords')) {
            Schema::create('passwords', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('titre');
                $table->string('site_web')->nullable();
                $table->text('mot_de_passe_crypte');
                $table->timestamp('last_used')->nullable();
                $table->timestamps();
            });
        } else {
            if (!Schema::hasColumn('passwords', 'last_used')) {
                Schema::table('passwords', function (Blueprint $table) {
                    $table->timestamp('last_used')->nullable();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('password_resets');
    }
};




