<?php

namespace App\Listeners;

use App\Events\DuplicateFundWarning;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogPossibleFundDuplicates
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DuplicateFundWarning $event): void
    {
        Log::warning('Possible duplicated fund created', [
            'duplicates' => $event->duplicates,
        ]);

        DB::table('fund_duplicates_log')->insert([
            'fund_id' => $event->fund->id,
            'duplicates' => json_encode($event->duplicates),
        ]);
    }
}
