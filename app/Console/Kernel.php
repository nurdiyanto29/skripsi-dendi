<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
 
    protected $commands = [

        Commands\WaitingCron::class,
        Commands\ResponCron::class,
        Commands\BatasBayarCron::class,

    ];

    /**

     * Define the application's command schedule.

     *

     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule

     * @return void

     */

    protected function schedule(Schedule $schedule)

    {

        $schedule->command('respon:cron')->everyMinute();
        $schedule->command('waiting:cron')->everyMinute();
        $schedule->command('batasbayar:cron')->everyMinute();

    }

     

    /**

     * Register the commands for the application.

     *

     * @return void

     */



    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
