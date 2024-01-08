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
use App\Models\Pesanan;

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
            // 'chat_id' => '5237463607',
            'chat_id' => '5881233108',
            'parse_mode' => 'HTML',
            'text' => 'lov yu dek. wkkwkwwk'
        ]);
    }

    public function resetAllChats(Request $request)
    {
        // Ambil token bot dari konfigurasi
        $botToken = config('telegram.token');

        // Dapatkan daftar obrolan
        $response = Http::get("https://api.telegram.org/bot{$botToken}/getUpdates");
        $chats = $response->json('result');

        // Iterasi melalui daftar obrolan dan hapus pesan
        foreach ($chats as $chat) {
            $chatId = $chat['message']['chat']['id'];
            $messageId = $chat['message']['message_id'];

            $this->deleteMessage($chatId, $messageId);
        }

        return response()->json(['status' => 'success']);
    }

    private function deleteMessage($chatId, $messageId)
    {
        // Ambil token bot dari konfigurasi
        $botToken = config('telegram.token');

        // Hapus pesan menggunakan metode deleteMessage
        Http::get("https://api.telegram.org/bot{$botToken}/deleteMessage", [
            'chat_id' => $chatId,
            'message_id' => $messageId,
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
            $respon1 = null;
            $first = $message['from']['first_name'];
            if (isset($message['from']['username'])) {
                $username = $message['from']['username'];
            } else {
                $username = '-';
            }
            $text = $message['text'];

            $now = Carbon::now();

            if ($text === '/start') {
                $this->handleStart($chatId, $username);
            } elseif ($text === '/registrasi') {
                $this->handleRegistrasi($chatId, $username);
            } elseif (strpos($text, 'Nama:') !== false && strpos($text, 'Email:') !== false  && strpos($text, 'Alamat:') !== false  && strpos($text, 'Hp:') !== false  && strpos($text, 'Password:') !== false) {
                $nama = null;
                $email = null;
                $alamat = null;
                $hp = null;
                $password = null;

                // Pemisahan teks menjadi baris-baris
                $lines = explode("\n", $text);

                // Iterasi setiap baris untuk menangkap nilai nama dan email
                foreach ($lines as $line) {
                    if (strpos($line, 'Nama:') !== false) {
                        $nama = trim(str_replace('Nama:', '', $line));
                    } elseif (strpos($line, 'Email:') !== false) {
                        $email = trim(str_replace('Email:', '', $line));
                    } elseif (strpos($line, 'Alamat:') !== false) {
                        $alamat = trim(str_replace('Alamat:', '', $line));
                    } elseif (strpos($line, 'Hp:') !== false) {
                        $hp = trim(str_replace('Hp:', '', $line));
                    } elseif (strpos($line, 'Password:') !== false) {
                        $password = trim(str_replace('Password:', '', $line));
                    }
                }

                if ($nama && $email && $alamat && $hp && $password) {

                    $link = config('base.url') . '/login';
                    $dt = User::where('email', $email)->first();
                    if ($dt) {
                        $responseText = 'Saat ini emailmu sudah terdaftar di sistem kami silahkan login ' . $link . ' sesuai email dan password yang kamu daftarkan sebelumnya';
                    } else {
                       $x= User::create([
                            'name' => $nama,
                            'email' => $email,
                            'alamat' => $alamat,
                            'tlp' => $hp,
                            'password' => bcrypt($password),
                        ]);

                        $x->update([
                            'telegram_id' => $chatId,
                        ]);

                        $responseText = 'Saat ini akunmu sudah terdaftar di sistem kami silahkan login ' . $link . ' sesuai email dan password yang kamu daftarkan sebelumnya';
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

                    // Membuat instance Carbon dari tanggal asli

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

                                    $link = config('base.url') . '/dashboard/pembayaran/create?brg_dtl=';
                                    $ee = $link($e);

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

                                $respon1 = 'Berikut Adalah informasi sementara barang yang kamu pesan. Copy informasi dibawah ini dan masukkan jumlah hari pemesanan';

                                $responseText = "Kode Barang : " . $barang->kode_barang . "\n";
                                $responseText .= "Nama Barang : "  . $barang->nama .   "\n";
                                $responseText .= "Tanggal Sewa : " . Carbon::now() . "\n";
                                $responseText .= "Jumlah Hari : ";
                            } else {
                                $responseText = "Barang dengan kode $tx[1] tidak ditemukan. Silakan periksa kembali kode barang yang Anda masukkan.";
                            }
                        }
                    } else {
                        $responseText = 'Format yang anda masukkan salah. Pastikan sudah sesuai dengan format yang telah di tentukan';
                    }
                }
            } elseif (strpos($text, '/JADIPSAN_') !== false) {

                $tx = explode("_", $text);
                $c = count($tx);
                $dt = User::where('telegram_id', $chatId)->first();

                $wt = WaitingList::find($tx[1]);


                if ($wt) {
                    $wt->update([
                        'respon_user' => Carbon::now()
                    ]);


                    $barang_detail = BarangDetail::where('waiting_id', $wt->id)->first();
                    if ($barang_detail) {
                        $barang_detail->update([
                            'penyewa' => $dt->id,
                            'mulai' => Carbon::now(),
                            'kembali' => NULL,
                            'waiting_id' => NULL,
                        ]);
                    }
                    $responseText = "Masukaan Jumlah hari pemesanan /haripemesanan_2" . "\n";
                } else {
                    $responseText = "luput" . "\n";
                }
            } elseif (strpos($text, '/JADIPESAN_') !== false) {

                $tx = explode("_", $text);
                $responseText = "luput" . $tx[1] . "\n";
                // $c = count($tx);
                $dt = User::where('telegram_id', $chatId)->first();


                $barang_detail = BarangDetail::find($tx[1]);

                if ($barang_detail) {

                    $waiting = WaitingList::find($barang_detail->waiting_id);



                    if ($waiting) {
                        $waiting->update([
                            'respon_user' => Carbon::now(),
                        ]);

                        $respon1 = 'copy format di bawah ini dan lengkapi datanya ya';

                        $responseText = "KONFIRMASI WAITING LIST 0000" . $barang_detail->id . "\n";
                        $responseText .= "Kode Barang : " . $barang_detail->barang->kode_barang . "\n";
                        $responseText .= "Nama Barang : "  . $barang_detail->barang->nama .   "\n";
                        $responseText .= "Tanggal Sewa : " . Carbon::now() . "\n";
                        $responseText .= "Jumlah Hari : ";
                    }

                    if (!$waiting) $responseText = "Data Waiting Anda tidak tersedia" . "\n";
                }

                if (!$barang_detail) $responseText = "Barang Tidak tersedia" . "\n";
            } elseif (strpos($text, '/DAFTAR_WAITING_LIST') !== false) {

                $tx = explode("_", $text);
                // $responseText = "luput" . $tx[1] . "\n";
                $dt = User::where('telegram_id', $chatId)->first();

                $barang = Barang::where('kode_barang', trim($tx[3]))->first();
                $now = Carbon::now();
                $add = $now->addHour(1);
                if ($barang) {
                    $data_order = WaitingList::create([
                        'barang_id' => $barang->id,
                        'user_id' => $dt->id,
                        'status_sewa' => 0,
                    ]);
                    $link = config('base.url') . '/dashboard/waiting';
                    $responseText = "Terimakasih Data Waiting untuk barang $barang->nama dengan kode barang $barang->kode_barang sudah terdaftar" . "\n" . "dapat anda lihat detail waiting anda di tautan berikut " . $link;
                }

                if (!$barang)
                    $responseText = "Barang dengan kode " . $tx[3] . " Tidak Tersedia" .  "\n";
            } elseif (strpos($text, 'KONFIRMASI WAITING LIST 0000') !== false && strpos($text, 'Jumlah Hari :') !== false) {
                $bd_id = null;
                $hari = null;

                $lines = explode("\n", $text);
                // $responseText = 'sip';

                foreach ($lines as $line) {
                    if (strpos($line, 'KONFIRMASI WAITING LIST 0000') !== false) {
                        $bd_id = trim(str_replace('KONFIRMASI WAITING LIST 0000', '', $line));
                    } elseif (strpos($line, 'Jumlah Hari :') !== false) {
                        $hari = trim(str_replace('Jumlah Hari:', '', $line));
                    }
                }

                if ($bd_id && $hari) {

                    $e = BarangDetail::find($bd_id);

                    if ($e) {

                        $w = WaitingList::find($e->waiting_id);
                        if ($w) $w->delete();

                        $e->update([
                            'mulai' => Carbon::now(),
                            'status_sewa' => 1,
                            'kembali' => $now->addDays($hari),
                            'waiting_id' => NULL,
                        ]);

                        $dt = User::where('telegram_id', $chatId)->first();

                        $data_order = Pesanan::create([
                            'barang_detail_id' => $e->id,
                            'user_id' => $dt->id,
                            'tipe_bayar' => NULL,
                            'bukti_bayar' => NULL,
                            'status' => 'belum bayar',

                            'mulai' => $e->mulai,
                            'kembali' => $e->kembali,
                            'total' => (int)$e->barang->harga_sewa * (int)$hari,
                        ]);

                        $responseText = 'Data Penyewaan berhasil di tambahkan berikut adalah informasi sewa anda' . "\n";
                        $responseText .= "\n";
                        $responseText .= "Nama Barang:" . $e->barang->nama . "\n";
                        $responseText .= "Kode Barang:" . $e->barang->kode_barang . "\n";
                        $responseText .= "Mulai sewa: $e->mulai \n";
                        $responseText .= "Kembali sewa  : $e->kembali \n";
                        $link = config('base.url') . '/dashboard/pembayaran/create?brg_dtl=' . $data_order->id;
                        $responseText .= "Anda dapat segera melakukan pembayaran melalui link berikut ini " . $link . "\n";
                        $responseText .= "\n";
                        // $responseText = 'terimakasih sudah di update';

                    }
                } else {

                    $responseText = 'nssip';
                }
            } elseif (strpos($text, 'Kode Barang') !== false && strpos($text, 'Jumlah Hari :') !== false && strpos($text, 'Nama Barang :') !== false) {
                $kode_barang = null;
                $hari = null;

                $lines = explode("\n", $text);
                foreach ($lines as $line) {
                    if (strpos($line, 'Kode Barang') !== false) {
                        $kode_barang = trim(str_replace('Kode Barang :', '', $line));
                    } elseif (strpos($line, 'Jumlah Hari :') !== false) {
                        $hari = trim(str_replace('Jumlah Hari :', '', $line));
                    }
                }

                // $responseText = 'ksks' .$hari. $kode_barang;

                if ($kode_barang && $hari) {

                    $brg = Barang::where('kode_barang', $kode_barang)->first();

                    if (!$brg) $responseText = 'barang tidak tersedia';

                    if ($brg) {
                        $e = BarangDetail::where([
                            'barang_id' => $brg->id,
                            'status_sewa' => 0,
                        ])->first();

                        if (!$e) {
                            $br = BarangDetail::where([
                                'barang_id' => $brg->id,
                                'status_sewa' => 1,
                            ])->orderBy('kembali', 'ASC')->first();

                            $link = '/DAFTAR_WAITING_LIST_' . $brg->kode_barang;
                            $responseText = "Yahh........., barang yang kamu inginkan saat ini sedang sedang full booked. Ada 1 barang yang paling dekat ready di tanggal " . tgl($br->kembali) . ". Gimana? kalau masih minat dengan barang ini kamu bisa klik link berikut agar di daftarkan di data waitinglist oleh admin" . "\n"  . $link . "\n" . "nanti admin kabari kalo barangnya ready";
                        }

                        if ($e) {
                            $e->update([
                                'mulai' => Carbon::now(),
                                'status_sewa' => 1,
                                'kembali' => $now->addDays($hari),
                                'waiting_id' => NULL,
                            ]);

                            $dt = User::where('telegram_id', $chatId)->first();

                            $data_order = Pesanan::create([
                                'barang_detail_id' => $e->id,
                                'user_id' => $dt->id,
                                'tipe_bayar' => NULL,
                                'bukti_bayar' => NULL,
                                'status' => 'belum bayar',

                                'mulai' => $e->mulai,
                                'kembali' => $e->kembali,
                                'total' => (int)$e->barang->harga_sewa * (int)$hari,
                            ]);

                            $responseText = 'Data Penyewaan berhasil di tambahkan berikut adalah informasi sewa anda' . "\n";
                            $responseText .= "\n";
                            $responseText .= "Nama Barang:" . $e->barang->nama . "\n";
                            $responseText .= "Kode Barang:" . $e->barang->kode_barang . "\n";
                            $responseText .= "Mulai sewa: $e->mulai \n";
                            $responseText .= "Kembali sewa: $e->kembali \n";
                            $link = config('base.url') . '/dashboard/pembayaran/create?brg_dtl=' . $data_order->id;

                            $responseText .= "Anda dapat segera melakukan pembayaran melalui link berikut ini " . $link . "\n";
                            $responseText .= "\n";
                            // $responseText = 'terimakasih sudah di update';

                        }
                    }
                } else {
                    $responseText = 'Pesanan gagal di tambahkan .Pastikan format yang anda masukkan sudah sesuai';
                }
            } else {
                // Balas dengan pesan default jika perintah tidak dikenali
                $responseText = 'Maaf, saya tidak mengenali perintah tersebut.';
            }
            if (isset($respon1)) $this->sendMsg($chatId, $respon1);
            if (isset($responseText)) $this->sendTelegramMessage($chatId, $responseText);
        } elseif (isset($data['callback_query'])) {

            $this->handleCallbackQuery($data['callback_query']);
        }

        return response()->json(['status' => 'success']);
    }

    private function handleStart($chatId, $username)
    {
        $user = User::where('telegram_id', $chatId)->first();
        if ($user) {
            $responseText = 'Halo 🖐 Bro/Sist '   . $username . '. Selamat datang di Gading Adventure. Kondisi akunmu untuk sistem telegram kami baik baik saja. Anda dapat klik /profil untuk melihat lebih detail';
        }
        if (!$user) {
            $responseText = 'Halo 🖐 Bro/Sist '   . $username . ' Saat ini akunmu belum terdaftar di sistem kami. klik /registrasi untuk melakukan pendaftaran dan ikuti langkah selanjutnya. .';
        }
        $this->sendTelegramMessage($chatId, $responseText);

        return response('Handling /help command');
    }
    private function handleRegistrasi($chatId, $username)
    {
        $dt = User::where('telegram_id', $chatId)->first();
        if ($dt == null) {
            $respon1 = 'Copy format di bawah ini dan masukkan datanya' . "\n";
            $responseText = 'Nama:' . "\n";
            $responseText .= 'Email:' . "\n";
            $responseText .= 'Alamat:' . "\n";
            $responseText .= 'Hp:' . "\n";
            $responseText .= 'Password:' . "\n";
        } else {
            $responseText = 'Anda sudah terdaftar di sistem kami, tidak perlu lagi melakukan registrasi. Anda dapat melihat informasi profile anda dengan klik atau mengetikkan /profil' . "\n";
        }
        $this->sendMsg($chatId, $respon1);
        $this->sendTelegramMessage($chatId, $responseText);

        return response('Handling /registrasi command');
    }


    private function sendTelegramMessage($chatId, $text)
    {
        $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendMessage';
        $messageData = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        // if ($keyboard !== null) {
        //     $messageData['reply_markup'] = json_encode($keyboard);
        // }

        Telegram::sendMessage($messageData);
    }

    private function sendMsg($chatId, $text)
    {
        $url = 'https://api.telegram.org/bot' . env('TELEGRAM_BOT_TOKEN') . '/sendMessage';
        $messageData = [
            'chat_id' => $chatId,
            'text' => $text,
        ];

        // if ($keyboard !== null) {
        //     $messageData['reply_markup'] = json_encode($keyboard);
        // }

        Telegram::sendMessage($messageData);
    }
}
