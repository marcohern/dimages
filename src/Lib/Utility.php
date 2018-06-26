<?php

namespace Marcohern\Dimages\Lib;

class Utility {
    
    public static function idx($index) {
        if (empty($index)) $index = 0;
        return str_pad($index, 3, "0", STR_PAD_LEFT);
    }

    public static function tempDomain() {
        return substr(md5(uniqid('sess',true)),16).'-tmp';
    }

    public static function tempSlug() {
        return substr(md5(uniqid('mhn',true)),16);
    }

    public static function padded($index) {
        if (empty($index)) $index = 0;
        return str_pad($index, 7, "0", STR_PAD_LEFT);
    }
}