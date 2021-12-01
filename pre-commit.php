<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

exec("git diff --cached --name-only --diff-filter=ACM", $output, $return_var);
