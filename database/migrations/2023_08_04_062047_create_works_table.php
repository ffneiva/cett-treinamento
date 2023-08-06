<?php

use App\Models\Institution;
use App\Models\Researcher;
use App\Models\Work;
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
        Schema::create('works', function (Blueprint $table) {
            $table->id();
            $table->string('thumb')->nullable();
            $table->string('title');
            $table->date('publication_date');
            $table->foreignIdFor(Institution::class)->constrained();
            $table->string('file')->nullable();
            $table->integer('grade');
            $table->string('subject');
            $table->string('type');
            $table->string('language');
            $table->string('license');
            $table->timestamps();
        });

        Schema::create('work_researchers', function (Blueprint $table) {
            $table->foreignIdFor(Work::class)->constrained();
            $table->foreignIdFor(Researcher::class)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('works');
        Schema::dropIfExists('work_researchers');
    }
};
