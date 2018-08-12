<?php

if(version_compare($GLOBALS['version'],'1.7.5') < 0) {
    copy("src/core/source.txt","target.txt");
}
