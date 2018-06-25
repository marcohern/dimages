<?php

namespace marcohern\Dimages\Lib;

use stdClass;

class Dimage {
    private static $fileNameExp = "/(.+\/)?(?<domain>[^.]+)\.(?<slug>[^.]+)\.(?<index>[^.]+)\.(?<profile>[^.]+)\.(?<density>[^.]+)\.(?<id>[^.]+)\.(?<ext>[^.]+)$/";

    public $id;
    public $domain;
    public $slug;
    public $index;
    public $profile;
    public $density;
    public $ext;

    public static function fromStdClass(stdClass $source) {
        $dimage = new Dimage;
        $dimage->id = $source->id;
        $dimage->domain = $source->domain;
        $dimage->slug = $source->slug;
        $dimage->index = $source->index;
        $dimage->profile = $source->profile;
        $dimage->density = $source->density;
        $dimage->ext = $source->ext;
        return $dimage;
    }

    public static function fromFileName(string $filepath) {
        $m = null;
        $r = preg_match(self::$fileNameExp, $filepath, $m);
        if ($r) {
            $record = new Dimage;
            $record->id = 0 + $m['id'];
            $record->domain = $m['domain'];
            $record->slug   = $m['slug'];
            $record->index  = 0 + $m['index'];
            $record->profile = $m['profile'];
            $record->density = $m['density'];
            $record->ext = $m['ext'];
            return $record;
        }
        return false;
    }

    private function idx() {
        if (empty($this->index)) return "000";
        return str_pad($this->index, 3, "0", STR_PAD_LEFT);
    }

    public function getFileName() {
        $idx = $this->idx();
        return "{$this->domain}.{$this->slug}.$idx.{$this->profile}.{$this->density}.{$this->id}.{$this->ext}";
    }
}