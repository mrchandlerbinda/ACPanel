<?php

class UserBars
{
	private $srvName = false;
	private $srvInfo = false;
	private $iconOnline = false;
	private $iconType = false;
	private $position = 0;
	private $options = array();

	public function create($srvName, $srvInfo, $iconOnline, $iconType, $position, $options, $barType = "mb")
	{
		if( $srvName !== FALSE && strlen($srvName) > 0 )
			$this->srvName = $srvName;
		if( $srvInfo !== FALSE )
			$this->srvInfo = $srvInfo;
		if( $iconOnline !== FALSE )
			$this->iconOnline = $iconOnline;
		if( $iconType !== FALSE )
			$this->iconType = $iconType;
		if( $position !== FALSE && strlen($position) > 0 )
			$this->position = $position;
		if( is_array($options) )
			$this->options = $options;

		$function = "createType_".$barType;
		return $this->$function();
	}

	private function createType_mb()
	{
		$options = $this->options;

        	$image = $this->build_background($options['left'], $options['right'], 350, 20);

		if( $options['bordercolor'] )
		{
			// Get border color
			$options['bordercolor'] = $this->hex2rgb($options['bordercolor']);
			$options['bordercolor'] = imagecolorallocate($image, $options['bordercolor'][0], $options['bordercolor'][1], $options['bordercolor'][2]);

	        	$image = $this->drawBorder($image, $options['bordercolor'], 1);
		}

		if( $this->srvName !== FALSE )
			$this->build_text($image, $this->srvName, $options['textcolor'], 38, 9, "iFlash_706", 6, "#000000");
		if( $this->srvInfo !== FALSE )
			$this->build_text($image, $this->srvInfo, $options['textcolor'], 38, 18, "iFlash_705", 6);

		if( $this->position )
		{
			$this->build_text($image, "#".$this->position, "#ff3333", 348, 15, "helvdlbi", 11, "#000000", "right");
		}

		return $image;
	}

	private function createType_cb()
	{
		$options = $this->options;

        	$image = $this->build_background($options['left'], $options['right'], 88, 31, false);

		if( $options['bordercolor'] )
		{
			// Get border color
			$options['bordercolor'] = $this->hex2rgb($options['bordercolor']);
			$options['bordercolor'] = imagecolorallocate($image, $options['bordercolor'][0], $options['bordercolor'][1], $options['bordercolor'][2]);

	        	$image = $this->drawBorder($image, $options['bordercolor'], 1);
		}

		if( $this->position )
		{
			$this->build_text($image, "#".$this->position, "#ff3333", 88, 13, "helvdlbi", 9, "#222222", "center");
		}

		if( $this->srvName !== FALSE )
			$this->build_text($image, $this->srvName, $options['textcolor'], 88, 26, "MicroN55", 8, "", "center");

		return $image;
	}

	private function drawBorder($img, $color, $thickness = 1) 
	{ 
		$x1 = 0; 
		$y1 = 0; 
		$x2 = ImageSX($img) - 1; 
		$y2 = ImageSY($img) - 1; 
		
		for( $i = 0; $i < $thickness; $i++ ) 
		{ 
			ImageRectangle($img, $x1++, $y1++, $x2--, $y2--, $color); 
		}

		return $img;
	}

	private function build_background($c1, $c2, $width, $height, $sl = true)
	{
		// Create initial image
		$image = imagecreatetruecolor($width, $height);

		// Build gradient
		$this->build_gradient($image, 0, 0, $height, $width, $this->hex2rgb($c1), $this->hex2rgb($c2));

		// Save alpha
		imagealphablending($image,true);

		if( $sl )
		{
			// Add background scanlines
			$scanlines = imagecreatefrompng(dirname(__file__)."/../images/scanlines.png");
			imagealphablending($scanlines,true);
			imagecopy($image, $scanlines, 0, 0, 0, 0, $width, $height);
		}

		if( $this->iconType !== FALSE )
		{
			// Add game type icon
			$favicon = imagecreatefrompng(dirname(__file__)."/../images/games/".$this->iconType);
			imagealphablending($favicon, true);
			imagecopy($image, $favicon, 2, 2, 0, 0, 16, 16);
		}

		if( $this->iconOnline !== FALSE )
		{
			// Add status icon
			$status = imagecreatefrompng(dirname(__file__)."/../images/status_".(($this->iconOnline) ? "on" : "off").".png");
			imagealphablending($status, true);
			imagecopy($image, $status, 20, -1, 0, 0, 16, 16);
		}

		if( $sl )
		{
			// Add shine
			$scanlines = imagecreatefrompng(dirname(__file__)."/../images/shine.png");
			imagealphablending($scanlines, true);
			imagecopy($image, $scanlines, 0, 0, 0, 0, $width, $height);
		}

		return $image;
	}

