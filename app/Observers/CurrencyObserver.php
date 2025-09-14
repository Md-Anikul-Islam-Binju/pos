<?php

namespace App\Observers;

use App\Models\Currency;

class CurrencyObserver
{
    /**
     * Handle the "saving" event.
     */
    public function saving(Currency $currency): void
    {
        // Ensure that only one currency can be set as default
        if ($currency->is_default) {
            // Ensure the status is true if is_default is true
            $currency->status = true;

            // Unset the default flag for all other currencies
            Currency::where('is_default', true)
                ->where('id', '!=', $currency->id)
                ->update(['is_default' => false]);
        }

        // Ensure there's always one default currency with status true
        $defaultCurrencyCount = Currency::where('is_default', true)->count();
        if ($defaultCurrencyCount === 0 && !$currency->is_default) {
            // If no default currency exists, mark this one as default
            $currency->is_default = true;
            $currency->status = true;
        }
    }

    /**
     * Prevent deletion or deactivation of the default currency.
     */
    public function updating(Currency $currency): void
    {
        // Prevent deactivating the current default currency
        if ($currency->is_default && $currency->status === false) {
            throw new \Exception('Cannot deactivate the default currency.');
        }
    }

    /**
     * Handle the "deleting" event.
     */
    public function deleting(Currency $currency): void
    {
        // Prevent deletion of the default currency
        if ($currency->is_default) {
            throw new \Exception('Cannot delete the default currency.');
        }
    }
}
