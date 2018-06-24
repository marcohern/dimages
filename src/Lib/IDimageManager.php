<?php

interface IDimageManager {
    function getFileName($domain, $slug, $index, $profile, $density, $id, $ext);
    function getTmpFileName($slug);

    function queryById($id);
    function queryFull($domain, $slug, $index = null);
    function query($domain, $slug, $profile, $density, $index = null);
}