<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Cài đặt Memcached
| -------------------------------------------------------------------------
| Bạn có thể chỉ định các máy chủ Memcached của mình dưới đây.
|
|	See: https://codeigniter.com/user_guide/libraries/caching.html#memcached
|
*/

$config = array(
	'default' => array(
		'hostname' => '127.0.0.1',
		'port'     => '11211',
		'weight'   => '1',
	),
);
