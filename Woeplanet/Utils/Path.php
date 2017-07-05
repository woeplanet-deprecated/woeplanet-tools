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

    static public function build_enpathified_filespec($base_dir, $id, $file_format="%s.geojson") {
        $id_path = \Woeplanet\Utils\Path::enpathify($id);
        $file_name = sprintf($file_format, $id);

        return sprintf('%s/%s/%s', $base_dir, $id_path, $file_name);
    }

    static public function deserialise_json_data($file) {
        $json = NULL;
        if (file_exists($file) && is_readable($file)) {
            if (($data = file_get_contents($file)) !== FALSE) {
                $assoc = true;
                $json = json_decode($data, $assoc);
            }
        }

        return $json;
    }
}
?>
