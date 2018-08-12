<?php

if(version_compare($GLOBALS['version'],'1.7.5') < 0) {
    file_put_contents("lib/gila.min1.css",file_get_contents("src/core/lib/gila.min.css"));
}
