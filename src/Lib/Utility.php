<?php

namespace Marcohern\Dimages\Lib;

class Utility {
    
    public static function idx($index) {
        if (empty($index)) return "000";
        return str_pad($index, 3, "0", STR_PAD_LEFT);
    }

    public static function tempDomain() {
        return substr(md5(uniqid('sess',true)),16).'-tmp';
    }

    public static function tempSlug() {
        return substr(md5(uniqid('mhn',true)),16);
    }
}