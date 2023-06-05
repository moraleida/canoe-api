<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models;

class PerformanceSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $i = 0;
        while ($i < 300) {
            Models\FundManager::factory($i)->create();
            $companies = Models\Company::factory($i)->create();
            Models\Fund::factory(2*$i)->hasAttached($companies)->create();
            Models\FundAlias::factory(3*$i)->create();
            $i++;
        }
    }
}
