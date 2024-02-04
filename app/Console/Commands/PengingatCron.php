<?php

namespace App\Console\Commands;

use App\Models\BarangDetail;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\WaitingList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

use Telegram\Bot\Laravel\Facades\Telegram;

class PengingatCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pengingat:cron';

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

        $jam = now()->subMinute(60)->format('Y-m-d H:i'); // Format tanggal dan jam 

        $barang_detail = BarangDetail::where('status_sewa', 1)
            ->whereNotNull('penyewa')
            ->whereRaw("DATE_FORMAT(kembali, '%Y-%m-%d %H:%i') = ?", [$jam])
            ->get();

            if($barang_detail){
                foreach ($barang_detail as $item) {

                    $e = User::find($item->penyewa);
        
                    Telegram::sendMessage([
                        'chat_id' => $e->telegram_id,
                        'parse_mode' => 'HTML',
                        'text' => 'Halo, ' . $e->name . ' segera lakukan pengembalian barang ' . $item->barang->nama . ' sebelum ' .  tgl($item->kembali, 'DD,MMMM Y \j\a\m H:mm') . ' Terimaksih',
                    ]);
        
        
                    Log::info("penginagt 1 oke fine!" .$e->name);
                }
            }

        Log::info("pengingat oke!");
    }
}
