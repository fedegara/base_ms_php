<?php
/**
 * Created by PhpStorm.
 * User: dabreu
 * Date: 11/26/20
 * Time: 10:31 a. m.
 */


use Symfony\Component\Dotenv\Dotenv;

// Constant definitions
define('ROOT_PATH', realpath(__DIR__ . '/../').'/');
define('CACHE_PATH', ROOT_PATH . 'var/cache');

// load ENV
(new Dotenv())->loadEnv(ROOT_PATH . '.env');


error_reporting(E_ERROR);