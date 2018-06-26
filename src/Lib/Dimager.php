<?php

namespace Marcohern\Dimages\Lib;

use Intervention\Image\ImageManagerStatic as IImage;
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

    public function getDomain($domain) {
        $query = $this->dir."/$domain.*.*.org.org.*.*";
        return $this->list($query);
    }

    public function getSources($domain, $slug) {
        $query = $this->dir."/$domain.$slug.*.org.org.*.*";
        return $this->list($query);
    }

    public function getImage(Dimage $dimage) {
        $filepath = $this->dir."/".$dimage->getFileName();
        return IImage::make($filepath);
    }

    public function createImage(Dimage $dimage, IImage $iimage) {
        $dimage->id = DimageId::get();
        $filepath = $this->dir."/".$dimage->getFileName();
        $iimage->save($filepath);
        return $dimage;
    }

    public function updateImage(Dimage $dimage, IImage $iimage) {
        $filepath = $this->dir."/".$dimage->getFileName();
        $iimage->save($filepath);
        return $dimage;
    }

    public function saveImage(Dimage $dimage, IImage $iimage) {
        if (empty($dimage->id)) return $this->createImage($dimage, $iimage);
        return $this->updateImage($dimage, $iimage);
    }

    public function deleteImage($id) {
        $query = $this->dir."/*.*.*.*.*.$id.*";
        $files = glob($query);
        if (array_key_exists($files, 0)) {
            unlink($files[0]);
            return true;
        }
        return false;
    }
}