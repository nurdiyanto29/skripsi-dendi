<?php

namespace App\Console\Commands;

use App\Models\BarangDetail;
use App\Models\WaitingList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use Telegram\Bot\Laravel\Facades\Telegram;

class WaitingCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waiting:cron';

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

        $add = $no->addHour(1);
        $overdueItems = BarangDetail::where('kembali', '<=', $now)->get();
        // dd($overdueItems);
        Log::info("barang waiting" . $overdueItems);


        // dd($overdueItems);
        // $item->update([
        //     'mulai' => NULL,
        //     'kembali' => NULL,
        //     'status_sewa' => 0,
        // ]);

        foreach ($overdueItems as $item) {

            $waiting = WaitingList::where('barang_id',$item->barang_id)->orderBy('created_at', 'ASC')->first();
            Log::info("si waiting" . $item->barang_id);

            if($waiting->notif_date == null){
                //nila notif terkirim kan awalnya null, nah setelah di kirim maka akan ada isi nya. berarti harus mencari waiting 
                //list yang gak ada null nya dan harus ada waiting id nya di barang detail sekarang lagi ngantrrin siapa gitu anjeng
                Telegram::sendMessage([
                    'chat_id' => $waiting->user->telegram_id,
                    'parse_mode' => 'HTML',
                    'text' => ' Halo ' . $waiting->user->name . ' Barang ' . $item->barang->nama . ' Sudah tersedia. Jika kamu serius untuk melanjutkan pemesanan kamu bisa klik /JADIPESAN_' . $waiting->id . ' jika tak kunjung ada respon selama 1 jam setelah chat ini dikirim maka datamu di waiting list akan terhapus dan akan di lempar ke pelanggang yang lain'
                ]);
    
                $waiting->update([
                    'notif_date' => $now,
                    'kadaluarsa' => $add
                ]);
    
                $item->update([
                    'waiting_id' => $waiting->id
                ]);
            }





        }
        Log::info("Cron is working fine!" . $waiting);
    }
}
