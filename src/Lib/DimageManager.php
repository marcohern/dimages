<?php

namespace Marcohern\Dimages\Lib;

/**
 * Manage Dimages
 * 
 * Manages all images stored in the dimages folder
 */
class DimageManager {
    private static $ip = 3;
    private static $ipc = '0';
    private static $storagePath = 'app/mhn/dimages';

    public function idx($index = 0) {
        return str_pad($index, self::$ip, self::$ipc, STR_PAD_LEFT);
    }

    public function dir() { return storage_path(self::$storagePath); }


    public function query(string $domain, string $slug, string $index, string $profile, string $density) {
        return "$domain.$slug.$index.$profile.$density.*";
    }

    public function fileName(string $ext, string $domain,string $slug, $index = 0, string $profile = 'original', string $density = 'original') {
        $idx = $this->idx($index);
        return "$domain.$slug.$idx.$profile.$density.$ext";
    }

    public function filePath(string $ext, string $domain,string $slug, $index = 0, string $profile = 'original', string $density = 'original') {
        return $this->dir()."/".$this->fileName($ext, $domain, $slug, $index, $profile, $density);
    }

    public function dirQuery(string $domain, string $slug, string $index, string $profile, string $density) {
        $dir = opendir($this->dir());
        $query = $this->query($domain, $slug, $index, $profile, $density);
        $files = glob($query);        
        closedir($dir);
        return $files;
    }
}