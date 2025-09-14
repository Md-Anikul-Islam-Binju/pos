<?php

namespace App\Console;

use App\Jobs\DeleteOldCronJobLogs;
use App\Jobs\UpdateAccountBalanceJob;
use App\Jobs\UpdateCustomerBalance;
use App\Jobs\UpdateProductionHouseBalance;
use App\Jobs\UpdateProductSellPricesJob;
use App\Jobs\UpdateSupplierBalance;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('queue:work')->withoutOverlapping();
        // Log before and after the UpdateAccountBalanceJob
        $schedule->job(new UpdateAccountBalanceJob())
            ->everyMinute() // Adjust as needed
            ->before(function () {
                // $this->logCronJob('UpdateAccountBalanceJob', 'initiated');
            })
            ->after(function () {
                // $this->logCronJob('UpdateAccountBalanceJob', 'completed');
            });

        // Log before and after the UpdateProductSellPricesJob
        $schedule->job(new UpdateProductSellPricesJob())
            ->everyMinute()
            ->before(function () {
                // $this->logCronJob('UpdateProductSellPricesJob', 'initiated');
            })
            ->after(function () {
                // $this->logCronJob('UpdateProductSellPricesJob', 'completed');
            });

        $schedule->job(new UpdateCustomerBalance())
            ->everyMinute()
            ->before(function () {
                // $this->logCronJob('UpdateCustomerBalance', 'initiated');
            })
            ->after(function () {
                // $this->logCronJob('UpdateCustomerBalance', 'completed');
            });

        $schedule->job(new UpdateSupplierBalance())
            ->everyMinute()
            ->before(function () {
                // $this->logCronJob('UpdateSupplierBalance', 'initiated');
            })
            ->after(function () {
                // $this->logCronJob('UpdateSupplierBalance', 'completed');
            });

        $schedule->job(new UpdateProductionHouseBalance())
            ->hourly()
            ->before(function () {
                // $this->logCronJob('UpdateProductionHouseBalance', 'initiated');
            })
            ->after(function () {
                // $this->logCronJob('UpdateProductionHouseBalance', 'completed');
            });

        $schedule->job(new DeleteOldCronJobLogs())
            ->hourly()
            ->before(function () {
                // $this->logCronJob('DeleteOldCronJobLogs', 'initiated');
            })
            ->after(function () {
                // $this->logCronJob('DeleteOldCronJobLogs', 'completed');
            });
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
