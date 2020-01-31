<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPagesTableAddTestingCounterpartId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('pages', static function (Blueprint $table) {
            $table->unsignedInteger('testing_a_id')->nullable();
            $table->foreign('testing_a_id', 'pages_fk_testing_a_id')
                ->references('id')
                ->on('pages')
                ->onUpdate('cascade')->onDelete('set null');

            $table->unsignedInteger('testing_b_id')->nullable();
            $table->foreign('testing_b_id', 'pages_fk_testing_b_id')
                ->references('id')
                ->on('pages')
                ->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('pages', static function (Blueprint $table) {
            $table->dropForeign('pages_fk_testing_a_id');
            $table->dropColumn(['testing_a_id']);

            $table->dropForeign('pages_fk_testing_b_id');
            $table->dropColumn(['testing_b_id']);
        });
    }
}
