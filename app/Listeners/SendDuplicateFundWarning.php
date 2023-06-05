<?php

namespace App\Listeners;

use App\Events\DuplicateFundWarning;
use App\Events\FundCreatedOrUpdated;
use App\Models\Fund;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendDuplicateFundWarning
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
    public function handle(FundCreatedOrUpdated $event): void
    {
        $duplicates = Fund::findDuplicates($event->fund);

        if (count($duplicates) > 0) {
            DuplicateFundWarning::dispatch($event->fund, $duplicates);
        }
    }
}
