<?php

include_once __DIR__.'/bootstrap.php';

$reader = new Nbt\StreamReader();

$out = $reader->readFromPath(__DIR__.'/data/smalltest.nbt');

var_export($out);

/*

Expects

Nbt\Tag\RootTag::__set_state(array(
   'value' =>
  array (
    'hello world' =>
    Nbt\Tag\CompoundTag::__set_state(array(
       'value' =>
      array (
        'name' =>
        Nbt\Tag\StringTag::__set_state(array(
           'value' => 'Bananrama',
        )),
      ),
    )),
  ),
))

 */