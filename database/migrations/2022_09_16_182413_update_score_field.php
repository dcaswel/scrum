<?php

use App\Models\Guideline;
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
        Schema::table('guidelines', fn (Blueprint $table) => $table->string('score')->change());
        /** Getting rid of any unneeded decimals */
        Guideline::all()
            ->each(fn (Guideline $guideline) => $guideline->update(['score' => (string) (float) $guideline->score]));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guidelines', fn (Blueprint $table) => $table->decimal('score', 8, 1)
            ->change());
    }
};
