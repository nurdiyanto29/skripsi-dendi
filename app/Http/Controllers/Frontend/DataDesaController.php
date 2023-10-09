<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Frontend\Controller;
use App\Models\Konten;
use App\Models\Penduduk;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Dusun;
use Illuminate\Support\Carbon;

class DataDesaController extends Controller
{
    public function index($type)
    {
        $q = array(
            'pendidikan',
            'pendidikan_ditempuh',
            'pekerjaan',
            'agama',
            'jenis_kelamin',
            'golongan_darah',
            'status_perkawinan',
            'pendidikan_yang_ditempuh',
            'kelompok_umur',
        );
        if (in_array($type, $q)) {
            $replace = str_replace('_', ' ',$type);
            $type = Str::title($replace);
            $data = [
                'nama' => ucfirst($type)
            ];
            $opt = [];
            return $this->view('frontend.data_desa', compact('data', 'opt'));
        }
        abort('404');
    }

    function ajax(Request $req, $type)
    {
        $penduduk = Penduduk::get();
        if ($type == 'golongan_darah') $type = 'gol_darah';
        if ($type == 'status_perkawinan') $type = 'kawin';
        if ($type == 'jenis_kelamin') $type = 'jk';
        if ($type == 'pendidikan_yang_ditempuh') $type = 'pendidikan_ditempuh';

        if ($type == 'kelompok_umur') {
            $_data = $penduduk->groupBy(function ($penduduk) {
                $age = Carbon::parse($penduduk->tanggal_lahir)->age;
                if ($age >= 0 && $age < 1) {
                    return 'Dibawah 1 tahun';
                } elseif ($age >= 1 && $age <= 4) {
                    return '2-4 tahun';
                } elseif ($age > 4 && $age <= 9) {
                    return '5-9 tahun';
                } elseif ($age > 9 && $age <= 14) {
                    return '10-14 tahun';
                } elseif ($age > 14 && $age <= 19) {
                    return '15-19 tahun';
                } elseif ($age > 19 && $age <= 24) {
                    return '20-24 tahun';
                } elseif ($age > 24 && $age <= 29) {
                    return '25-29 tahun';
                } elseif ($age > 29 && $age <= 34) {
                    return '30-34 tahun';
                } elseif ($age > 34 && $age <= 39) {
                    return '35-39 tahun';
                } elseif ($age > 39 && $age <= 44) {
                    return '40-44 tahun';
                } elseif ($age > 44 && $age <= 49) {
                    return '45-49 tahun';
                } elseif ($age > 49 && $age <= 54) {
                    return '50-54 tahun';
                } elseif ($age > 54 && $age <= 59) {
                    return '55-59 tahun';
                } elseif ($age > 59 && $age <= 64) {
                    return '60-64 tahun';
                } elseif ($age > 64 && $age <= 69) {
                    return '65-69 tahun';
                } elseif ($age > 69 && $age <= 74) {
                    return '70-74 tahun';
                } elseif ($age >= 75) {
                    return 'Lebih dari 75 tahun';
                } else {
                    return 'Tidak Tahu';
                }
            });
        }
        if ($type != 'kelompok_umur') {
        $_data = $penduduk->groupBy($type);
        }

        if (!$_data) return set_res('Data kosong!');

        $item = [];
        $chart = [
            'labels' => [],
            'backgroundColor' => [],
            'jumlah' => [],
        ];
        $kelompok = $jumlah = $lk = $pr = $rt = $percent_lk = $percent_pr = 0;
        $percent_all = 100;
        foreach ($_data as $key => $val) {

            $e = [
                'kelompok' => $key,
                'jumlah' => $val->count(),
                'lk' => $val->where('jk', 'L')->count(),
                'pr' => $val->where('jk', 'P')->count(),
                'percent_all' => number_format(($val->count() / $penduduk->count()) * 100, 2, '.', ''),
                'percent_lk' =>  number_format(($val->where('jk', 'L')->count() / $penduduk->count()) * 100, 2, '.', ''),
                'percent_pr' =>  number_format(($val->where('jk', 'P')->count() / $penduduk->count()) * 100, 2, '.', ''),
            ];

            $jumlah += $e['jumlah'];
            $lk += $e['lk'];
            $pr += $e['pr'];
            // $percent_all += $e['percent_all'];
            $percent_pr += $e['percent_pr'];
            $percent_lk += $e['percent_lk'];

            // chart, jka ada filter selain jiwa tinggal ganti dataset datanya sesuai filter (rt, jiwa, lk, pr)
            $chart['labels'][] = $key;
            $chart['jumlah'][] = (int)$e['jumlah'];
            // $chart['backgroundColor'][] = $count;
            $item[] = $e;
        }
        $total = compact('jumlah', 'percent_all', 'percent_lk', 'percent_pr', 'kelompok', 'lk', 'pr');
        // dd($total);
        return set_res('', true, compact('item', 'total', 'chart','type'));
    }
}
