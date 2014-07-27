#!/usr/bin/env php
<?php
set_time_limit(300);
date_default_timezone_set('Europe/Berlin');


Phar::mapPhar("backup");

require "phar://backup/bin/backups.php";

__HALT_COMPILER();