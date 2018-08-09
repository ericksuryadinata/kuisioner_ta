<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function crypto_rand_secure($min, $max){
    $range = $max - $min;
    if ($range < 1) return $min; // not so random...
    $log = ceil(log($range, 2));
    $bytes = (int) ($log / 8) + 1; // length in bytes
    $bits = (int) $log + 1; // length in bits
    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
    do {
        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
        $rnd = $rnd & $filter; // discard irrelevant bits
    } while ($rnd >= $range);
    return $min + $rnd;
}

function getToken($length){
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet); // edited

    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[crypto_rand_secure(0, $max)];
    }

    return $token;
}

if ( ! function_exists('read_more'))
{
	function read_more($string,$limit=100)
	{
		$string = trim(preg_replace('/\s+/', ' ', $string));
		$string = trim(preg_replace('/\t+/', '', $string));
		$string = str_replace('&nbsp;','',$string);
		$length = strlen(strip_tags($string));
		if ($length>$limit){

		    $isi_string = htmlentities(strip_tags($string)); // membuat paragraf pada isi berita dan mengabaikan tag html
		    $isi = substr($isi_string,0,$limit); // ambil sebanyak 220 karakter
		    $isi = substr($isi_string,0,strrpos($isi," ")).' ... '; // potong per spasi kalimat

			return $isi;
		}
		else {
			return strip_tags($string);
		}
	}
}

if ( ! function_exists('goExplode'))
{
	function goExplode($string,$delimiter="-",$result=0) {
	    $var 	= explode($delimiter, $string);
	    if($result==0){
	    	return $var[0];
	    }

	    if(!isset($var[$result])){
	    	return;
	    }

	    return $var[$result];
	}
}

if ( ! function_exists('gRecaptcha')){

	function gRecaptcha($type=null)
	{

		if($type=="show"){
			$show = '<div class="g-recaptcha" data-sitekey="'.getenv('GOOGLE_RECAPTCHA_SITE').'"></div>';
			return $show;
		}
		$secret = getenv('GOOGLE_RECAPTCHA_SECRET');
	    $gRecaptcha = $_POST['g-recaptcha-response'];
	    $gRecaptcha = "https://www.google.com/recaptcha/api/siteverify?secret=".$secret."&response=".$_POST['g-recaptcha-response'];
	    $response = file_get_contents($gRecaptcha);
	    $responseData = json_decode($response);

	    if(!isset($_POST['g-recaptcha-response'])){
	    	return false;
	    }


	    if($responseData->success){
	        return true;
	    }else{
	        return false;
	    }
	}
}

if ( ! function_exists('remove_file'))
{
	function remove_file($path){
		if(file_exists($path)){
			if(unlink($path)){
				return true;	
			}
			return false;
		}

		return false;
	}
}

if (!function_exists('default_image_for')){
	function default_image_for($type=null){
		switch ($type) {
			case 'box-ads':
				return 'uploads/images/placeholder/box-ads.png';
				break;
			case 'long-ads':
			    return 'uploads/images/placeholder/long-ads.png';
				break;
			case 'man':
			    return 'uploads/images/placeholder/avatar.png';
				break;
			case 'women':
			    return 'uploads/images/placeholder/women.png';
				break;
			case 'camera':
			    return 'uploads/images/placeholder/camera.jpg';
				break;
			case 'video':
			    return 'uploads/images/placeholder/video.jpg';
				break;
			case 'untag':
			    return 'uploads/images/placeholder/untag.png';
				break;
			default:
				return 'uploads/images/placeholder/basic.png';
				break;
		}
	}
}

if (!function_exists('upload_path')){
	function upload_path($type,$condition=null){
		switch ($condition) {
			case 'original':
				return 'uploads/images/'.$type.'/'.'original/';
				break;
			case 'mobile':
				return 'uploads/images/'.$type.'/'.'mobile/';
				break;
			case 'thumb':
				return 'uploads/images/'.$type.'/'.'thumb/';
				break;
			case 'medium':
				return 'uploads/images/'.$type.'/'.'medium/';
				break;
			case 'large':
				return 'uploads/images/'.$type.'/'.'large/';
				break;
			case 'extra':
				return 'uploads/images/'.$type.'/'.'extra/';
				break;
			case 'files':
				return 'uploads/files/';
				break;
			case 'viewerjs':
				return 'ViewerJS/#/uploads/files/';
				break;
			default:
				return 'uploads/images/'.$type.'/';
				break;
		}
	}
}

