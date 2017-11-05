<?php

class PRCY 
{
	private $userAgent = "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.2.1) Gecko/20021204";

	public function GetCY($site) 
	{
		$cy = 0;
		$url_ = str_replace("http://", "", $site);
		$url_ = str_replace("www.", "", $url_);
		$cy_url = "http://bar-navig.yandex.ru/u?ver=2&show=32&url=http://www.".$url_."/";
		$cy_data = implode("", file("$cy_url"));
		preg_match("/value=\"(.\d*)\"/", $cy_data, $cy_arr);
		
		if( $cy_arr[1] != "" )
			$cy = (int)$cy_arr[1];

		return $cy;
	}

	public function getPlusOnes($url)
	{
		$count = 0;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://clients6.google.com/rpc");
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $url . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
		$contents = curl_exec($ch);
		curl_close($ch);

		if( $contents )
		{
			$json = json_decode($contents, true);
			$count = intval( $json[0]['result']['metadata']['globalCounts']['count'] );
		}

		return (is_numeric($count)) ? $count : 0;
	}

	public function getVKLIKE($vkid, $page)
	{
		if( ($page."" != "") && ($page."" != "http://") && is_numeric($vkid) )
		{
			$contents = "";

			if( @function_exists("curl_init") )
			{	
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, 'http://vk.com/widget_like.php?app='.$vkid.'&type=mini&url='.urlencode($page));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
				curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
				$contents = trim(@curl_exec($ch));
				curl_close($ch);
	
				if( $contents )
				{
					if( preg_match('#<span\sid="stats_num">([0-9\+]+)<\/span>#', $contents, $matches) )
					{
						$vk_likes = (($matches[1] == '+1') || !is_numeric($matches[1])) ? '0' : $matches[1];
						return $vk_likes;
					}
				}
			}
		}

		return FALSE;
	}

	public function CheckBanner($site, $subject)
	{
		$check = 0;
	
		if( ($site."" != "") &&($site."" != "http://") )
		{
			$url = (substr(strtolower($site),0,7) != "http://") ? "http://".$site : $site;
			$contents = "";
	
			if( @function_exists("curl_init") )
			{	
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
				curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
				$contents = trim(@curl_exec($ch));
				curl_close($ch);

				if( $contents )
				{
					$subject = "http:\/\/".str_replace(".", "\.", $subject);
					if( $subject{strlen($subject)-1} !== "/" )
						$subject .= "\/";

					$regex_str = "#<a.+href=(\"|')".$subject."?(\"|').+>.*<\/a>#isU";
					if( preg_match($regex_str, $contents) )
						$check = 1;
				}
			}
		}

		return $check;
	}

	public function GetPR($site)
	{
		$pagerank = 0;
		$result = array("",-1);
	
		if( ($site."" != "") &&($site."" != "http://") )
		{
			$url_ = (substr(strtolower($site),0,7) != "http://") ? "http://".$site : $site;
			$host = "toolbarqueries.google.com";
			$target = "/tbr";
			$querystring = sprintf("client=navclient-auto&ch=%s&features=Rank&q=%s", $this->CheckHash($this->HashURL($url_)), urlencode("info:".$url_));
			$contents = "";
	
			if( @function_exists("curl_init") )
			{	
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, "http://".$host.$target."?".$querystring);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
				curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
				$contents = trim(@curl_exec($ch));
				curl_close($ch);
			}
			else
			{
				if( $socket  = @fsockopen($host, "80", $errno, $errstr, 30) )
				{	
					$request  = "GET $target?$querystring HTTP/1.0\r\n";
					$request .= "Host: $host\r\n";
					$request .= "User-Agent: ".$this->userAgent."\r\n";
					$request .= "Accept-Language: en-us, en;q=0.50\r\n";
					$request .= "Accept-Charset: ISO-8859-1, utf-8;q=0.66, *;q=0.66\r\n";
					$request .= "Accept: text/xml,application/xml,application/xhtml+xml,";
					$request .= "text/html;q=0.9,text/plain;q=0.8,video/x-mng,image/png,";
					$request .= "image/jpeg,image/gif;q=0.2,text/css,*/*;q=0.1\r\n";
					$request .= "Connection: close\r\n";
					$request .= "Cache-Control: max-age=0\r\n\r\n";
			
					stream_set_timeout($socket, 10);
					fwrite($socket, $request);
					$ret = '';
					while (!feof($socket))
					{
						$ret .= fread($socket, 4096);
					}
					fclose($socket);
					$contents = trim(substr($ret,strpos($ret,"\r\n\r\n") + 4));
				}
				else
				{
					$contents = trim(@file_get_contents("http://".$host.$target."?".$querystring));
				}
			}

			$result[0] = $contents;
			// Rank_1:1:0 = 0
			// Rank_1:1:5 = 5
			// Rank_1:1:9 = 9
			// Rank_1:2:10 = 10 etc.
			$p = explode(":", $contents);
			if( isset($p[2]) ) $result[1] = $p[2];
		}
	
		if( $result[1] != -1 )
			$pagerank = (int)$result[1];

		return $pagerank;
	}

	private function StrToNum($Str, $Check, $Magic)
	{
		$Int32Unit = 4294967296;  // 2^32
		$length = strlen($Str);
		for( $i = 0; $i < $length; $i++ )
		{
			$Check *= $Magic; 	
			// If the float is beyond the boundaries of integer (usually +/- 2.15e+9 = 2^31), 
			// the result of converting to integer is undefined
			// refer to http://www.php.net/manual/en/language.types.integer.php
			if( $Check >= $Int32Unit )
			{
				$Check = ($Check - $Int32Unit * (int) ($Check / $Int32Unit));
				// If the check less than -2^31
				$Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
			}
			$Check += ord($Str{$i}); 
		}
		return $Check;
	}

	private function HashURL($String)
	{
		$Check1 = $this->StrToNum($String, 0x1505, 0x21);
		$Check2 = $this->StrToNum($String, 0, 0x1003F);
		$Check1 >>= 2; 	
		$Check1 = (($Check1 >> 4) & 0x3FFFFC0 ) | ($Check1 & 0x3F);
		$Check1 = (($Check1 >> 4) & 0x3FFC00 ) | ($Check1 & 0x3FF);
		$Check1 = (($Check1 >> 4) & 0x3C000 ) | ($Check1 & 0x3FFF);	
		
		$T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) <<2 ) | ($Check2 & 0xF0F );
		$T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 & 0xF0F0000 );
		
		return ($T1 | $T2);
	}
	
	private function CheckHash($Hashnum)
	{
		$CheckByte = 0;
		$Flag = 0;
		$HashStr = sprintf('%u', $Hashnum);
		$length = strlen($HashStr);
		
		for( $i = $length - 1;  $i >= 0;  $i-- )
		{
			$Re = $HashStr{$i};
			if( 1 === ($Flag % 2) )
			{			  
				$Re += $Re;	 
				$Re = (int)($Re / 10) + ($Re % 10);
			}
			$CheckByte += $Re;
			$Flag++;	
		}
	
		$CheckByte %= 10;
		if( 0 !== $CheckByte )
		{
			$CheckByte = 10 - $CheckByte;
			if( 1 === ($Flag % 2) )
			{
				if( 1 === ($CheckByte % 2) )
				{
					$CheckByte += 9;
				}
				$CheckByte >>= 1;
			}
		}
		return '7'.$CheckByte.$HashStr;
	}
}

?>