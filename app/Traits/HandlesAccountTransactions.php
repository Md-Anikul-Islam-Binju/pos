<?php

namespace App\Traits;

use App\Jobs\UpdateAccountBalanceJob;
use App\Models\AccountTransaction;
use App\Models\AccountTransfer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;


trait HandlesAccountTransactions
{
    public static function bootHandlesAccountTransactions(): void
    {
        static::updating(function ($model) {
            if (isset($model->account_id) && isset($model->amount)) {
                $model->handleAccountTransaction($model, $model->account_id, $model->amount);
            }
            elseif (isset($model->from_account_id) && isset($model->to_account_id) && isset($model->amount)) {
                $model->handleAccountTransaction($model,$model->from_account_id,$model->amount);
            }
        });

        static::deleting(function ($model) {
            // Handle deletion of transactions when an expense or transfer is deleted
            $model->handleTransactionDeletion($model);
        });
    }

    public function handleAccountTransaction($model, $accountId, $amount, $statusField = 'status'): void
    {
        $originalStatus = $model->getOriginal($statusField);
        $newStatus = $model->$statusField;

        // Check if the status change requires a transaction
        if ($this->statusChangeRequiresTransaction($originalStatus, $newStatus)) {
            // Check if it's a transfer
            if ($model instanceof AccountTransfer) {
                // Create the transaction for the 'out' account
                $this->createTransaction($model->from_account_id, $amount, 'out', $model);

                // Create the transaction for the 'in' account
                $this->createTransaction($model->to_account_id, $amount, 'in', $model);
            } else {
                // For other models (e.g., Expense), determine transaction type dynamically
                $transactionType = $model->getTransactionType();
                $this->createTransaction($accountId, $amount, $transactionType, $model);
            }
        }
    }

    protected function createTransaction($accountId, $amount, $transactionType, $model): void
    {
        // Create a reference specific to the model
        $previousStatus = $model->getOriginal('status'); // Get the previous status before the update
        $reference = sprintf(
            '%s status has been changed from "%s" to "%s" for the record with ID: %d.',
            class_basename($model), // This gets the model's class name without the namespace
            $previousStatus,        // Previous status of the model
            $model->status,         // Current status of the model after update
            $model->id              // ID of the model
        );

        // Get 'I' for In and 'O' for Out from $transactionType
        $transactionTypeLetter = strtoupper(substr($transactionType, 0, 1)); // Extract 'I' or 'O'

        // Get the first letter of the model name, e.g., 'E' for 'Expense'
        $modelLetter = strtoupper(substr(class_basename($model), 0, 1));

        // Generate a unique transaction ID in the desired format (TransactionType + ModelNameFirstLetter + Random)
        do {
            $transactionId = "T".$transactionTypeLetter . $modelLetter . Str::upper(Str::random(7));
        } while (AccountTransaction::where('transaction_id', $transactionId)->exists());

        // Create a new account transaction for every status change
        AccountTransaction::create([
            'account_id' => $accountId,
            'transaction_id' =>$transactionId,
            'transaction_type' => $transactionType,
            'amount' => $amount,
            'model' => get_class($model),
            'model_id' => $model->id,
            'reference' => $reference,
        ]);
        UpdateAccountBalanceJob::dispatch();
    }


    private function statusChangeRequiresTransaction($originalStatus, $newStatus): bool
    {
        if (($originalStatus === 'pending' || $originalStatus === 'rejected') && $newStatus === 'approved') {
            return true; // Create transaction for approval
        }

        if ($originalStatus === 'approved' && ($newStatus === 'pending' || $newStatus === 'rejected')) {
            return true; // Handle rejections or pending status
        }

        return false;
    }

    protected function handleTransactionDeletion($model): void
    {
        // Handle the deletion of associated transactions
        AccountTransaction::where('model_id', $model->id)
            ->where('model', get_class($model))
            ->delete();
    }
}
