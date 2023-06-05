<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table( \App\Models\Fund::TABLE, function (Blueprint $table) {
            $table->foreignIdFor( \App\Models\FundManager::class)->constrained()->restrictOnDelete();
        });

        Schema::table( \App\Models\FundAlias::TABLE, function (Blueprint $table) {
            $table->foreignIdFor( \App\Models\Fund::class)->constrained()->cascadeOnDelete();
        });

        Schema::table('company_fund', function (Blueprint $table) {
            $table->foreignIdFor( \App\Models\Fund::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor( \App\Models\Company::class)->constrained()->cascadeOnDelete();
        });

        Schema::table('fund_duplicates_log', function (Blueprint $table) {
            $table->foreignIdFor( \App\Models\Fund::class)->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table( \App\Models\Fund::TABLE, function (Blueprint $table) {
            $table->dropForeign(['fund_manager_id']);
        });

        Schema::table( \App\Models\FundAlias::TABLE, function (Blueprint $table) {
            $table->dropForeign(['fund_id']);
        });

        Schema::table('company_fund', function (Blueprint $table) {
            $table->dropForeign(['fund_id']);
            $table->dropForeign(['company_id']);
        });

        Schema::table('fund_duplicates_log', function (Blueprint $table) {
            $table->dropForeign(['fund_id']);
        });
    }
}
