<?php

namespace App\Listeners;

use App\Events\WhatsAppMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendWhatsAppNotification
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
     * @param  \App\Events\WhatsAppMessage  $event
     * @return void
     */
    public function handle(WhatsAppMessage $event)
    {
        //
    }
}
