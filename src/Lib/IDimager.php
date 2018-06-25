<?php

namespace Marcohern\Dimages\Lib;

interface IDimager {
    function getById($id);
    function getSources($domain, $slug);
}