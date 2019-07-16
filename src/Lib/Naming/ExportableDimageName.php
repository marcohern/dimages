<?php

namespace Marcohern\Dimages\Lib\Naming;

use Marcohern\Dimages\Exceptions\SourceInvalidException;
use Marcohern\Dimages\Lib\DimageConstants;

class ExportableDimageName extends TemplateableDimageName {
  

  /**
   * Generates the URL for this DimageName.
   * 
   * @return string URL for this image
   */
  public function toUrl() : string {
    $urlTemplate = $this->urlTemplate();
    return $this->format($urlTemplate);
  }

  /**
   * Returns the filename of this image.
   * 
   * @return string file name
   */
  public function toFileName() {
    if (empty($this->profile) || empty($this->density)) {
      return $this->format(DimageConstants::RFILE_NAME_N);
    }
    return $this->format(DimageConstants::RFILE_NAME_PDN);
  }

  /**
   * Returns the relative path.
   * 
   * @return string image relative path
   */
  public function toIdentityPath() : string {
    return $this->format(DimageConstants::RFILE_PATH);
  }

  /**
   * Generates the File Name for this DimageName.
   * 
   * @return string File Name for this image
   */
  public function toIdentityPathFileName() : string {
    $rfile = $this->fileTemplate();
    return $this->format($rfile);
  }

  /**
   * returns the full file path from the storage root
   * 
   * @return string Image file path from storage root
   */
  public function toFullPathFileName() {
    return DimageConstants::IMAGESUBDIR.'/'.$this->toIdentityPathFileName();
  }

  /**
   * returns the full image directory from the storage root
   * 
   * @return string Image directory from storage root
   */
  public function toFullPath() {
    return DimageConstants::IMAGESUBDIR.'/'.$this->toIdentityPath();
  }
}