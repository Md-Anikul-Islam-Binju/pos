<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\AccountTransaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateAccountBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-account-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update account balances based on in/out transaction types';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        // Get all accounts
        $accounts = Account::all();

        foreach ($accounts as $account) {
            // Calculate the total incoming amount
            $totalIn = AccountTransaction::where('account_id', $account->id)
                ->where('transaction_type', 'in') // Assuming 'in' denotes deposits or income
                ->sum('amount');

            // Calculate the total outgoing amount
            $totalOut = AccountTransaction::where('account_id', $account->id)
                ->where('transaction_type', 'out') // Assuming 'out' denotes withdrawals or expenses
                ->sum('amount');

            // Update the account balance
            $account->balance = $totalIn - $totalOut;
            $account->update();

            $this->info("Updated balance for Account ID {$account->id}: {$account->balance}");
            Log::info('Account balances updated successfully.');
        }

        $this->info('All account balances updated successfully.');
    }
}
