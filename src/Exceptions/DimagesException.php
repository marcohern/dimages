<?php

namespace Marcohern\Dimages\Exceptions;

use Exception;

class DimagesException extends Exception {
  protected $status = 400;

  public function render($request) {
    $body = [
      'exception' => get_class($this),
      'code' => '0x'.dechex($this->getCode()),
      'intcode' => $this->getCode(),
      'message' => $this->getMessage(),
      'file' => $this->getFile(),
      'line' => $this->getLine(),
    ];
    $debug = config('app.debug');
    if ($debug) {
      $body['trace'] = $this->getTrace();
    }
    return response($body, $this->status);
  }
}