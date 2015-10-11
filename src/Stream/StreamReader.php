<?php

namespace Nbt\Stream;


use Nbt\Tag\AbstractCollectionTag;
use Nbt\Tag\CompoundTag;

use Nbt\Stream\TagType;
use Nbt\Stream\TagFileAwareTrait;

class StreamReader {

	use TagFileAwareTrait;

	public function readFromPath($filename, $wrapper = "compress.zlib://") {
		$file = fopen("{$wrapper}{$filename}", "rb");
		return $this->readFromResource($file);
	}

	public function readFromResource($file) {
		if (! is_resource($file)) {
			throw new \Exception("Input is not a resource");
		}
		$out = $this->readFullTagFromStream($file);
		if (! $out instanceof CompoundTag) {
			throw new \Exception("Malformed file");
		}
		return $out;
	}

	protected function readFullTagFromStream($file) {
		if (feof($file)) {
			return false;
		}
		$tagType = $this->readTagDataFromStream($file, TagType::TAG_BYTE)->getValue();
		if ($tagType == TagType::TAG_END) {
			return false;
		}
		$tagName = $this->readTagDataFromStream($file, TagType::TAG_STRING)->getValue();

		$tag = $this->readTagDataFromStream($file, $tagType);

		$tag->setName($tagName);


		return $tag;
	}

	protected function readTagDataFromStream($file, $tagType) {

		if ($tagType == TagType::TAG_BYTE) {
			return new \Nbt\Tag\ByteTag($this->unpackNBytes($file, 1, "c"));
		}
		if ($tagType == TagType::TAG_SHORT) {
			$unsigned = $this->unpackNBytes($file, 2, "n");
			if ($unsigned >= pow(2, 15)) { // convert to signed
				$unsigned -= pow(2, 16);
			}
			return new \Nbt\Tag\ShortTag($unsigned);
		}
		if ($tagType == TagType::TAG_INT) {
			$unsigned = $this->unpackNBytes($file, 4, "N");
			if ($unsigned >= pow(2, 31)) { // conert to signed
				$unsigned = $unsigned - pow(2, 32);
			}
			return new \Nbt\Tag\IntTag($unsigned);
		}
		if ($tagType == TagType::TAG_LONG) {
			$first  = $this->unpackNBytes($file, 4, "N");
			$second = $this->unpackNBytes($file, 4, "N");
			return new \Nbt\Tag\LongTag(($first << 32) + $second);
		}
		if ($tagType == TagType::TAG_FLOAT) {
			if ($this->isBigEndian()) {
				return new \Nbt\Tag\FloatTag($this->unpackNBytes($file, 4, 'f'));
			}
			return new \Nbt\Tag\FloatTag($this->unpackNBytes($file, 4, 'f', true));
		}
		if ($tagType == TagType::TAG_DOUBLE) {
			if ($this->isBigEndian()) {
				return new \Nbt\Tag\DoubleTag($this->unpackNBytes($file, 8, 'd'));
			}
			return new \Nbt\Tag\DoubleTag($this->unpackNBytes($file, 8, 'd', true));
		}
		if ($tagType == TagType::TAG_BYTE_ARRAY) {
			$length = $this->readTagDataFromStream($file, TagType::TAG_INT)->getValue();
			$out = new \Nbt\Tag\ByteArrayTag();
			for ($i = 0; $i < $length; $i++) {
				$out->push($this->readTagDataFromStream($file, TagType::TAG_BYTE));
			}
			return $out;
		}
		if ($tagType == TagType::TAG_STRING) {
			if (!($length = $this->readTagDataFromStream($file, TagType::TAG_SHORT)->getValue())) {
				return new \Nbt\Tag\StringTag("");
			}
			return new \Nbt\Tag\StringTag(utf8_decode(fread($file, $length)));
		}
		if ($tagType == TagType::TAG_LIST) {
			$type   = $this->readTagDataFromStream($file, TagType::TAG_BYTE)->getValue();
			$length = $this->readTagDataFromStream($file, TagType::TAG_INT)->getValue();

			$out = new \Nbt\Tag\ListTag(TagType::getTagClassName($type));
			for ($i = 0; $i < $length; $i++) {
				if (feof($file)) {
				}
				$out->push($this->readTagDataFromStream($file, $type));
			}
			return $out;
		}
		if ($tagType == TagType::TAG_COMPOUND) {
			$out = new \Nbt\Tag\CompoundTag();
			while ($child = $this->readFullTagFromStream($file)) {
				$out->set($child->getName(), $child);
			}
			return $out;
		}

		throw new \Exception("Unsupported type `$tagType`");
	}

	private function unpackNBytes($file, $n, $format, $reverse = false) {
		$data = fread($file, $n);
		if ($reverse) {
			$data = strrev($data);
		}
		list(, $unpacked) = unpack($format, $data);
		return $unpacked;
	}

}