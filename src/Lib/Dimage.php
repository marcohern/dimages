<?php

namespace marcohern\Dimages\Lib;

use stdClass;

class Dimage {
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
        $r = preg_match("/(.+\/)?(?<domain>[^.]+)\.(?<slug>[^.]+)\.(?<index>[^.]+)\.(?<profile>[^.]+)\.(?<density>[^.]+)\.(?<id>[^.]+)\.(?<ext>[^.]+)$/", $filepath, $m);
        if ($r) {
            $record = new stdClass;
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
}