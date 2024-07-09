<?php

namespace App\Listeners;

use App\Events\CardChosen;

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
     */
    public function handle(CardChosen $event): void
    {
        //
    }
}
