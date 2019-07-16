<?php

namespace Marcohern\Dimages\Lib\Naming;

use Marcohern\Dimages\Exceptions\SourceInvalidException;
use Marcohern\Dimages\Lib\DimageConstants;

class TemplateableDimageName extends BaseDimageName {
  
  /**
   * returns the minimzed URL Template for this image.
   * 
   * @return string URL Template
   */
  protected function urlTemplate() {
    $rurl = null;
    if ($this->isSource()) {
      if (empty($this->index)) {
        $rurl = DimageConstants::RURL_EI;
      } else {
        $rurl = DimageConstants::RURL_EIN;
      }
    } else {
      if (empty($this->index)) {
        $rurl = DimageConstants::RURL_EIPD;
      } else {
        $rurl = DimageConstants::RURL_EIPDN;
      }
    }
    if (is_null($rurl)) {
      throw new SourceInvalidException("Unable to detect url template", 0xbd478efaaf);
    }
    return $rurl;
  }

  /**
   * Returns the file template for this object.
   * 
   * @return string File Template
   */
  protected function fileTemplate() {
    $rfile = null;
    if (empty($this->profile) || empty($this->density)) {
      $rfile = DimageConstants::RFILE_EIN;
    } else {
      $rfile = DimageConstants::RFILE_EIPDN;
    }
    return $rfile;
  }

}