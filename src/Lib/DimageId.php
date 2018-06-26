<?php

namespace Marcohern\Dimages\Lib;

class DimageId {

    private static $folder = 'app/mhn/dimages';
    private static $file = 'dimageid.txt';

    private static function readCurrentId(string $folder, string $file) {
        $filepath = "$folder/$file";
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

    private static function saveId(string $folder, string $file, $id) {
        $filepath = "$folder/$file";
        $f = fopen($filepath, "w");
        fwrite($f,$id);
        fclose($f);
    }

    private static function getId(string $folder, string $file) {
        $id = self::readCurrentId($folder, $file);
        $id++;
        self::saveId($folder, $file, $id);
        return $id;
    }

    public static function get($folder = null, $file = null) {
        
        if (empty($folder)) $folder = storage_path(self::$folder);
        if (empty($file)) $file = self::$file;
        return self::getId($folder, $file);
    }
}