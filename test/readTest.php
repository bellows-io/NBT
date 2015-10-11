<?php

include_once __DIR__.'/bootstrap.php';

$reader = new Nbt\StreamReader();

$out = $reader->readFromPath(__DIR__.'/data/bigtest.nbt');

drop($reader, $out);