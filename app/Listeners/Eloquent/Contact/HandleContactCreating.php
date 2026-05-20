<?php

namespace App\Listeners\Eloquent\Contact;

use App\Models\Contact;

class HandleContactCreating
{
    /**
     * Handle the event.
     */
    public function handle(Contact $contact): void
    {
        $contact->users()->attach(auth()->id());
    }
}
