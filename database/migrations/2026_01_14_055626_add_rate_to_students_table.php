<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->decimal('rate', 8, 2)
                ->nullable()
                ->after('name'); // adjust column position if needed
        });
    }

    public function down(): void
    {

    }
};
