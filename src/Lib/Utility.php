<?php

namespace Marcohern\Dimages\Lib;

class Utility {
    
    public static function idx($index) {
        if (empty($index)) return "000";
        return str_pad($index, 3, "0", STR_PAD_LEFT);
    }
}