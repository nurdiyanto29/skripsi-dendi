<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Telegram\Bot\Api;
use App\Models\Barang;
use App\Models\ChatId;

use GuzzleHttp\Client;
use App\Models\Pesanan;
use App\Models\WaitingList;
use Illuminate\Support\Str;
use App\Models\BarangDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Telegram\Bot\FileUpload\InputFile;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramController extends Controller
{

    public function webhook(Request $request)
    {
        $data = $request->all();
        if (isset($data['message'])) {

            $message = $data['message'];

            $chatId = $message['chat']['id'];
            $respon1 = null;
            $first = $message['from']['first_name'];

            if (isset($message['photo'])) {

                $caption = isset($message['caption']) ? $message['caption'] : '';

                if (!$caption) $responseText = 'Pastikan kode bayar di inputkan di caption foto.';

                if ($caption) {
                    $n_caption = str_replace("INV000", "", $caption);

                    $cek = Pesanan::where('id', $n_caption)
                        ->where('bukti_bayar', null)
                        ->where('bukti_bayar', null)->first();

                    if (!$cek) $responseText = 'Pembayaran dengan kode ' . $caption . ' Tidak ditemukan';
                    $photo = end($message['photo']);
                    $photoId = $photo['file_id'];

                    $telegram = new \Telegram\Bot\Api('6795475393:AAHO4f-ShvgGcaWo0mIjSHujvYG9xoAK3Mo');
                    $file = $telegram->getFile(['file_id' => $photoId]);
                    $filePath = $file->getFilePath();

                    $photoContents = file_get_contents('https://api.telegram.org/file/bot' . '6795475393:AAHO4f-ShvgGcaWo0mIjSHujvYG9xoAK3Mo' . '/' . $filePath);
                    $fileName = 'uploads/bukti_bayar/' . basename($filePath);

                    if ($cek) {
                        $cek->update([
                            'bukti_bayar' => basename($filePath),
                            'tipe_bayar' => 'tf',
                            'status' => 'terbayar belum terkonfirmasi',
                        ]);

                        $directory = public_path('uploads/bukti_bayar');
                        if (!File::exists($directory)) {
                            File::makeDirectory($directory, 0777, true, true);
                        }

                        File::put(public_path($fileName), $photoContents);
                        $responseText = 'Terimakasih. Pembayaran sudah kami terima. Tunggu konfirmasi dari admin ya';
                    }
                }
                if (isset($responseText)) $this->sendTelegramMessage($chatId, $responseText);
            } else {
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
                    $lines = explode("\n", $text);

                    $this->handleRegistrasiProses($chatId, $lines, $nama, $email, $alamat, $hp, $password);
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
                            $dt = User::where('telegram_id', 'gading_tele' . $chatId)->first();
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
                        } catch (Exception $e) {
                            $responseText = 'Tanggal anda salah ya' . "\n";
                        }
                    } else {
                        $responseText = "Proses registrasi anda Gagal Pastikan anda menginputkan dengan format yang benar";
                    }
                } elseif ($text === '/catalog') {
                    $barang = Barang::where('status', 1)->get();
                    $this->handleCatalog($chatId, $barang);
                } elseif ($text === '/profil') {
                    $dt = User::where('telegram_id', 'gading_tele' . $chatId)->first();
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
                    $dt = User::where('telegram_id', 'gading_tele' . $chatId)->first();
                    if ($dt == null) {
                        $responseText = 'Maaf anda belum terdaftar di sistem kami. ketikan atau klik /registrasi untuk melakukan pendaftaran di sistem kami. Registrasi adalah langkah pertama untuk bisa melakukan pemesanan melalui telegram bot Gading Adventure' . "\n";
                    } else {
                        if ($c == 2) {
                            if (strpos($text, $tx[0]) !== false && strpos($text, $tx[1]) !== false) {
                                $barang = Barang::where('kode_barang', $tx[1])->first();

                                if ($barang != null) {

                                    $respon1 = 'Berikut Adalah informasi sementara barang yang kamu pesan. Copy informasi dibawah ini dan masukkan tanggal sewa dengan format Tahun-Bulan-Tgl (2024-02-01) serta jumlah hari pemesanan';

                                    $responseText = "Kode Barang : " . $barang->kode_barang . "\n";
                                    $responseText .= "Nama Barang : "  . $barang->nama .   "\n";
                                    $responseText .= "Tanggal Sewa : " . "\n";
                                    $responseText .= "Jumlah Hari : ";
                                } else {
                                    $responseText = "Barang dengan kode $tx[1] tidak ditemukan. Silakan periksa kembali kode barang yang Anda masukkan.";
                                }
                            }
                        } else {
                            $responseText = 'Format yang anda masukkan salah. Pastikan sudah sesuai dengan format yang telah di tentukan';
                        }
                    }
                } elseif (strpos($text, '/JADIPESAN_') !== false) {

                    $tx = explode("_", $text);
                    // $responseText = "luput" . $tx[1] . "\n";
                    $dt = User::where('telegram_id', 'gading_tele' . $chatId)->first();


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
                    $dt = User::where('telegram_id', 'gading_tele' . $chatId)->first();

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
                            $hari = trim(str_replace('Jumlah Hari :', '', $line));
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

                            $dt = User::where('telegram_id', 'gading_tele' . $chatId)->first();

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
                } elseif (strpos($text, 'Kode Barang') !== false && strpos($text, 'Jumlah Hari :') !== false && strpos($text, 'Nama Barang :') !== false && strpos($text, 'Tanggal Sewa :') !== false) {
                    $kode_barang = null;
                    $hari = null;
                    $tanggal = null;

                    $lines = explode("\n", $text);
                    foreach ($lines as $line) {
                        if (strpos($line, 'Kode Barang') !== false) {
                            $kode_barang = trim(str_replace('Kode Barang :', '', $line));
                        } elseif (strpos($line, 'Jumlah Hari :') !== false) {
                            $hari = trim(str_replace('Jumlah Hari :', '', $line));
                        } elseif (strpos($line, 'Tanggal Sewa :') !== false) {
                            $tanggal = trim(str_replace('Tanggal Sewa :', '', $line));
                        }
                    }


                    if ($kode_barang && $hari && $tanggal) {

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
                                $tgl = Carbon::createFromFormat('Y-m-d', $tanggal);
                                $akhir = Carbon::createFromFormat('Y-m-d', $tanggal);
                                $akhir->addDays($hari);
                                $akhir->endOfDay();

                                $e->update([
                                    'mulai' =>  $tgl->copy()->startOfDay(),
                                    'status_sewa' => 1,
                                    'kembali' => $akhir,
                                    'waiting_id' => NULL,
                                ]);

                                $dt = User::where('telegram_id', 'gading_tele' . $chatId)->first();

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
                                $responseText .= "Atau dapat juga upload bukti pembayaran melalui telegram dengan memberikan caption INV000$data_order->id\n";
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
                if ($respon1 != null) $this->sendMsg($chatId, $respon1);
                if (isset($responseText)) $this->sendTelegramMessage($chatId, $responseText);
            }
        }

        return response()->json(['status' => 'success']);
    }

    private function handleStart($chatId, $username)
    {
        $user = User::where('telegram_id', 'gading_tele' . $chatId)->first();

        if ($user) {
            $responseText = 'Halo 🖐 Bro/Sist '   . $username . '. Selamat datang di Gading Adventure. Kondisi akunmu untuk sistem telegram kami baik baik saja. Anda dapat klik /profil untuk melihat lebih detail';
        }
        if (!$user) {
            $responseText = 'Halo 🖐 Bro/Sist '   . $username . ' Saat ini akunmu belum terdaftar di sistem kami. klik /registrasi untuk melakukan pendaftaran dan ikuti langkah selanjutnya. .';
        }
        $this->sendTelegramMessage($chatId, $responseText);

        return response('Handling /help command');
    }

    // private function sendImage($chatId, $imagePath)
    // {
    //     // Kirim gambar sebagai respons menggunakan API Bot Telegram
    //     $response = Http::post('https://api.telegram.org/bot' . config('telegram.bot_token') . '/sendPhoto', [
    //         'chat_id' => $chatId,
    //         'photo' => $imagePath
    //     ]);
    //     $responseText = 'Halo 🖐 '.$imagePath;
    //     $this->sendTelegramMessage($chatId, $responseText);
    //     return $response->successful();
    // }


    private function processPhoto($photoData)
    {
        // Periksa apakah kunci 'file_path' ada dalam array
        if (isset($photoData['file_id'])) {
            // Simpan foto ke folder publik
            $photoPath = $this->savePhotoToStorage($photoData);

            // Simpan informasi foto ke basis data
            $this->savePhotoToDatabase($photoPath);
        } else {
            // Log pesan kesalahan atau lakukan penanganan lainnya
            Log::error('File path not found in photo data:', $photoData);
        }
    }
    private function savePhotoToStorage($photoData)
    {
        // Buat path untuk penyimpanan foto
        $photoPath = 'photos/' . $photoData['file_id'] . '.jpg';
        $response = Http::post('https://api.telegram.org/bot' . '6795475393:AAHO4f-ShvgGcaWo0mIjSHujvYG9xoAK3Mo' . '/sendPhoto', [
            'chat_id' => 5881233108,
            'photo' => $photoPath
        ]);

        // Dapatkan URL foto menggunakan file_id
        $photoUrl = 'https://api.telegram.org/file/bot' . '6795475393:AAHO4f-ShvgGcaWo0mIjSHujvYG9xoAK3Mo' . '/' . $photoData['file_id'];

        // Unduh dan simpan foto ke folder storage
        $fileContents = file_get_contents($photoUrl);
        Storage::disk('public')->put($photoPath, $fileContents);

        return $photoPath;
    }

    private function savePhotoToDatabase($photoPath)
    {

        $photo = Pesanan::first();
        $photo->update([
            'bukti_bayar' => basename($photoPath)
        ]);
        // filename = basename($photoPath);
        // $photo->path = Storage::url($photoPath); // Path untuk diakses melalui web
        // $photo->save();
    }





    private function handleCatalog($chatId, $barang)
    {
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
        $this->sendTelegramMessage($chatId, $responseText);

        return response('Handling /help command');
    }
    private function handleRegistrasi($chatId, $username)
    {
        $respon1 = null;
        $dt = User::where('telegram_id', 'gading_tele' . $chatId)->first();
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
        if ($respon1 != null) $this->sendMsg($chatId, $respon1);
        $this->sendTelegramMessage($chatId, $responseText);

        return response('Handling /registrasi command');
    }
    private function handleRegistrasiProses($chatId, $lines, $nama, $email, $alamat, $hp, $password)
    {
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

            $tele = User::where('telegram_id', 'gading_tele' . $chatId)->first();


            if ($dt || $tele) {
                $responseText = 'Saat ini emailmu  atau akun telegramu sudah terdaftar di sistem kami silahkan login ' . $link . ' sesuai email dan password yang kamu daftarkan sebelumnya';
            } else {

                $x = User::create([
                    'name' => $nama,
                    'email' => $email,
                    'alamat' => $alamat,
                    'tlp' => $hp,
                    'telegram_id' => 'gading_tele' . $chatId,
                    'password' => bcrypt($password),
                ]);

                $responseText = 'Saat ini akunmu sudah terdaftar di sistem kami silahkan login ' . $link . ' sesuai email dan password yang kamu daftarkan sebelumnya';
            }
        } else {
            $responseText = "Proses registrasi anda Gagal Pastikan anda menginputkan dengan format yang benar";
        }
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
