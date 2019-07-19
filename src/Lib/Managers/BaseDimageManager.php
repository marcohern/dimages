<?php

namespace Marcohern\Dimages\Lib\Managers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use Marcohern\Dimages\Exceptions\DimageNotFoundException;
use Marcohern\Dimages\Exceptions\DimageOperationInvalidException;
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

  public function source(string $entity, string $identity, int $index = 0) : DimageName {
    $dimages = $this->sources($entity, $identity);
    foreach($dimages as $dimage) {
      if ($dimage->index === $index) return $dimage;
    }
    throw new DimageNotFoundException("Image not found:$entity/$identity/$index", 0xd9745b991e);
  }

  public function derivative(string $entity, string $identity, string $profile, string $density, int $index = 0) : DimageName {
    $dimages = $this->derivatives($entity, $identity);
    foreach($dimages as $dimage) {
      if ($dimage->index === $index &&
        $dimage->profile === $profile &&
        $dimage->density === $density) return $dimage;
    }
    throw new DimageNotFoundException("Image not found:$entity/$identity/$profile/$density/$index", 0xd9745b991e);
  }

  public function rename(DimageName $source, DimageName $target) {
    if (!$this->exists($target)) {
      $this->move($source, $target);
    } else {
      throw new DimageOperationInvalidException("Cannot rename '$source' to '$target': target exists.", 0xd9745b991e);
    }
  }

  public function switchIndex(string $entity, string $identity, int $source, int $target) {
    $dimages = $this->dimages($entity, $identity);
    $temps = [];
    $moves = [];
    foreach ($dimages as $dimage) {
      if ($dimage->index === $target) {
        $dtemp = clone $dimage;
        $dtemp->ext .= '___';
        $dsource = clone $dimage;
        $dsource->index = $source;
        $temps[] = ['from' => $dimage, 'temp' => $dtemp, 'to' => $dsource ];
      }
      else if ($dimage->index === $source) {
        $dtarget = clone $dimage;
        $dtarget->index = $target;
        $moves[] = [ 'from' => $dimage, 'to' => $dtarget ];
      }
    }
    //dd($temps);
    if (empty($moves)) throw new DimageNotFoundException("Source index not found: $entity/$identity/$source");
    foreach ($temps as $temp) $this->move($temp['from'], $temp['temp']);
    foreach ($moves as $move) $this->move($move['from'], $move['to'  ]);
    foreach ($temps as $temp) $this->move($temp['temp'], $temp['to'  ]);
  }
}