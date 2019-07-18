<?php

namespace Marcohern\Dimages\Lib\Managers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Lib\DimageConstants;
use Marcohern\Dimages\Lib\DimageFunctions;
use Marcohern\Dimages\Lib\DimageSequencer;
use Marcohern\Dimages\Lib\DimageName;

class BaseDimageManager extends StorageDimageManager {

  public function sources(string $entity, string $identity, $index=null) : array {
    $dimages = $this->dimages($entity, $identity);
    $result = [];
    foreach ($dimages as $dimage) {
      if (is_null($index)) {
        if ($dimage->isSource()) $result[] = $dimage;
      } else {
        if ($dimage->isSource() && $dimage->index === $index) $result[] = $dimage;
      }
    }
    return $result;
  }

  public function derivatives(string $entity, string $identity, $index=null) : array {
    $dimages = $this->dimages($entity, $identity);
    $result = [];
    foreach ($dimages as $dimage) {
      if (is_null($index)) {
        if ($dimage->isDerived()) $result[] = $dimage;
      } else {
        if ($dimage->isDerived() && $dimage->index === $index) $result[] = $dimage;
      }
    }
    return $result;
  }

}