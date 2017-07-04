<?php

namespace Woeplanet\Utils;

class Path {
    static public function enpathify($id) {
        $temp = strval($id);
        $elements = [];

        while (strlen($temp) > 3) {
            $elements[] = substr($temp, 0, 3);
            $temp = substr($temp, 3);
        }

        if (!empty($temp) || $temp === '0') {
            $elements[] = $temp;
        }

        return implode('/', $elements);
    }

    static public function createPath($path) {
        if (is_dir($path)) {
            return true;
        }
        $prev_path = substr($path, 0, strrpos($path, '/', -2) + 1 );
        $return = self::createPath($prev_path);
        return ($return && is_writable($prev_path)) ? mkdir($path) : false;
    }
}
?>
