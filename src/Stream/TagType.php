<?php

namespace Nbt\Stream;

use Nbt\Tag\AbstractTag;

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

abstract class TagType {

	const TAG_END = 0;
	const TAG_BYTE = 1;
	const TAG_SHORT = 2;
	const TAG_INT = 3;
	const TAG_LONG = 4;
	const TAG_FLOAT = 5;
	const TAG_DOUBLE = 6;
	const TAG_BYTE_ARRAY = 7;
	const TAG_STRING = 8;
	const TAG_LIST = 9;
	const TAG_COMPOUND = 10;
	const TAG_INT_ARRAY = 11;

	public static function identifyClassName($className) {
		if ($className == ByteArrayTag::class) {
			return self::TAG_BYTE_ARRAY;
		}
		if ($className == ByteTag::class) {
			return self::TAG_BYTE;
		}
		if ($className == CompoundTag::class) {
			return self::TAG_COMPOUND;
		}
		if ($className == DoubleTag::class) {
			return self::TAG_DOUBLE;
		}
		if ($className == FloatTag::class) {
			return self::TAG_FLOAT;
		}
		if ($className == ListTag::class) {
			return self::TAG_LIST;
		}
		if ($className == IntTag::class) {
			return self::TAG_INT;
		}
		if ($className == LongTag::class) {
			return self::TAG_LONG;
		}
		if ($className == ShortTag::class) {
			return self::TAG_SHORT;
		}
		if ($className == StringTag::class) {
			return self::TAG_STRING;
		}
		return null;
	}


	public static function identify(AbstractTag $tag) {
		if ($tag instanceof ByteArrayTag) {
			return self::TAG_BYTE_ARRAY;
		}
		if ($tag instanceof ByteTag) {
			return self::TAG_BYTE;
		}
		if ($tag instanceof CompoundTag) {
			return self::TAG_COMPOUND;
		}
		if ($tag instanceof DoubleTag) {
			return self::TAG_DOUBLE;
		}
		if ($tag instanceof FloatTag) {
			return self::TAG_FLOAT;
		}
		if ($tag instanceof ListTag) {
			return self::TAG_LIST;
		}
		if ($tag instanceof IntTag) {
			return self::TAG_INT;
		}
		if ($tag instanceof LongTag) {
			return self::TAG_LONG;
		}
		if ($tag instanceof ShortTag) {
			return self::TAG_SHORT;
		}
		if ($tag instanceof StringTag) {
			return self::TAG_STRING;
		}
		return null;
	}

	public static function getTagClassName($tagType) {

		if ($tagType == self::TAG_BYTE_ARRAY) {
			return ByteArrayTag::class;
		}
		if ($tagType == self::TAG_BYTE) {
			return ByteTag::class;
		}
		if ($tagType == self::TAG_COMPOUND) {
			return CompoundTag::class;
		}
		if ($tagType == self::TAG_DOUBLE) {
			return DoubleTag::class;
		}
		if ($tagType == self::TAG_FLOAT) {
			return FloatTag::class;
		}
		if ($tagType == self::TAG_LIST) {
			return ListTag::class;
		}
		if ($tagType == self::TAG_INT) {
			return IntTag::class;
		}
		if ($tagType == self::TAG_LONG) {
			return LongTag::class;
		}
		if ($tagType == self::TAG_SHORT) {
			return ShortTag::class;
		}
		if ($tagType == self::TAG_STRING) {
			return StringTag::class;
		}
		return null;
	}

}
