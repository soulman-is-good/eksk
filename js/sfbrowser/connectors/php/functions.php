<?php
if (!function_exists("dump")) {
	function dump($s) {
		echo "<pre>";
		print_r($s);
		echo "</pre>";
	}
}

if (!function_exists("trace")) {
	function trace($s) {
		if (SFB_DEBUG || 1) {
			$oFile = @fopen("log.txt", "a");
			$sDump  = $s."\n";
			@fputs ($oFile, $sDump );
			@fclose($oFile);
		}
	}
}
?>