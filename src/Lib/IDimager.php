<?php

namespace Marcohern\Dimages\Lib;

use Marcohern\Dimages\Lib\Dimage;

interface IDimager {
    function getById($id);
    function getDomain($domain);
    function getSources($domain, $slug);

    function getImage(Dimage $dimage);
    function createImage(Dimage $dimage, IImage $iimage);
    function updateImage(Dimage $dimage, IImage $iimage);
    function saveImage(Dimage $dimage, IImage $iimage);
    function deleteImage($id);

    
}