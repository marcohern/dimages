<?php

namespace Marcohern\Dimages\Lib;

use Intervention\Image\Image;
use Marcohern\Dimages\Lib\Dimage;

interface IDimager {
    function getById($id);
    function getDomain($domain);
    function getSources($domain, $slug);

    function getImage(Dimage $dimage);
    function createImage(Dimage $dimage, Image $iimage);
    function updateImage(Dimage $dimage, Image $iimage);
    function saveImage(Dimage $dimage, Image $iimage);
    function deleteImage($id);

    
}