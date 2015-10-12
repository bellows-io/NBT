<?php

// includes composer and debugging tools
include_once __DIR__.'/bootstrap.php';

use Nbt\Tag\ByteArrayTag;
use Nbt\Tag\ByteTag;
use Nbt\Tag\CompoundTag;
use Nbt\Tag\DoubleTag;
use Nbt\Tag\FloatTag;
use Nbt\Tag\ListTag;
use Nbt\Tag\IntTag;
use Nbt\Tag\LongTag;
use Nbt\Tag\ShortTag;
use Nbt\Tag\StringTag;

$writer = new Nbt\Stream\StreamWriter();

$data = (new CompoundTag())
	->setName("Level")
	->add((new LongTag(9223372036854775807))->setName("longTest"))
	->add((new ShortTag(32767))->setName("shortTest"))
	->add((new StringTag("HELLO WORLD THIS IS A TEST STRING ÅÄÖ!"))->setName("stringTest"))
	->add((new FloatTag(0.49823147058487))->setName("floatTest"))
	->add((new IntTag(2147483647))->setName("intTest"))
	->add((new CompoundTag())
		->setName("nested compound test")
		->add((new CompoundTag())
			->setName('ham')
			->add((new StringTag('Hampus'))->setName('name'))
			->add((new FloatTag(0.75))->setName('value')))
		->add((new CompoundTag())
			->setName('egg')
			->add((new StringTag('Eggbert'))->setName('name'))
			->add((new FloatTag(0.5))->setName('value'))))
	->add((new ListTag(LongTag::class))
		->setName("listTest (long)")
		->push(new LongTag(11))
		->push(new LongTag(12))
		->push(new LongTag(13))
		->push(new LongTag(14))
		->push(new LongTag(15)))
	->add((new ListTag(CompoundTag::class))
		->setName("listTest (compound)")
		->push((new CompoundTag())
			->add((new StringTag("Compound tag #0"))->setName('name'))
			->add((new LongTag(1264099775885))->setName('created-on')))
		->push((new CompoundTag())
			->add((new StringTag("Compound tag #1"))->setName('name'))
			->add((new LongTag(1264099775885))->setName('created-on'))))
	->add((new ByteTag(127))->setName('byteTest'));

$byteArrayTest = (new ByteArrayTag())->setName('byteArrayTest (the first 1000 values of (n*n*255+n*7)%100, starting with n=0 (0, 62, 34, 16, 8, ...))');
for ($i = 0; $i < 1000; $i++) {
	$value = (($i * $i * 255) + ($i * 7)) % 100;
	$byteArrayTest->push(new ByteTag($value));
}

$data->add($byteArrayTest)
	->add((new DoubleTag(0.49312871321823))->setName('doubleTest'));

$writer->writeToPath($data, __DIR__.'/outtest.nbt', '');
