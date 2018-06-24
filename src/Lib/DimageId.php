<?php

namespace Marcohern\Dimages\Lib;

class DimageId {

    private static $folder = 'app/mhn/dimages';
    private static $file = 'dimageid.txt';

    private static function getId(string $folder) {
        $filepath = "$folder/".self::$file;
        $id=0;
        if (file_exists($filepath)) {
            $f = fopen($filepath, "r");
            $id = fgets($f);
            fclose($f);
            $id++;
        } else {
            $id = 1;
        }
        $f = fopen($filepath, "w");
        fwrite($f,$id);
        fclose($f);
        return $id;
    }

    public static function get($folder = null) {
        if (empty($folder)) return self::getId(storage_path(self::$folder));
        else return self::getId(storage_path($folder));
    }
}