<?php

namespace Marcohern\Dimages\Exceptions;

use Exception;
use Marcohern\Dimages\Exceptions\ImageException;

class ImageNotFoundException extends ImageException
{
  protected $status = 404;
}
