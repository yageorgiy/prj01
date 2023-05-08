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
        Schema::table("events", function (Blueprint $table) {
            $table->dropColumn("event_name");
            $table->bigInteger("event_type_id")->unsigned();

            $table
                ->foreign("event_type_id")
                ->references("id")
                ->on("event_types")
                ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("events", function (Blueprint $table) {
            $table->text("event_name");
            $table->removeColumn("event_type_id");
        });
    }
};
