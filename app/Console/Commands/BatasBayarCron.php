<?php

namespace App\Console\Commands;

use App\Models\BarangDetail;
use App\Models\Pesanan;
use App\Models\WaitingList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use Telegram\Bot\Laravel\Facades\Telegram;

class BatasBayarCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'batasbayar:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()

    {
        $now = Carbon::now();
        $no = Carbon::now();

        // $jam = $now->addHour(1); // jam 

        $jam = $now->subMinute(2); // mnt

        $blm_bayar = Pesanan::where('status', 0)
        ->where('mulai', '<=', $jam)
        ->whereNull('tipe_bayar')->get();

        foreach ($blm_bayar as $item) {
           
            Telegram::sendMessage([
                'chat_id' => $item->user->telegram_id,
                'parse_mode' => 'HTML',
                'text' => 'Mohon maaf, ada 1 pesananmu kami hapus dari list. kareana kamu tak kunjung melakukan pembayaran',
            ]);

            $item->barangDetail->update([
                'penyewa' => NUll,
                'mulai' => NUll , 
                'kembali' => NULL,
                'status_sewa' => 0,
            ]
            );

            $item->delete();
           
            Log::info("looping Cron is working fine!");
            
        }
        Log::info("Cron is working fine!");
    }
}