if (!function_exists('image_path_for')){
	function image_path_for($type,$condition=null){
		switch ($condition) {
			case 'original':
				return 'uploads/images/'.$type.'/'.'original/';
				break;
			case 'mobile':
				return 'uploads/images/'.$type.'/'.'mobile/';
				break;
			case 'thumb':
				return 'uploads/images/'.$type.'/'.'thumb/';
				break;
			case 'medium':
				return 'uploads/images/'.$type.'/'.'medium/';
				break;
			case 'large':
				return 'uploads/images/'.$type.'/'.'large/';
				break;
			case 'extra':
				return 'uploads/images/'.$type.'/'.'extra/';
				break;
			default:
				return 'uploads/images/'.$type.'/';
				break;
		}
	}
}

if ( ! function_exists('seo'))
{
	function seo($s) {
	    $c = array (' ');
	    $d = array ('-','/','\\',',','.','#',':',';','\'','"','[',']','{','}',')','(','|','`','~','!','@','%','$','^','&','*','=','?','+');
	    $s = str_replace($d, '', $s); // Hilangkan karakter yang telah disebutkan di array $d
	    $s = strtolower(str_replace($c, '-', $s)); // Ganti spasi dengan tanda - dan ubah hurufnya menjadi kecil semua
	    return $s;
	}
}


if ( ! function_exists('slug'))
{
	function slug($text)
	{
		// replace non letter or digits by -
		$text = preg_replace('~[^\\pL\d]+~u', '-', $text);
	
		// trim
		$text = trim($text, '-');
	
		// transliterate
		$text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
	
		// lowercase
		$text = strtolower($text);
	
		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);
	
		if (empty($text))
		{
			return 'n-a';
		}
	
		return $text;
	}
}

if (!function_exists('hari_indo')){
	function hari_indo($hari){
		switch ($hari) {
			case '1':
				return 'Senin';
				break;
			case '2':
				return 'Selasa';
				break;
			case '3':
				return 'Rabu';
				break;
			case '4':
				return 'Kamis';
				break;
			case '5':
				return 'Jumat';
				break;
			case '6':
				return 'Sabtu';
				break;
			case '7':
				return 'Minggu';
				break;
			default:
				return false;
				break;
		}
	}
}

if (!function_exists('tgl_indo')){
	function tgl_indo($tgl,$condition=null){
     	$tanggal = substr($tgl,8,2);
     	switch (substr($tgl,5,2)){
			case '01': 
				$bulan= "Januari";
				break;
			case '02':
				$bulan= "Februari";
				break;
			case '03':
				$bulan= "Maret";
				break;
			case '04':
				$bulan= "April";
				break;
			case '05':
				$bulan= "Mei";
				break;
			case '06':
				$bulan= "Juni";
				break;
			case '07':
				$bulan= "Juli";
				break;
			case '08':
				$bulan= "Agustus";
				break;
			case '09':
				$bulan= "September";
				break;
			case '10':
				$bulan= "Oktober";
				break;
			case '11':
				$bulan= "November";
				break;
			case '12':
				$bulan= "Desember";
				break;
		}
		$tahun = substr($tgl,0,4);

		if($condition !== 'berita'){
			return $tanggal.' '.$bulan.' '.$tahun;
		}else{
			$jam = explode(' ',$tgl);
			$datetime = new DateTime($tgl);
			return hari_indo(date('N',$datetime->getTimestamp())).', '.$tanggal.' '.$bulan.' '.$tahun.' - '.$jam[1].' WIB';
		}
     }
}

if ( ! function_exists('toTime'))
{
	function toTime($date){
		$time=strtotime($date);
		return date("H:i A",$time);
	}
}