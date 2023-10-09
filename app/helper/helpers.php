<?php

use App\Models\PlanOverview;
use Illuminate\Support\Str;
use Hamcrest\Type\IsNumeric;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Request;

use Illuminate\Support\Facades\Storage;
use function PHPUnit\Framework\fileExists;


// function tgl($date){
//     // Carbon\Carbon::parse($data->tgl_catatan_sipil)->isoFormat('D MMM Y')
//     return \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY HH:mm:ss');
// }
function tgls($date)
{
    Carbon\Carbon::parse($date)->isoFormat('D MMM Y');
    // return \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY');
}


function uriaktif($uri = '', $segment = 2)
{
    if (is_array($uri)) return in_array(Request::segment($segment), $uri) ? 'active' : '';
    return Request::segment($segment) == $uri ? 'active' : '';
}


// Start template
function table_count($value)
{
    return $value->count() ?: null;
}

// Start time
function tgl($date, $format = 'D MMM Y')
{
    if (!$date) return;
    try {
        if (gettype($date) == 'object') {
            return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $date)->isoFormat($format);
        } else {
            return \Carbon\Carbon::parse($date)->isoFormat($format);
        }
    } catch (\Carbon\Exceptions\InvalidFormatException $e) {
        return null;
    }
}


 function rp($amount)
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function tgl_full($date)
{
    return tgl($date, 'D MMMM Y HH:mm');
}

function jam($date)
{
    return tgl($date, 'HH:mm');
}


function thumbnail($file, $thumb = 'thumb_', $src = false, $replace = true)
{
    $i = Storage::url($file);
    $x = str_replace("storage", "files", $i);

    $e = explode('/', $x);
    $file = end($e);
    $thum_file = str_replace($file, $thumb . $file, $x);

    $thum_exist = file_exists((public_path($thum_file)));
    $origin_exist = file_exists((public_path($x)));
    $exist = $thum_exist ?: $origin_exist;
    $x = $thum_exist ? $thum_file : $x;

    $notFound = 'img/notfound.png';
    $img = $exist ? URL::asset($x) : URL::asset($notFound);

    if ($src) return $replace  ? $img : ($exist ? $img : null);
    return  $replace  ? '<img src="' . $img . '" style="max-width:100px; max-height:110px;" />' : null;
}

function image($file, $src = false, $replace = true)
{
    $i = Storage::url($file);
    dd($i);
    $x = str_replace("storage", "files", $i);

    $exist = file_exists((public_path($x)));

    $notFound = 'img/notfound.png';
    $img = $exist ? URL::asset($x) : URL::asset($notFound);

    if ($src) return $replace  ? $img : ($exist ? $img : null);
    return  $replace  ? '<img src="' . $img . '" style="max-width:100px; max-height:110px;" />' : null;
}


function remove_image($file, $thumb = '')
{
    $i = Storage::url($file);
    $x = str_replace("storage", "files", $i);
    if (file_exists(public_path($x))) {

        @unlink(public_path($x));
        if ($thumb) {
            $e = explode('/', $x);
            $file = end($e);
            @unlink(public_path(str_replace($file, $thumb . $file, $x)));
        }
    }
}


function get_substr($string, $value = 50)
{
    $string = strip_tags($string);
    $value = intval($value);
    if ($value > 0) {
        $dot = (strlen($string) > $value) ? "..." : '';
        $txt = substr($string, 0, $value);
        return $txt . $dot;
    } else if ($value < 0) {
        $txt = substr($string, $value);
        $value = abs($value);
        $dot = (strlen($string) > $value) ? "..." : '';
        return $dot . $txt;
    }
}


function persen($val)
{
    return $val ? $val . '%' : '';
}

function progress($val)
{

    if ($val < 25) $class = 'danger';
    elseif ($val < 50) $class = 'warning';
    elseif ($val < 75) $class = 'primary';
    else $class = 'success';

    return $class;
}

function ceklist($val)
{
    return (bool)$val ? '<span class="fa fa-check-square text-success"></span>' : '<span class="fa fa-square "></span>';
}

//ubah tr color (boolean)
function tr_color_code($val, $reverse = null)
{
    $color = 'table-secondary';
    if ($reverse) return $val ? '' : $color;
    else return $val ? $color : '';
}

function color_rgb($key)
{
    $color = [
        '223, 255, 0', '255, 191, 0', '255, 127, 80', '222, 49, 99', '159, 226, 191',
        '64, 224, 208', '100, 149, 237', '204, 204, 255', '255, 105, 180', '255, 99, 71',
        '255, 215, 0', '255, 228, 181', '147, 112, 219', '189, 183, 107', '50, 205, 50',
        '60, 179, 113', '0, 128, 0', '128, 128, 0', '102, 205, 170', '127, 255, 212',
        '70, 130, 180', '123, 104, 238', '0, 0, 205', '188, 143, 143', '160, 82, 45',
        '112, 128, 144', '255, 160, 122', '199, 21, 133', '255, 218, 185', '102, 51, 153'
    ];
    $jml_key = count($color) - 1;
    if ($key > $jml_key) $key = $key % $jml_key;
    return "rgb($color[$key])";
}

