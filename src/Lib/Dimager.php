<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\IDimager;
use Marcohern\Dimages\Lib\Dimage;
use Marcohern\Dimages\Exceptions\DimageNotFoundException;

class Dimager implements IDimager {

    private $dir;

    protected function first($query) {
        $files = glob($query);
        if (array_key_exists(0,$files)) {
            return Dimage::fromFileName($files[0]);
        }
        throw new DimageNotFoundException("Query '$query' yielded no results.");
    }

    protected function list($query) {
        $files = glob($query);
        $r = [];
        foreach ($files as $filename) {
            $r[] = Dimage::fromFileName($filename);
        }
        return $r;
    }

    public function __construct($path = null) {
        $this->dir = $path;
    }

    public function getById($id) {
        $query = $this->dir."/*.*.*.*.*.$id.*";
        return $this->first($query);
    }

    public function getSources($domain, $slug) {
        $query = $this->dir."/$domain.$slug.*.org.org.*.*";
        return $this->list($query);
    }
}