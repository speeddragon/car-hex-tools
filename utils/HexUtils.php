<?php

    class HexUtils {

        /**
         * Get HEX from a binary file
         *
         * @param $filename
         * @return string
         */
        public static function getHex($filename) {
            $finalHex = "";

            if (file_exists($filename)) {
                $handle = @fopen($filename, "r");

                if ($handle) {
                    while (!feof($handle)) {
                        $hex = bin2hex(fread ($handle , 4 ));
                        $finalHex .= $hex;
                    }
                    fclose($handle);
                }
            }

            return $finalHex;
        }

        /**
         * Convert HEX values to its String representation
         *
         * @param $hex
         * @return string
         */
        public static function hexToString($hex){
            $hex = strtoupper($hex);

                $string='';
                for ($i=0; $i < strlen($hex)-1; $i+=2){
                if (hexdec($hex[$i].$hex[$i+1]) <= 32 || hexdec($hex[$i].$hex[$i+1]) >= 127) {
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

        public static function printHex($hex, $length) {
            $beautify = "";
            $counter = 0;

            while ($counter < strlen($hex)) {
                $hexLine = substr($hex, $counter, $length*2);
                $beautify .= strtoupper($hexLine) . "\n";
                $counter += $length*2;
            }

            return $beautify;
        }

        public static function printString($hex, $length) {
            $beautify = "";
            $counter = 0;

            while ($counter < strlen($hex)) {
                    $hexLine = substr($hex, $counter, $length*2);
                    $beautify .= HexUtils::hexToString($hexLine) . "\n";
                    $counter += $length*2;
            }

            return $beautify;
        }


        public static function hexDiff($file1, $file2) {
            $hex1 = self::getHex($file1);
            $hex2 = self::getHex($file2);

            $hexDiff = array();

            if (strlen($hex1) != strlen($hex2)) {
                return -1;
            } else {
                for($pos = 0; $pos < strlen($hex1); $pos = $pos + 2) {
                    $byte1 = substr($hex1, $pos, 2);
                    $byte2 = substr($hex2, $pos, 2);

                    if ($byte1 == $byte2) {
                        $hexDiff[$pos] = $byte1;
                    } else {
                        $hexDiff[$pos] = array($byte1, $byte2);
                    }
                }
            }

            return $hexDiff;
        }

        public static function cleanDump($crashFile, $cleanFile, $toCleanFile) {
            // Check all files size, must match
            if (filesize($crashFile) == filesize($cleanFile) && filesize($crashFile) == filesize($toCleanFile)) {

                $crashHex = self::getHex($crashFile);
                $cleanHex = self::getHex($cleanFile);
                $toCleanHex = self::getHex($toCleanFile);

                $cleanedHex = "";

                // Process
                for($pos = 0; $pos < strlen($crashHex); $pos = $pos + 2) {
                    $crashByte = substr($crashHex, $pos, 2);
                    $cleanByte = substr($cleanHex, $pos, 2);
                    $toCleanByte = substr($toCleanHex, $pos, 2);

                    if ($cleanByte == 'ff') {
                        $cleanedHex .= $cleanByte;
                    } else {
                        $cleanedHex .= $toCleanByte;
                    }    
                }

                //echo $cleanedHex;

                // Create new file
                $newFilename = "cleaned/" . time() . ".bin";
                $fp = fopen($newFilename, "wb+");
                fwrite($fp, pack('H*', $cleanedHex));
                fclose($fp);

                return $newFilename;
            } else {
                throw new Exception("DIFFERENT_SIZES");
            }
        }
    }
