<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\App;

/**
 * Trait to temporarily switch app locale to the email recipient's preferred language.
 *
 * Usage:
 *   $this->withRecipientLocale($recipient, function () { ... });
 *
 * The locale is always restored after the callback, even if an exception occurs.
 */
trait LocaleSwitchable
{
    /**
     * Execute a callback using the recipient's preferred locale,
     * then restore the original locale.
     */
    protected function withRecipientLocale(User $recipient, callable $callback): mixed
    {
        $originalLocale = App::getLocale();
        App::setLocale($recipient->locale ?? 'id');

        try {
            return $callback();
        } finally {
            App::setLocale($originalLocale);
        }
    }
}
