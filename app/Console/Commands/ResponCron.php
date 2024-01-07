<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\BarangDetail;
use App\Models\WaitingList;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Laravel\Facades\Telegram;

class ResponCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'respon:cron';

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

        foreach ($overdueItems as $item) {
            $waiting = WaitingList::where('barang_id', $item->barang_id)
                ->whereNotNull('notif_date')
                ->whereNull('respon_user')
                ->where('kadaluarsa', '<=', $now)
                ->orderBy('created_at', 'ASC')
                ->first();

            if ($waiting) {
                Telegram::sendMessage([
                    'chat_id' => $waiting->user->telegram_id,
                    'parse_mode' => 'HTML',
                    'text' => 'Mohon maaf. Data waitinglist anda untuk barang ' .$waiting->barang->nama. ' kami hapus karena kamu tak kunjung memberi respon'
                ]);
                $waiting->delete();
            }
        }
        Log::info("resppon!");
    }
}
