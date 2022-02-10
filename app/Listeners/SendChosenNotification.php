<?php

namespace App\Listeners;

use App\Events\CardChosen;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendChosenNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\CardChosen  $event
     * @return void
     */
    public function handle(CardChosen $event)
    {
        //
    }
}