	private function build_gradient($im, $x1, $y1, $height, $width, $left_color, $right_color)
	{
		// Build initial colours
		$color0 = ($left_color[0] - $right_color[0])/$width;
		$color1 = ($left_color[1] - $right_color[1])/$width;
		$color2 = ($left_color[2] - $right_color[2])/$width;

		// Loop through width
		for( $i = 0; $i <= $width; $i++ )
		{
			// Get next colour
			$red = $left_color[0] - floor($i*$color0);
			$green = $left_color[1] - floor($i*$color1);
			$blue = $left_color[2] - floor($i*$color2);

			// Draw line of current colour onto image
			$col = imagecolorallocate($im, $red, $green, $blue);
			imageline($im, $x1+$i, $y1, $x1+$i, $y1+$height, $col);
		}
	}

	private function build_text($im, $txt, $color, $x, $y, $font, $size, $border = "", $align = "left")
	{
		// Get text color
		$color = $this->hex2rgb($color);

		// Sort out alignment
		if( $align != 'left' )
		{
			$dat = imagettfbbox($size, 0, "./scripts/fonts/".$font.".ttf", $txt); 
			$W = abs($dat[2] - $dat[0]);
			if( $align == 'right' ) $x -= ($W+2);     
      			else $x = abs($x/2) - abs($W/2); 
		}

		$color = imagecolorallocate($im, $color[0], $color[1], $color[2]);

		if( $border )
		{
			$color2 = $this->hex2rgb($border);
			$color2 = imagecolorallocate($im, $color2[0], $color2[1], $color2[2]);

			// Write text border
			imagettftext($im, $size, 0, $x-1, $y-1, $color2, "./scripts/fonts/".$font.".ttf", $txt);
			imagettftext($im, $size, 0, $x-1, $y, $color2, "./scripts/fonts/".$font.".ttf", $txt);
			imagettftext($im, $size, 0, $x-1, $y+1, $color2, "./scripts/fonts/".$font.".ttf", $txt);
			imagettftext($im, $size, 0, $x, $y+1, $color2, "./scripts/fonts/".$font.".ttf", $txt);
			imagettftext($im, $size, 0, $x, $y-1, $color2, "./scripts/fonts/".$font.".ttf", $txt);
			imagettftext($im, $size, 0, $x+1, $y-1, $color2, "./scripts/fonts/".$font.".ttf", $txt);
			imagettftext($im, $size, 0, $x+1, $y, $color2, "./scripts/fonts/".$font.".ttf", $txt);
			imagettftext($im, $size, 0, $x+1, $y+1, $color2, "./scripts/fonts/".$font.".ttf", $txt);
		}

		// Write text
		imagettftext($im, $size, 0, $x, $y, $color, "./scripts/fonts/".$font.".ttf", $txt);
	}

	private function hex2rgb($c)
	{
		if(!$c) return false;
		$c = trim($c);
		$out = false;

		// Check for valid colour code
		if( preg_match("/^[0-9ABCDEFabcdef\#]+$/i", $c) )
		{
			// Remove hash
			$c = str_replace('#','',$c);

			// Parse through short colour-code
			$l = strlen($c) == 3 ? 1 : (strlen($c) == 6 ? 2 : false);
			if( $l )
			{
				// Grab RGB components
				unset($out);
				$out[0] = $out['r'] = $out['red'] = hexdec(substr($c, 0,1*$l));
				$out[1] = $out['g'] = $out['green'] = hexdec(substr($c, 1*$l,1*$l));
				$out[2] = $out['b'] = $out['blue'] = hexdec(substr($c, 2*$l,1*$l));
			}
			else
			{
				$out = false;
			}
		}
		else
		{
			$out = false;
		}

		return $out;
	}
}

?>