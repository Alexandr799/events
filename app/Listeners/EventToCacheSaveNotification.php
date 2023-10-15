<?php

namespace App\Listeners;

use App\Events\EventToCacheSave;
use App\Jobs\CacheSaveJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class EventToCacheSaveNotification implements ShouldQueue
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
    public function handle(EventToCacheSave $eventToCacheSave): void
    {
        $id = $eventToCacheSave->id;
        $event = unserialize($eventToCacheSave->event);
        $eventDate = new Carbon($event->date);
        $currentDate = now();
        $period = $currentDate->diffInDays($eventDate);

        if (abs($period) < 31) {
            $periodType = 'день';
        } elseif (abs($period) <= 365) {
            $period = floor($period / 12);
            $periodType = 'месяц';
        } else {
            $period = floor($period / 365);
            $periodType = 'год';
        }

        $event->period =  $period * ($currentDate->isAfter($eventDate) ? -1 : 1);
        $event->period_type = $periodType;
        Cache::put($id, $event, now()->addMinutes(5));
    }
}
