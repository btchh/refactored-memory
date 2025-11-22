<?php

namespace App\Observers;

use App\Models\Transaction;

class TransactionObserver
{
    /**
     * Handle the Transaction "saved" event.
     * This fires after the transaction is saved to the database,
     * ensuring that relationships are available for calculation.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return void
     */
    public function saved(Transaction $transaction): void
    {
        // Store the current total to check if it changed
        $oldTotal = $transaction->total_price;
        
        // Calculate the new total using the model's method
        $transaction->calculateTotalPrice();
        
        // Only save if the total has changed to avoid infinite loop
        if ($transaction->total_price !== $oldTotal) {
            $transaction->saveQuietly(); // Save without triggering events
        }
    }
}
