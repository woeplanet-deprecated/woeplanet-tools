<?php

namespace Woeplanet\Utils;

class Fields {
    static public function hasField($doc, $name) {
        return (isset($doc[$name]) && !empty($doc[$name]));
    }
}
?>
