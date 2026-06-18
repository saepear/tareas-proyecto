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
        if (Schema::hasColumn('users', 'nombre')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('nombre', 'first_name');
                $table->renameColumn('apellido', 'last_name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'first_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->renameColumn('first_name', 'nombre');
                $table->renameColumn('last_name', 'apellido');
            });
        }
    }
};
