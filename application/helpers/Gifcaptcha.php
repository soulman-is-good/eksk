<?php
/*
:::::::::::::::::::::::::::::::::::::::::::::::::
::                                             ::
::         CAPTCHA Validation projects         ::
::                                             ::
::             2006 10. 01. 09.56.             ::
::                                             ::
::                                             ::
:: Try on-line animated CAPTCHA form generator ::
::                                             ::
:: http://gifs.hu/phpclasses/demos/Captcha01/  ::
::                                             ::
:::::::::::::::::::::::::::::::::::::::::::::::::
*/

define ( 'VERSION', '2.00' );
define ( ANIM_FRAMES, 70 );
define ( ANIM_DELAYS, 10 );

Class Gifcaptcha {
	/*
	:::::::::::::::::::::::::::::::::::::::::::::
	::                                         ::
	::           V A R I A B L E S             ::
	::                                         ::
	:::::::::::::::::::::::::::::::::::::::::::::
	*/
	var $var01 = 64;
	var $var02 = 90;
	var $var03 = 0x66; // Red
	var $var04 = 0x66; // Green
	var $var05 = 0x00; // Blue
	var $var06 = 130;
	var $var07 = true;
	var $var08 = array ( );
	var $var09 = array ( );
	var $var10 = array ( );
	var $var11 = array ( );
	var $var12 = array ( );
	var $bg;
	var $image;
	var $width;
	var $height;
	var $font;
	var $ttf;
	var $textcolor;
	var $text;
	var $var16;
	var $var17;
	var $var18;
	/*
	:::::::::::::::::::::::::::::::::::::::::::::
	::                                         ::
	::M A I N  C L A S S  C O N S T R U C T O R::
	::                                         ::
	:::::::::::::::::::::::::::::::::::::::::::::
	*/
    public function  __construct( $text, $font, $color ) {
    $var4 = HexDec($color);
    $this->var03 = floor($var4 / pow(256, 2));
    $this->var04 = floor(( $var4 % pow(256, 2) ) / pow(256, 1));
    $this->var05 = floor(( ( $var4 % pow(256, 2) ) % pow(256, 1) ) / pow(256, 0));
    $path = X3::app()->basePath.'/images/captcha.png';
    $this->bg = imagecreatefrompng($path);
    $this->width = imageSX($this->bg);
    $this->height = imageSY($this->bg);
    $this->image = imageCreateTrueColor($this->width, $this->height);
    imageFill($this->image, 0, 0, ImageColorAllocate($this->image, 243, 250, 253));
    //$this->ttf = imageTTFBbox(26, 0, $font, $text);
    $this->textcolor = ImageColorAllocate($this->image, 30, 107, 131);
	$this->font = $font;
	$this->text = $text;
	imagettftext($this->image, 18, 0, 13, 33, $this->textcolor, $this->font, $this->text);
    for ($x = 0; $x < $this->width; $x++) {
		$xoffset = $x * $this->height;
        for ($y = 0; $y < $this->height; $y++) {
            $p = imageColorsForIndex($this->image, imageColorAt($this->image, $x, $y));
            $this->var17 [$y+$xoffset][0] = $p ['red'];
            $this->var17 [$y+$xoffset][1] = $p ['green'];
            $this->var17 [$y+$xoffset][2] = $p ['blue'];
            $this->var17 [$y+$xoffset][3] = ($p['red']==243&&$p['green']==250&&$p['blue']==253)?255:$p ['alpha'];
        }
    }
    imageDestroy($this->image);
    $this->image = imageCreateTrueColor($this->width, $this->height);
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::
	::                                         ::
	::           F U N C T I O N  0 1          ::
	::                                         ::
	:::::::::::::::::::::::::::::::::::::::::::::
	*/
	function funcs01 ( ) {
    	for ( $x = 0; $x < $this->width; $x++ ) {
    		for ( $y = 0; $y < $this->height; $y++ ) {
				$var0 = 0;
				$var0 += $this->var17 [ $x ] [ $y ];
				$var0 += $this->var17 [ ( $x + 1 ) % $this->width ] [ $y ];
				$var0 += $this->var17 [ ( $x + $this->width - 1) % $this->width ] [ $y ];
				$var0 += $this->var17 [ $x ] [ ( $y + 1 ) % $this->height ];
				$var0 += $this->var17 [ $x ] [ ( $y + $this->height - 1 ) % $this->height ];
				$var0 += $this->var17 [ ( $x + 1 ) % $this->width ] [ ( $y + 1 ) % $this->height ];
				$var0 += $this->var17 [ ( $x + $this->width - 1) % $this->width ] [ ( $y + $this->height - 1 ) % $this->height ];
				$var0 += $this->var17 [ ( $x + $this->width - 1) % $this->width ] [ ( $y + 1 ) % $this->height ];
				$var0 += $this->var17 [ ( $x + 1 ) % $this->width ] [ ( $y + $this->height - 1) % $this->height ];
				$var0 /= 9;
				$var1 [ $x ] [ $y ] = ( ( ( float ) ( $var0 / 3 ) ) * ( ( float ) ( $this->var01 / 255 ) ) );
			}
		}

    	for ( $x = 1; $x < $this->width - 1; $x++ ) {
    		for ( $y = 1; $y < $this->height - 1; $y++ ) {
				$this->var11 [ $x ] [ $y ] = ( $var1 [ $x + 1 ] [ $y ] - $var1 [ $x - 1 ] [ $y ] );
				$this->var12 [ $x ] [ $y ] = ( $var1 [ $x ] [ $y + 1 ] - $var1 [ $x ] [ $y - 1 ] );
			}
		}
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::
	::                                         ::
	::           F U N C T I O N  0 2          ::
	::                                         ::
	:::::::::::::::::::::::::::::::::::::::::::::
	*/
	function funcs02 ( ) {
		for ( $i = 0; $i < ( 255 - $this->var02 ); $i++) {
			$r = ( int ) ( $this->var03 * $i / (255 - $this->var02 ) );
			$g = ( int ) ( $this->var04 * $i / (255 - $this->var02 ) );
			$b = ( int ) ( $this->var05 * $i / (255 - $this->var02 ) );
			$this->var09 [ $i ] = array ( $r, $g, $b );
		}
		for ( $i = ( 255 - $this->var02 );  $i < 256; $i++ ) {
			$r = ( int ) ( $this->var03 + ( 255 - $this->var03 ) * ( $i + $this->var02 - 255 ) / $this->var02 );
			$g = ( int ) ( $this->var04 + ( 255 - $this->var04 ) * ( $i + $this->var02 - 255 ) / $this->var02 );
			$b = ( int ) ( $this->var05 + ( 255 - $this->var05 ) * ( $i + $this->var02 - 255 ) / $this->var02 );
			$this->var09 [ $i ] = array ( $r, $g, $b );
		}
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::
	::                                         ::
	::           F U N C T I O N  0 3          ::
	::                                         ::
	:::::::::::::::::::::::::::::::::::::::::::::
	*/
	function funcs03 ( ) {
		$this->var16 = $this->var06;
		for ( $y = 0; $y < $this->var06; $y ++ ) {
			for ($x = 0; $x < $this->var06; $x++ ) {
				$var0 = ( (float) $x ) / $this->var16;
				$var1 = ( (float) $y ) / $this->var16;
				$var2 = ( float ) ( 1 - sqrt ( $var0 * $var0 + $var1 * $var1 ) );
				if ( $var2 < 0 ) $var2 = 0;
				$this->var10 [ $x ] [ $y ] = ( int ) ( $var2 * 0xff );
			}
		}
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::
	::                                         ::
	::        C A P T C H A  F R A M E         ::
	::                                         ::
	:::::::::::::::::::::::::::::::::::::::::::::
	*/
	function Frame ( $Lx, $Ly ) {
		$this->var16 = $this->var06 - 1;
		$image = imageCreateTrueColor($this->width, $this->height);
		imagecopy($this->image, $this->bg, 0, 0, 0, 0, $this->width, $this->height);
		for ( $x = 0; $x < $this->width; $x++ ) {
			$xoffset = $x * $this->height;
			$off = sin(($x+$Lx)*3.14/35)*5+cos(($y+$Lx)*3.14/35)*5;
			for ( $y = 0; $y < $this->width; $y++ ) {
				//if($this->var17 [ $x + $yoffset ]  [ 0 ]+$this->var17 [ $x + $yoffset ]  [ 1 ]+$this->var17 [ $x + $yoffset ]  [ 2 ] > 0)
				if($this->var17 [ $y + $xoffset ]  [ 3 ] == 0)
					imageSetPixel ( $this->image, $x, $y+$off,
									imageColorAllocateAlpha ( $this->image,
										$this->var17 [ $y + $xoffset ]  [ 0 ],
										$this->var17 [ $y + $xoffset ]  [ 1 ],
										$this->var17 [ $y + $xoffset ]  [ 2 ],
										$this->var17 [ $y + $xoffset ]  [ 3 ]
									)
					);					
			}
		}
		imagecopy($image, $this->image, 0, 0, 0, 0, $this->width, $this->height);
		imageDestroy($this->image);
		$this->image = imageCreateTrueColor($this->width, $this->height);		
		ob_start ( ); imageGif ( $image ); $var2 = ob_get_contents ( ); ob_end_clean ( ); return $var2;
	}
	/*
	:::::::::::::::::::::::::::::::::::::::::::::
	::                                         ::
	::          A N I M A T E D  O U T         ::
	::                                         ::
	:::::::::::::::::::::::::::::::::::::::::::::
	*/
	function AnimatedOut ( ) {
		for ( $i = 0; $i < ANIM_FRAMES; $i++ ) {
			$j = 0;
			$f_arr [ ] = $this->Frame ( $i, 16 );
			$d_arr [ ] = ANIM_DELAYS;
		}
		$GIF = new Gifencoder ( $f_arr, $d_arr, 0, 0, 255, 255, 255, "bin" );
		return ( $GIF->GetAnimation ( ) );
	}
}
?>
