<?php

	function removeToDisplay($hex) {
		$remove = array( "09", "0a", "0b", "0d");
	}

	function getHex($file) {
		$finalHex = "";
		$handle = @fopen($file, "r");
		if ($handle) {
    			while (!feof($handle)) {
        			$hex = bin2hex(fread ($handle , 4 ));
        			$finalHex .= $hex;
    			}
    			fclose($handle);
		}

		return $finalHex;
	}

	function hexToStr($hex){
		$hex = strtoupper($hex);

    		$string='';
    		for ($i=0; $i < strlen($hex)-1; $i+=2){
			if (hexdec($hex[$i].$hex[$i+1]) <= 32 || hexdec($hex[$i].$hex[$i+1]) == 127) {
				$string .= ".";
			} else if (hexdec($hex[$i].$hex[$i+1]) == 60) {
				$string .= "&lt;";
			} else if (hexdec($hex[$i].$hex[$i+1]) == 62) {
				$string .= "&gt;";
			} else if ($hex[$i].$hex[$i+1] == "FF") {
				$string .= "_";
			} else {
				$string .= chr(hexdec($hex[$i].$hex[$i+1]));
			}
    		}
    		return $string;
	}

	function printHex($hex, $length) {
		$beautify = "";
		$counter = 0;

		while ($counter < strlen($hex)) {
			$hexLine = substr($hex, $counter, $length*2);
			$beautify .= strtoupper($hexLine) . "\n";
			$counter += $length*2;
		}

		return $beautify;
	}

	function getInfo($array, $start) {
		foreach($array as $item) {
			if ($item[0] == $start) {
				return $item;
			}
		}

		return false;
	}

	function addKeyData(&$info) {
		for($i = 0; $i < 10; $i++) {
			$info[] = array( 2700*2 + 4*2*$i, 4*2, "Key " . ($i+1) . " Serial Number");
			$info[] = array( 2768*2 + 4*2*$i, 4*2, "Key " . ($i+1) . " Crypt Low");
			$info[] = array( 0xAB8*2 + 2*2*$i, 2*2, "Key " . ($i+1) . " Crypt High");
			$info[] = array( 0xAFC*2 + 4*2*$i, 4*2, "Key " . ($i+1) . " Configuration");
			$info[] = array( 0x830*2 + 2*2*$i, 2*2, "Key " . ($i+1) . " Number");
			$info[] = array( 0x860*2 + 4*2*$i, 4*2, "Key " . ($i+1) . " Remote Crypt Low");
			$info[] = array( 0x848*2 + 2*2*$i, 2*2, "Key " . ($i+1) . " Remote Crypt High");
			$info[] = array( 0x88C*2 + 4*2*$i, 4*2, "Key " . ($i+1) . " Synchronization");
		}
	}

	function printHexGraphic($hex, $length) {
		$beautify = "";
                $counter = 0;

		$info = array(
				array(0, 4, "EGS"),
				array(4, 6, "DME"),
				array(2913*2, 3*2, "Paint Color Code"),
				array(1864*2, 16*2, "ISN"),
				array(4057*2, 3*2, "Programming Date"),
				array(4049*2, 7*2, "Short VIN"),
				array(4062*2, 4*2, "HW Version"),
				array(3608*2, 3*2, "Date Manufacturer"),
				array(3204*2,  17*2, "VIN")
		);

		addKeyData($info);

		$length *= 2;

		$item = null;
		$colorStatus = false;
                while ($counter < strlen($hex)) {
			$getItem = getInfo($info, $counter);

			if (!$item && $getItem) {
				$item = $getItem;
			}

			if ($getItem) {
				$beautify .= '<span style="color: white; background-color: '.($colorStatus ? '' : 'dark').'red;" title="'.$item[2].'">';
				$colorStatus = !$colorStatus;
			}

                        $hexLine = substr($hex, $counter, 1);
                        $beautify .= strtoupper($hexLine);

			if ($item && ($item[0] + $item[1] - 1) == $counter) {
				$beautify .= '</span>';
				$item = null;
			}

			if ($counter % $length == ($length-1) ) {
				$beautify .= "\n";
			}

                        $counter += 1;
                }

                return $beautify;
	}

	function printStr($hex, $length) {
		$beautify = "";
                $counter = 0;

                while ($counter < strlen($hex)) {
                        $hexLine = substr($hex, $counter, $length*2);
                        $beautify .= hexToStr($hexLine) . "\n";
                        $counter += $length*2;
                }

                return $beautify;
	}

	# ----

	function getVIN($file) {
		$hex = getHex($file);
		$vinHex = substr($hex,3204*2,  17*2);

		return hexToStr($vinHex);
	}

	function getISN($file) {
		$hex = getHex($file);
		$isnHex = substr($hex, 1864*2, 16*2);

		return strtoupper($isnHex);
	}

	function getKeys($file) {
		$keys = array();

		// TODO: Create Keys Structure
		$hex = getHex($file);

		for($id = 0; $id < 10; $id++) {
			$key = array(
				'id' => substr($hex, 2700*2 + 4*2*$id, 4*2),
				'cryptLow' => substr($hex, 2768*2 +4*2*$id, 4*2),
				'cryptHigh' => substr($hex, 0xAB8*2 + 2*2*$id, 2*2),
				'configuration' => substr($hex, 0xAFC*2 + 4*2*$id, 4*2),
				'keyStatus' => '',

				'number' => substr($hex, 0x830*2 + 2*2*$id, 2*2),
				'remoteCryptLow' => substr($hex, 0x860*2 + 4*2*$id, 4*2),
				'remoteCryptHigh' => substr($hex, 0x848*2 + 2*2*$id, 2*2),
				'synchronization' => substr($hex, 0x88C*2 + 4*2*$id, 4*2)
			);
			$keys[] = $key;
		}
		return $keys;
	}

	function getDME($file) {
		$hex = getHex($file);
		return strtoupper(substr($hex, 2*2, 3*2));
	}

	function getEGS($file) {
		$hex = getHex($file);
                return substr($hex, 0, 2*2);
	}

	function getHardwareVersion($file) {
		$hex = getHex($file);
                return substr($hex, 4062*2, 4*2);
	}

	function getDateManufacturer($file) {
                $hex = getHex($file);
                return substr($hex, 3608*2, 3*2);
        }

	function getPaintCode($file) {
		$hex = getHex($file);
                return hexToStr(substr($hex, 2913*2, 3*2));
	}

	function getProgramingDate($file) {
		$hex = getHex($file);
		return substr($hex, 4057*2, 3*2);
	}

	function extractISNfromDDE($file) {
		$hex = getHex($file);

		$isn1 = substr($hex, 136*2, 16*2);
		$isn2 = substr($hex, 264*2, 16*2);
		$isn3 = substr($hex, 392*2, 16*2);

		if ($isn1 == $isn2 && $isn1 == $isn3) {
			return $isn1;
		} else {
			return false;
		}
	}

	function hexDiff($file1, $file2) {
		$hex1 = getHex($file1);
		$hex2 = getHex($file2);

		$hexDiff = array();

		if (strlen($hex1) != strlen($hex2)) {
			echo "Different file sizes!";
		} else {
			$i = 0;
			for($pos = 0; $pos < strlen($hex1); $pos = $pos + 2) {
				$byte1 = substr($hex1, $pos, 2);
				$byte2 = substr($hex2, $pos, 2);

				if ($byte1 == $byte2) {
					$hexDiff[$pos] = $byte1;
				} else {
					$hexDiff[$pos] = array($byte1, $byte2);
				}
				$i++;
			}
		}

		return $hexDiff;
	}
