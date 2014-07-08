#!/usr/bin/env php
<?php

Phar::mapPhar("backup");

require "phar://backup/bin/backups.php";

__HALT_COMPILER();