<?php

include_once __DIR__.'/../vendor/autoload.php';

use \Debug\Debug;
Debug::$projectRoot = __DIR__;

function drop() {
	Debug::setCallDepth(3);
	call_user_func_array(['\\Debug\\Debug', 'show'], func_get_args());
	exit;
}
function see() {
	Debug::setCallDepth(3);
	call_user_func_array(['\\Debug\\Debug', 'show'], func_get_args());
}
