<?php

namespace Marcohern\Dimages\Lib;

class DimageId {

    private static $folder = 'app/mhn/dimages';
    private static $file = 'dimageid.txt';

    private static function readCurrentId(string $folder) {
        $filepath = "$folder/".self::$file;
        $id=null;
        if (file_exists($filepath)) {
            $f = fopen($filepath, "r");
            $id = fgets($f);
            fclose($f);
        } else {
            $id = 0;
        }
        return $id;
    }

    private static function saveId($id) {
        $f = fopen($filepath, "w");
        fwrite($f,$id);
        fclose($f);
    }

    private static function getId(string $folder) {
        $id = self::readCurrentId();
        $id++;
        self::saveId($id);
        return $id;
    }

    public static function get($folder = null) {
        if (empty($folder)) return self::getId(storage_path(self::$folder));
        else return self::getId(storage_path($folder));
    }
}