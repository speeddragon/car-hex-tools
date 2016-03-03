<?php

require('HexUtils.php');

/**
 * Class BmwCasUtils
 */
class BmwCasUtils {

    protected $_hex;

    public function __construct($filename) {
        $this->_hex = HexUtils::getHex($filename);
    }

    public function getHex() {
        return $this->_hex;
    }

    /**
     * Get VIN
     *
     * @return mixed
     */
    function getVIN() {
        $vinHex = substr($this->_hex, 3204*2, 17*2);
        return HexUtils::hexToString($vinHex);
    }

    function getISN() {
        $isnHex = substr($this->_hex, 1864*2, 16*2);
        return strtoupper($isnHex);
    }

    /**
     * Get keys information stored inside CAS
     * @return array
     */
    function getKeys() {
        $keys = array();

        for($id = 0; $id < 10; $id++) {
            // TODO: Create Keys Structure
            $key = array(
                'id' => substr($this->_hex, 2700*2 + 4*2*$id, 4*2),
                'cryptLow' => substr($this->_hex, 2768*2 +4*2*$id, 4*2),
                'cryptHigh' => substr($this->_hex, 0xAB8*2 + 2*2*$id, 2*2),
                'configuration' => substr($this->_hex, 0xAFC*2 + 4*2*$id, 4*2),
                'keyStatus' => '',

                'number' => substr($this->_hex, 0x830*2 + 2*2*$id, 2*2),
                'remoteCryptLow' => substr($this->_hex, 0x860*2 + 4*2*$id, 4*2),
                'remoteCryptHigh' => substr($this->_hex, 0x848*2 + 2*2*$id, 2*2),
                'synchronization' => substr($this->_hex, 0x88C*2 + 4*2*$id, 4*2)
            );

            $keys[] = $key;
        }
        return $keys;
    }

    /**
     * @param $info
     */
    public function addKeyData(&$info) {
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

    function getDME() {
        return strtoupper(substr($this->_hex, 2*2, 3*2));
    }

    function getEGS() {
        return substr($this->_hex, 0, 2*2);
    }

    function getHardwareVersion() {
        return substr($this->_hex, 4062*2, 4*2);
    }

    function getDateManufacturer() {
        return substr($this->_hex, 3608*2, 3*2);
    }

    function getPaintCode() {
        return HexUtils::hexToString(substr($this->_hex, 2913*2, 3*2));
    }

    function getProgramingDate() {
        return substr($this->_hex, 4057*2, 3*2);
    }

    function extractISNfromDDE() {
        $isn1 = substr($this->_hex, 136*2, 16*2);
        $isn2 = substr($this->_hex, 264*2, 16*2);
        $isn3 = substr($this->_hex, 392*2, 16*2);

        if ($isn1 == $isn2 && $isn1 == $isn3) {
            return $isn1;
        } else {
            return false;
        }
    }

    function getInfo($array, $start) {
        foreach($array as $item) {
            if ($item[0] == $start) {
                return $item;
            }
        }

        return false;
    }

    /**
     * Graphical representation of information inside the dump file
     *
     * @param $length
     * @return string
     */
    function printHexGraphic($length) {
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

        $this->addKeyData($info);

        $length *= 2;

        $item = null;
        $colorStatus = false;
        while ($counter < strlen($this->_hex)) {
            $getItem = $this->getInfo($info, $counter);

            if (!$item && $getItem) {
                $item = $getItem;
            }

            if ($getItem) {
                $beautify .= '<span style="color: white; background-color: ' . ($colorStatus ? '' : 'dark') .
                    'red;" title="'.$item[2].'">';
                $colorStatus = !$colorStatus;
            }

            $hexLine = substr($this->_hex, $counter, 1);
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
}