<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangDetail;
use Illuminate\Http\Request;
use Telegram\Bot\FileUpload\InputFile;
use Illuminate\Support\Carbon;

use Telegram\Bot\Laravel\Facades\Telegram;
use GuzzleHttp\Client;
use Telegram\Bot\Api;
use App\Models\ChatId;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\WaitingList;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class TelegramController extends Controller
{

    // dokumentasi inline btn

    // $botToken = 'YOUR_BOT_TOKEN'; // Ganti dengan token akses bot Anda
    //     $chatId = '5881233108'; // Ganti dengan ID chat yang sesuai

    //     $messageText = 'Pilih aksi:';
    //     $keyboard = [
    //         'keyboard' => [
    //             ['Button Pesan'],
    //             ['Button Rincian']
    //         ],
    //         'resize_keyboard' => true,
    //         'one_time_keyboard' => true
    //     ];

    //     $sendMessageParams = [
    //         'chat_id' => $chatId,
    //         'text' => $messageText,
    //         'reply_markup' => json_encode($keyboard)
    //     ];


    //     Telegram::sendMessage($sendMessageParams);
    public function sendMessage($id = 5881233108)
    {
        // dd(1);
        Telegram::sendMessage([
            'chat_id' => '5237463607',
            'parse_mode' => 'HTML',
            'text' => 'lov yu dek. wkkwkwwk'
        ]);
    }

    function messages()
    {
        return Telegram::getUpdates();
    }
    function setWebhook()
    {
        $url = 'https://1587-114-142-168-2.ngrok-free.app ';
        dd(Telegram::setWebhook([
            'url' => $url . '/telegram/webhook/' . env('TELEGRAM_BOT_TOKEN')
        ]));
        // return ['message' => 'sukses'];
    }
    public function webhook(Request $request)
    {
        $data = $request->all();
        // Periksa apakah ada pesan yang diterima
        if (isset($data['message'])) {
            $message = $data['message'];

            // Dapatkan informasi pesan
            $chatId = $message['chat']['id'];

            $keyboard = null;
            $first = $message['from']['first_name'];
            if (isset($message['from']['username'])) {
                $username = $message['from']['username'];
            } else {
                $username = '-';
            }
            // $username = $message['from']['username'] ?: '-';
            $text = $message['text'];

            $now = Carbon::now();

            // Lakukan logika sesuai dengan pesan yang diterima
            // Misalnya, jika pesan adalah '/start', balas dengan pesan selamat datang
            if ($text === '/start') {
                $responseText = 'Halo! Bro/Sist '  . $username . '. Selamat datang di Gading Adventure.';
            } elseif ($text === '/copyform') {
                $responseText = 'Kode Barang: ' . "\n";
                $responseText .= 'Tgl/Jam Sewa: ' . "\n";
                $responseText .= 'Jumlah Hari Sewa' . "\n";
            } elseif (strpos($text, '/wt_') !== false) {
                // } elseif (strpos($text, 'tg://_') !== false) {

                $tx = explode("_", $text);
                $c = count($tx);
                $dt = User::where('telegram_id', $chatId)->first();
                if ($dt == null) {
                    $responseText = 'Maaf anda belum terdaftar di sistem kami. ketikan atau klik /registrasi untuk melakukan pendaftaran di sistem kami. Registrasi adalah langkah pertama untuk bisa melakukan pemesanan melalui telegram bot Gading Adventure' . "\n";
                } else {
                    if ($c == 2) {
                        $d = base64_decode($tx[1] . '==');
                        $ex  = explode("_", $d);
                        if (strpos($text, $tx[0]) !== false && strpos($text, $tx[1]) !== false) {
                            try {

                                $barang = Barang::where('kode_barang', $ex[0])->first();
                                $t = Carbon::parse($ex[1]);
                                $t->addDays($ex[2]);

                                $k = $t->format('Y-m-d H:i:s');

                                if ($barang != null) {
                                    $data = new WaitingList();
                                    $data->user_id = $dt->id;
                                    $data->barang_id = $barang->id;
                                    $data->mulai = $ex[1];
                                    $data->kembali = $k;
                                    $data->barang_id = $barang->id;
                                    $data->save();


                                    $responseText = 'Terimakasih berikut adalah rincian barang anda :' . "\n";
                                    $responseText .= 'Kode: ' . $barang->kode_barang . "\n";
                                    $responseText .= 'Nama Barang: ' . $barang->nama . "\n";
                                    $responseText .= 'Mulai Sewa: ' . tgl($data->mulai) . "\n";
                                    $responseText .= 'Kembali: ' . tgl($data->kembali) . "\n";
                                    $responseText .= "Terimakasih ya. Kalian harus sabar. Nanti admin kabari lagi kalo barangnya ready di telegram bot ini ya." . "\n";
                                } else {
                                    $responseText = "Barang dengan kode $tx[1] tidak ditemukan. Silakan periksa kembali kode barang yang Anda masukkan.";
                                }
                            } catch (Exception $e) {
                                $responseText = 'Tanggal anda salah ya' . "\n";
                            }
                        }
                    } else {
                        $responseText = 'Format yang anda masukkan salah. Pastikan sudah sesuai dengan format yang telah di tentukan';
                    }
                }
            } elseif ($text === '/registrasi') {

                $dt = User::where('telegram_id', $chatId)->first();
                if ($dt == null) {
                    $responseText = 'Masukkan Nama dan Email Anda:' . "\n";
                    $responseText .= 'Nama:' . "\n";
                    $responseText .= 'Email:';
                } else {
                    $responseText = 'Anda sudah terdaftar di sistem kami, tidak perlu lagi melakukan registrasi. Anda dapat melihat informasi profile anda dengan klik atau mengetikkan /profil' . "\n";
                }

                // Tambahkan langkah berikutnya untuk menangkap input pengguna setelah perintah /registrasi
            } elseif (strpos($text, 'Nama:') !== false || strpos($text, 'Email:') !== false) {
                $nama = null;
                $email = null;

                // Pemisahan teks menjadi baris-baris
                $lines = explode("\n", $text);

                // Iterasi setiap baris untuk menangkap nilai nama dan email
                foreach ($lines as $line) {
                    if (strpos($line, 'Nama:') !== false) {
                        $nama = trim(str_replace('Nama:', '', $line));
                    } elseif (strpos($line, 'Email:') !== false) {
                        $email = trim(str_replace('Email:', '', $line));
                    }
                }

                if ($nama && $email) {

                    $dt = User::where('email', $email)->first();
                    if ($dt) {
                        // Email sudah ada dalam database
                        $responseText = 'Maaf, email yang Anda masukkan sudah terdaftar.';
                    } else {
                        // Email belum ada dalam database, data baru dapat disimpan
                        $user = new User();
                        $user->name = $nama;
                        $user->email = $email;
                        $user->password = bcrypt('123');
                        $user->telegram_id = $chatId;
                        $user->save();

                        $responseText = 'Terima kasih, data Anda telah berhasil disimpan!';
                    }
                } else {
                    $responseText = "Proses registrasi anda Gagal Pastikan anda menginputkan dengan format yang benar";
                }
            } elseif (strpos($text, 'Kode Barang:') !== false || strpos($text, 'Tgl/Jam Sewa:') !== false || strpos($text, 'Jumlah Hari Sewa:') !== false) {
                $kode = null;
                $tgl = null;
                $hari = null;

                // Pemisahan teks menjadi baris-baris
                $lines = explode("\n", $text);

                // Iterasi setiap baris untuk menangkap nilai nama dan email
                foreach ($lines as $line) {
                    if (strpos($line, 'Kode Barang:') !== false) {
                        $kode = trim(str_replace('Kode Barang:', '', $line));
                    } elseif (strpos($line, 'Tgl/Jam Sewa:') !== false) {
                        $tgl = trim(str_replace('Tgl/Jam Sewa:', '', $line));
                    } elseif (strpos($line, 'Jumlah Hari Sewa:') !== false) {
                        $hari = trim(str_replace('Jumlah Hari Sewa:', '', $line));
                    }
                }

                if ($kode && $tgl && $hari) {
                    try {
                        $t = Carbon::parse($tgl . ':00');
                        // Menambahkan hari
                        $t->addDays($hari);
                        // Mengambil hasil tanggal setelah ditambahkan hari
                        $k = $t->format('Y-m-d H:i:s');
                        $dt = User::where('telegram_id', $chatId)->first();
                        if ($dt == null) {
                            $responseText = 'Maaf anda belum terdaftar di sistem kami. ketikan atau klik /registrasi untuk melakukan pendaftaran di sistem kami. Registrasi adalah langkah pertama untuk bisa melakukan pemesanan melalui telegram bot Gading Adventure' . "\n";
                        } else {
                            $barang = Barang::where('kode_barang', $kode)->first();
                            if ($barang) {
                                $bd = BarangDetail::where([
                                    'barang_id' => $barang->id,
                                    'status_sewa' => 0,
                                ])->orderBy('id', 'ASC')->first();

                                if ($bd) {
                                    $bd->update([
                                        'mulai' => $tgl . ':00',
                                        'status_sewa' => 1,
                                        'kembali' => $k,
                                        'penyewa' => $dt->id,
                                    ]);
                                    $responseText = 'Data Penyewaan berhasil di tambahkan berikut adalah informasi sewa anda' . "\n";
                                    $responseText .= "\n";
                                    $responseText .= "Nama Barang: $barang->nama\n";
                                    $responseText .= "Kode Barang: $barang->kode_barang\n";
                                    $responseText .= "Mulai sewa: $tgl\n";
                                    $link = 'https://1587-114-142-168-2.ngrok-free.app/dashboard/pembayaran/create?brg_dtl=7';

                                    $responseText .= "Anda dapat segera melakukan pembayaran melalui link berikut ini " . $link . "\n";


                                    $responseText .= "\n";
                                } else {
                                    $br = BarangDetail::where([
                                        'barang_id' => $barang->id,
                                        'status_sewa' => 1,
                                    ])->orderBy('kembali', 'ASC')->first();

                                    $tgl_a = $tgl . ':00';
                                    $e = $barang->kode_barang . '_' . $tgl_a . '_' . $hari;

                                    $ee = base64_encode($e);

                                    $r = str_replace('=', '', $ee);

                                    $link = "/wt_" . $r;
                                    $responseText = "Yahh........., barang yang kamu inginkan saat ini sedang sedang full booked. Ada 1 barang yang paling dekat ready di tanggal " . tgl($br->kembali) . ". Gimana? kalau masih minat dengan barang ini kamu bisa klik link berikut agar di daftarkan di data waitinglist oleh admin" . "\n" . $link . "\n" . "nanti admin kabari kalo barangnya ready";
                                }
                            } else {
                                $responseText = 'Format yang anda masukkan salah . kode barang ' . $kode . 'tidak di temukan' . "\n";
                            }
                        }


                        // Validate the value...
                    } catch (Exception $e) {
                        $responseText = 'Tanggal anda salah ya' . "\n";
                    }
                } else {
                    $responseText = "Proses registrasi anda Gagal Pastikan anda menginputkan dengan format yang benar";
                }
            } elseif ($text === '/catalog') {
                $barang = Barang::where('status', 1)->get();
                $responseText = "Berikut adalah data barang yang dapat Anda pesan:\n";
                $responseText .= "\n";
                $i = 1;
                foreach ($barang as $item) {
                    $x = $i++;
                    $namaBarang = Str::upper($item->nama);
                    $kodeBarang = $item->kode_barang;
                    $hargaBarang = $item->harga_sewa;

                    $stokAwal = $item->barangDetail->count() . ' item';
                    $stokReady = $item->barangReady() . ' item';
                    $stokDisewa = $item->barangDisewa() . ' item';

                    $responseText .= " ### Barang $x ### " . "\n";
                    $responseText .= "|- Nama Barang : " . $namaBarang .   "\n";
                    $responseText .= "|- Kode Barang : " . $kodeBarang . "\n";
                    $responseText .= "|- Harga : Rp." . $hargaBarang . "\n";
                    $responseText .= "|- Jumlah Barang : " . $stokAwal . "\n";
                    $responseText .= "|- Barang Ready : " . $stokReady . "\n";
                    $responseText .= "|- Barang Disewa : " . $stokDisewa . "\n";
                    $responseText .= "|- Order ? : " . "/pesan_" . $kodeBarang . "\n";
                    // $responseText .= "\n";

                    $w = WaitingList::where('barang_id', $item->id)->get();
                    $nama = [];
                    foreach ($w as $y) {
                        $nama[] = $y->user->name;
                    }


                    if ($nama) $responseText .= "|- Daftar Waiting List : " . implode(', ', $nama) . "\n";
                    $responseText .= "\n";
                }
                $responseText .= "Masukkan dengan format: /pesan KODE_BARANG untuk melakukan pemesanan";
            } elseif ($text === '/profil') {
                $dt = User::where('telegram_id', $chatId)->first();
                if ($dt == null) {
                    $responseText = 'Maaf anda belum terdaftar di sistem kami. ketikan atau klik /registrasi untuk melakukan pendaftaran di sistem kami. Registrasi adalah langkah pertama untuk bisa melakukan pemesanan melalui telegram bot Gading Adventure' . "\n";
                } else {


                    $responseText = " ### Profil ### " . "\n";
                    $responseText .= "|- ID Pengguna : " . $chatId . "\n";
                    $responseText .= "|- Nama : " . $first .   "\n";
                    $responseText .= "|- email : " . $dt->email . "\n";
                    $responseText .= "|- username : " . $username . "\n";
                    $responseText .= "|- Status : " . 'aktif' . "\n";
                    $responseText .= "|- Bergabung sejak : " . tgl($dt->created_at) . "\n";
                }
            } elseif (strpos($text, '/pesan_') !== false) {

                $tx = explode("_", $text);
                $c = count($tx);
                $dt = User::where('telegram_id', $chatId)->first();
                if ($dt == null) {
                    $responseText = 'Maaf anda belum terdaftar di sistem kami. ketikan atau klik /registrasi untuk melakukan pendaftaran di sistem kami. Registrasi adalah langkah pertama untuk bisa melakukan pemesanan melalui telegram bot Gading Adventure' . "\n";
                } else {
                    if ($c == 2) {
                        if (strpos($text, $tx[0]) !== false && strpos($text, $tx[1]) !== false) {
                            $barang = Barang::where('kode_barang', $tx[1])->first();

                            if ($barang != null) {

                                $tmp = [
                                    'nama' => $barang->nama,
                                    'kode' => $barang->kode_barang,
                                ];

                                Cache::put('temp_order', $tmp, now()->addMinutes(5));

                                $responseText = "Anda telah memilih barang dengan kode $barang->kode_barang barang anda adalah $barang->nama.\n Silakan melanjutkan langkah berikutnya " . "\n";

                                $currentDate = Carbon::now();

                                $key = [];
                                for ($i = 1; $i <= 3; $i++) { // Ganti 3 dengan jumlah hari yang Anda inginkan
                                    $date = $currentDate->addDays()->format('d-m-Y');
                                    $key[] = [['text' => $date, 'callback_data' => 'select_date_' . $date]];
                                }

                                // Mengirim inline keyboard dengan tombol tanggal
                                $keyboard = [
                                    'inline_keyboard' => $key,
                                ];
                            } else {
                                $responseText = "Barang dengan kode $tx[1] tidak ditemukan. Silakan periksa kembali kode barang yang Anda masukkan.";
                            }
                        }
                    } else {
                        $responseText = 'Format yang anda masukkan salah. Pastikan sudah sesuai dengan format yang telah di tentukan';
                    }
                }
            } elseif (strpos($text, '/JADIPESAN_') !== false) {

                try {
                    $responseText = 'ada kok';
                } catch (\Throwable $th) {
                    $responseText = 'ga kok';
                }
                // $tx = explode("_", $text);
                // $c = count($tx);
                // $dt = User::where('telegram_id', $chatId)->first();



                // $wt = WaitingList::whereId($tx[1])->first();


                // try {
                //     if ($wt) {

                //         // $barang_detail = BarangDetail::where('waiting_id', $wt->id)->first()->update(['pembayaran' => 1]);
                //         $responseText = 'ada kok';

                //         // if ($barang_detail) {
                //         //     $responseText = "KONFIRMASI WAITING LIST 0000" . $barang_detail->id . "\n";
                //         //     $responseText .= "Kode Barang : " . $barang_detail->barang->kode_barang . "\n";
                //         //     $responseText .= "Nama Barang : "  . $barang_detail->barang->nama .   "\n";
                //         //     $responseText .= "Tanggal Sewa : " . Carbon::now() . "\n";
                //         //     $responseText .= "Jumlah Hari : ";
                //         // }
                //     }
                //     //code...
                // } catch (\Throwable $th) {
                //     //throw $th;
                //     $responseText = 'gak ada';
                // }


                // if(!$wt) $responseText = 'gak ada wt';
            } elseif (strpos($text, 'KONFIRMASI WAITING LIST 0000') !== false || strpos($text, 'Jumlah Hari :') !== false) {
                $barang_detail_id = null;
                $hari = null;

                $lines = explode("\n", $text);
                foreach ($lines as $line) {
                    if (strpos($line, 'KONFIRMASI WAITING LIST 0000') !== false) {
                        $barang_detail_id = trim(str_replace('KONFIRMASI WAITING LIST 0000', '', $line));
                    } elseif (strpos($line, 'Jumlah Hari :') !== false) {
                        $hari = trim(str_replace('Jumlah Hari : ', '', $line));
                    }
                }

                if ($hari && $barang_detail_id) {

                    $barang_dtl =  BarangDetail::whereId($barang_detail_id)->first();
                    $now = Carbon::now();


                    if ($barang_dtl) {

                        $waiting = WaitingList::find($barang_dtl->waiting_id);

                        if ($waiting) $waiting->delete();

                        $barang_dtl->update([
                            'mulai' => Carbon::now(),
                            'status_sewa' => 1,
                            'kembali' => $now->addDays($hari),
                            'waiting_id' => NULL,
                        ]);



                        $responseText = " ### Berikut adalah informasi barang yang anda pesan ### " . "\n";
                        $responseText .= "|- Kode Barang : " . $barang_dtl->barang->kode_barang ?? NULL . "\n";
                        $responseText .= "|- Nama Barang : "  . $barang_dtl->barang->nama ?? NULL .   "\n";
                        $responseText .= "|- Tanggal Sewa : " . $barang_dtl->mulai . "\n";
                        $responseText .= "|- Kembali Sewa : " . $barang_dtl->kembali . "\n";
                        $responseText .= "Terimaksih datamu berhasil disimpan";


                        $link = config('base.url') . '/dashboard/pembayaran/create?brg_dtl=' . $barang_dtl->id;

                        $responseText .= "Anda dapat segera melakukan pembayaran melalui link berikut ini " . $link . "\n";
                        $responseText .= "\n";
                    }
                    // $responseText = '.';
                    // res
                } else {
                    // Balas dengan pesan default jika perintah tidak dikenali
                    $responseText = 'Maaf, saya tidak mengenali perintah tersebut.';
                }

                // Kirim balasan ke pengguna menggunakan metode Telegram Bot API
                $this->sendTelegramMessage($chatId, $responseText, $keyboard);
            } elseif (isset($data['callback_query'])) {

                $this->handleCallbackQuery($data['callback_query']);
            }

            return response()->json(['status' => 'success']);
        }
    }


    //handle callback

    protected function handleCallbackQuery($callbackQuery)
    {
        $chatId = $callbackQuery['message']['chat']['id'];
        $callbackData = $callbackQuery['data'];

        if (strpos($callbackData, 'select_time_') === 0) {
            $selectedTime = substr($callbackData, 12);
            $tmp = Cache::get('temp_order');
            // Memproses pemilihan tanggal yang dipilih

            $tmp += ['jam' => $selectedTime];

            Cache::put('temp_order', $tmp, now()->addMinutes(5));

            // Lakukan tindakan berdasarkan tanggal yang dipilih

            $responseText = " ### Berikut adalah informasi sementara barang yang anda pesan ### " . "\n";
            $responseText .= "|- Kode Barang : " . $tmp['kode'] . "\n";
            $responseText .= "|- Nama Barang : "  . $tmp['nama'] .   "\n";
            $responseText .= "|- Tanggal Sewa : " . $tmp['tanggal'] . "\n";
            $responseText .= "|- Jam Sewa : " . $tmp['jam'] . "\n";
            $responseText .= "|- Hari : "  . "\n";
            $responseText .= "Lanjutkan dengan memilih jumlah hari Jam (1-5 hari).";

            $keyboard = [];
            for ($i = 1; $i <= 5; $i++) { // Ganti 3 dengan jumlah hari yang Anda inginkan
                $day = $i;
                $keyboard[] = [['text' => $day, 'callback_data' => 'select_day_' . $day]];
            }

            // Mengirim inline keyboard dengan tombol jam sewa
            $inlineKeyboard = [
                'inline_keyboard' => $keyboard,
            ];
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $responseText,
                'reply_markup' => json_encode($inlineKeyboard),
            ]);
        } elseif (strpos($callbackData, 'select_day_') === 0) {
            $selectedDay = substr($callbackData, 11);
            $tmp = Cache::get('temp_order');
            // Memproses pemilihan tanggal yang dipilih
            $tmp += ['hari' => $selectedDay];

            Cache::put('temp_order', $tmp, now()->addMinutes(5));

            // Lakukan tindakan berdasarkan tanggal yang dipilih

            $dt = User::where('telegram_id', $chatId)->first();
            $barang = Barang::where('kode_barang', $tmp['kode'])->first();
            if ($barang) {
                $bd = BarangDetail::where([
                    'barang_id' => $barang->id,
                    'status_sewa' => 0,
                ])->orderBy('id', 'ASC')->first();

                $dateString = $tmp['tanggal']; // Ganti dengan tanggal yang sesuai
                $originalDate = Carbon::createFromFormat('d-m-Y', $dateString);
                $newDateFormat = $originalDate->format('Y-m-d');

                $kembali = $originalDate->addDays($tmp['hari'])->format('Y-m-d');



                if ($bd) {
                    $bd->update([
                        'mulai' => $newDateFormat . ' ' . $tmp['jam'] . ':00',
                        'status_sewa' => 1,
                        'kembali' => $kembali . ' ' . $tmp['jam'] . ':00',
                        'penyewa' => $dt->id,
                    ]);

                    $responseText = " ### Berikut adalah informasi barang yang anda pesan ### " . "\n";
                    $responseText .= "|- Kode Barang : " . $tmp['kode'] . "\n";
                    $responseText .= "|- Nama Barang : "  . $tmp['nama'] .   "\n";
                    $responseText .= "|- Tanggal Sewa : " . $tmp['tanggal'] . "\n";
                    $responseText .= "|- Jam Sewa : " . $tmp['jam'] . "\n";
                    $responseText .= "|- Hari : " . $tmp['hari'] . "\n";
                    $responseText .= "Terimaksih datamu berhasil disimpan";


                    $link = 'https://1587-114-142-168-2.ngrok-free.app/dashboard/pembayaran/create?brg_dtl=7';

                    $responseText .= "Anda dapat segera melakukan pembayaran melalui link berikut ini " . $link . "\n";
                    $responseText .= "\n";
                } else {
                    $br = BarangDetail::where([
                        'barang_id' => $barang->id,
                        'status_sewa' => 1,
                    ])->orderBy('kembali', 'ASC')->first();

                    $tgl_a = $tgl . ':00';
                    $e = $barang->kode_barang . '_' . $tgl_a . '_' . $hari;

                    $ee = base64_encode($e);

                    $r = str_replace('=', '', $ee);

                    $link = "/wt_" . $r;
                    $responseText = "Yahh........., barang yang kamu inginkan saat ini sedang sedang full booked. Ada 1 barang yang paling dekat ready di tanggal " . tgl($br->kembali) . ". Gimana? kalau masih minat dengan barang ini kamu bisa klik link berikut agar di daftarkan di data waitinglist oleh admin" . "\n"  . "\n" . "nanti admin kabari kalo barangnya ready";
                }
            } else {
                $responseText = 'Format yang anda masukkan salah . kode barang '  . 'tidak di temukan' . "\n";
            }

            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $responseText,
            ]);
        } elseif (strpos($callbackData, 'select_date_') === 0) {
            // Memproses pemilihan jam sewa yang dipilih
            $selectedDate = substr($callbackData, 12); // Menghapus 'select_time_'

            // Lakukan tindakan berdasarkan jam sewa yang dipilih
            $responseText = "Anda telah memilih Tanggal sewa pada  $selectedDate. Silakan lanjutkan langkah berikutnya.";

            // Kirim tanggapan kepada Telegram (misalnya, menghapus keyboard inline)
            Telegram::answerCallbackQuery([
                'callback_query_id' => $callbackQuery['id'],
                'text' => $responseText,
            ]);

            $tmp = Cache::get('temp_order');
            // Memproses pemilihan tanggal yang dipilih

            $tmp += ['tanggal' => $selectedDate];

            Cache::put('temp_order', $tmp, now()->addMinutes(5));


            // Lakukan tindakan berdasarkan tanggal yang dipilih

            $responseText = " ### Berikut adalah informasi sementara barang yang anda pesan ### " . "\n";
            $responseText .= "|- Kode Barang : " . $tmp['kode'] . "\n";
            $responseText .= "|- Nama Barang : "  . $tmp['nama'] .   "\n";
            $responseText .= "|- Tanggal Sewa : " . $tmp['tanggal'] . "\n";
            $responseText .= "|- Jam Sewa : " . "\n";
            $responseText .= "Lanjutkan dengan memilih jam sewa. Jam (09 - 14).";

            $currentHour = 9; // Jam mulai
            $endHour = 16;    // Jam berakhir

            // Membuat inline keyboard dengan tombol jam sewa
            $keyboard = [];
            while ($currentHour <= $endHour) {
                $time = str_pad($currentHour, 2, '0', STR_PAD_LEFT) . ':00'; // Format jam
                $keyboard[] = [['text' => $time, 'callback_data' => 'select_time_' . $time]];
                $currentHour++;
            }

            // Mengirim inline keyboard dengan tombol jam sewa
            $inlineKeyboard = [
                'inline_keyboard' => $keyboard,
            ];
            Telegram::sendMessage([
                'chat_id' => $chatId,
                'text' => $responseText,
                'reply_markup' => json_encode($inlineKeyboard),
            ]);
        } else {
            // Callback data tidak dikenali
            $responseText = "Maaf, tindakan tidak dikenali.";
            Telegram::answerCallbackQuery([
                'callback_query_id' => $callbackQuery['id'],
                'text' => $responseText,
            ]);
        }
    }

    private function sendTelegramMessage($chatId, $text, $keyboard)
    {
        $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendMessage';
        $messageData = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($keyboard !== null) {
            $messageData['reply_markup'] = json_encode($keyboard);
        }

        Telegram::sendMessage($messageData);
    }
    private function sendTelegramMessage1($chatId, $text, $keyboard)
    {
        $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendMessage';
        $messageData = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        if ($keyboard !== null) {
            $messageData['reply_markup'] = json_encode($keyboard);
        }

        Telegram::sendMessage($messageData);
    }
}
