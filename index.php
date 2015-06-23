<?php

class Proxy
{
	protected $curl;



	public function __construct($publicUrl, $privateUrl)
	{
		$this->curl = curl_init();

		$privateUrl =  rtrim($privateUrl,'/');

		if (isset($_SERVER['PATH_INFO']))
		{
			$privateUrl .= $_SERVER['PATH_INFO'];
		}
		else
		{
			$privateUrl .= '/';
		}

		$privateUrl .= $_SERVER['QUERY_STRING'] !== '' ? "?{$_SERVER['QUERY_STRING']}" : "";

		// set URL and other appropriate options
		curl_setopt($this->curl, CURLOPT_URL, $privateUrl);
		curl_setopt($this->curl, CURLOPT_HEADER, 0);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->curl, CURLOPT_BINARYTRANSFER, 0); // For images, etc.
		curl_setopt($this->curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
		curl_setopt($this->curl, CURLOPT_WRITEFUNCTION, [$this, 'readResponse']);
		curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, [$this, 'readHeaders']);

		$this->createRequestHeaders();
	}



	public function excute()
	{
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->requestHeaders);
		curl_exec($this->curl);
		curl_close($this->curl);
	}



	protected function readHeaders($cu, $string)
	{
		$length = strlen($string);

		if (preg_match('#^HTTP#', $string))
		{
			//return $length;
		}

		if (preg_match('#^Location:#', $string))
		{
			//$string = str_replace($this->proxy_url, $this->url, $string);
		}
		elseif (preg_match('#^Cache-Control:#', $string))
		{
			//$this->cache_control = true;
			//return;
		}
		elseif (preg_match('#^Pragma:#', $string))
		{
			//$this->pragma = true;
		}

		if ($string !== "\r\n")
		{
			if (!preg_match('#^Transfer#', $string))
			{
				header(rtrim($string));
			}
		}

		return $length;
	}



	protected function readResponse(&$cu, $string)
	{
		 $headersParsed = true;

		// Clear the Cache-Control and Pragma headers
		// if they aren't passed from the proxy application.
		if ($headersParsed === false)
		{
			if (!$this->cache_control)
			{
				header('Cache-Control: ');
			}

			if (!$this->pragma)
			{
				header('Pragma: ');
			}

			$headersParsed = true;
		}

		$length = strlen($string);

		echo $string;

		return $length;
	}



	protected function createRequestHeaders()
	{
		$headers = apache_request_headers();

		foreach ($headers as $header => $value)
		{
			switch($header)
			{
				case 'Host':
					break;

				default:
					$this->requestHeaders[] = sprintf('%s: %s', $header, $value);
					break;
			}
		} 
	}

}








//$contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
//header('Content-Type:'.$contentType);
//echo '<pre/>';
//print_r(apache_request_headers());exit;

$p = new Proxy('', 'http://tutsplus.com');
$p->excute();
