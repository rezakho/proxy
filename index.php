<?php
/*
require('ProxyHandler.class.php');



$proxy = new ProxyHandler('http://localhost','http://qlake.ir');
$proxy->execute();
$proxy->readResponse();
*/

function readHeaders(&$cu, $string)
{
	$length = strlen($string);
	if (preg_match(',^Location:,', $string)) {
	$string = str_replace($this->proxy_url, $this->url, $string);
	}
	elseif(preg_match(',^Cache-Control:,', $string)) {
	$this->cache_control = true;
	}
	elseif(preg_match(',^Pragma:,', $string)) {
	$this->pragma = true;
	}
	if ($string !== "\r\n") {
	header(rtrim($string));

	}
	return $length;
}


function readResponse(&$cu, $string) {
        static $headersParsed = false;

        // Clear the Cache-Control and Pragma headers
        // if they aren't passed from the proxy application.
        if ($headersParsed === false) {
            if (!$this->cache_control) {
                header('Cache-Control: ');
            }
            if (!$this->pragma) {
                header('Pragma: ');
            }
            $headersParsed = true;
        }
        $length = strlen($string);
        echo $string;
        return $length;
    }


$proxy_url = "http://laravel.com/";

//$this->proxy_url =  rtrim($proxy_url,'/');

// Parse all the parameters for the URL
if (isset($_SERVER['PATH_INFO'])) {
    $proxy_url .= $_SERVER['PATH_INFO'];
}
else {
    // Add the '/' at the end
    $proxy_url .= '/';
}

if ($_SERVER['QUERY_STRING'] !== '') {
    $proxy_url .= "?{$_SERVER['QUERY_STRING']}";
}


// create a new cURL resource
$ch = curl_init();

// set URL and other appropriate options
curl_setopt($ch, CURLOPT_URL, $proxy_url);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_BINARYTRANSFER, true); // For images, etc.
curl_setopt($ch, CURLOPT_USERAGENT,$_SERVER['HTTP_USER_AGENT']);
//curl_setopt($ch, CURLOPT_WRITEFUNCTION, 'readResponse');
//curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'readHeaders');

// grab URL and pass it to the browser
$content  = curl_exec($ch);

$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
header('Content-Type:'.$contentType);
echo $content;
// close cURL resource, and free up system resources
curl_close($ch);