// End template

function set_res($message = 'Data tidak valid!', $status = false, $data = [])
{
    if ($status && !is_bool($status) && !$data) $data = $status;
    $status = (bool)$status;
    return json_encode(compact('status', 'message', 'data'));
}

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
    return $d && $d->format($format) === $date;
}


function dateDiff($start, $end)
{
    if (!validateDate($start) || !validateDate($end)) return;

    $start = new DateTime($start);
    $end = new DateTime($end);
    $diff = $start->diff($end)->format("%r%a");
    return $diff;
}


function role_type($type, $model, $return = false)
{
    // dd($model);
    $user = auth()->user();
    if ($user->hasRole('Super Admin')) return true;

    $model = camel2snake($model);
    $role = $user->can(strtolower($model) . '-' . $type);
    if ($return) return $role;

    if (!$role) abort('401');
}

function nominal($val)
{
    if (!is_numeric($val)) return $val;
    return number_format($val, 0, ',', '.');
}

function summernote($content)
{
    libxml_use_internal_errors(true);
    $dom = new \DomDocument();
    $dom->loadHtml($content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    $imageFile = $dom->getElementsByTagName('img');
    foreach ($imageFile as $item => $image) {
        $dt = $image->getAttribute('src');
        if (strpos($dt, 'data') !== false) {
            list($type, $dt) = array_pad(explode(';', $dt), 2, null);
            list(, $dt) = array_pad(explode(',', $dt), 2, null);

            $imgeData = base64_decode($dt);
            $f = finfo_open();
            $mime_type = finfo_buffer($f, $imgeData, FILEINFO_MIME_TYPE);
            $ext = mime_to_ext($mime_type, 'jpg');

            $image_name = "media/" . time() . '-' . uniqid() . $ext;

            $img = Image::make($dt)->save($image_name, 65);
            if ($img) {
                $image->removeAttribute('src');
                $image->setAttribute('src', '/' . $image_name);
            }
        }
    }
    $content = $dom->saveHTML();
    return $content;
}

function mime_to_ext($val, $default = null)
{

    switch ($val) {

        case "image/gif":
            return '.gif';
            break;

        case "image/jpeg":
            return ".jpg";
            break;

        case "image/png":
            return ".png";
            break;

        case "image/bmp":
            return ".bmp";
            break;
        default:
            return $default ? ".$default" : null;
            break;
    }
}




// function rupiah($val){
//     if (!is_numeric($val)) return null;
//     $val =  number_format($val, 0, ',', '.');
//     return "<div><span class='float-left'>Rp.</span> <span class='float-right'>$val</span></div>";
// }


function get_attr($html, $attr, $element, $all = false)
{
    $element = 'span';

    if ($all) preg_match_all('#<' . $element . '\s[^>]*?(?:' . $attr . '=[\'"](.*?)[\'"]).*?>#is', $html, $matches);
    else preg_match('#<' . $element . '\s[^>]*?(?:' . $attr . '=[\'"](.*?)[\'"]).*?>#is', $html, $matches);

    return $matches[1] ?? null;
}

function fileExtension($name)
{
    $n = strrpos($name, '.');
    return ($n === false) ? '' : substr($name, $n + 1);
}

// Start eloquent
// khusus mysql dan sekarang ERROR
function get_enum($table, $field, $json = false)
{

    // DB::enableQueryLog();
    $type = DB::select(DB::raw("SHOW COLUMNS FROM $table WHERE Field = '$field'"))[0]->Type ?? null;
    preg_match("/^enum\(\'(.*)\'\)$/", $type, $matches);
    $enum = $matches ? explode("','", $matches[1]) : [];
    return $json ? json_encode($enum) : $enum;
}


function soft_delete($model)
{
    try {
        $model = '\App\Models\\' . $model;
        return in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model));
    } catch (ErrorException $err) {
        return null;
    }
}

function model_path($model)
{
    $model = str_replace(' ', '', ucwords(str_replace('_', ' ', $model)));
    return 'App\Models\\' . $model;
}


// End eloquent

// camelCase to snake_case
function camel2snake($string)
{
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
}

function url_back($msg = 'Akses ditolak!')
{
    return url()->previous() != url()->full() ? redirect()->to(url()->previous())->with('error', $msg) : abort(401);
}
