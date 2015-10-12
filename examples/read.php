<?php

// includes composer and debugging tools
include_once __DIR__.'/bootstrap.php';

$reader = new Nbt\Stream\StreamReader();

$smallOut = $reader->readFromPath(DIR_DATA.'/smalltest.nbt');
$largeOut = $reader->readFromPath(DIR_DATA.'/bigtest.nbt');

see($smallOut, $largeOut);