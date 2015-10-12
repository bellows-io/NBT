<?php

namespace Nbt\Stream;

use Nbt\Stream\TagType;
use Nbt\Stream\TagFileAwareTrait;

use Nbt\Tag\AbstractTag;
use Nbt\Tag\CompoundTag;

class StreamWriter {

	use TagFileAwareTrait;

	public function writeToPath(CompoundTag $root, $filename, $wrapper = "compress.zlib://") {
		$file = fopen("{$wrapper}{$filename}", "wb");
		return $this->writeToResource($root, $file);
	}

	public function writeToResource(CompoundTag $root, $file) {
		if (! is_resource($file)) {
			throw new \Exception("Input is not a valid resource");
		}
		$this->writeFullTagToStream($root, $file);
		return true;
	}


	public function writeFullTagToStream(AbstractTag $tag, $file) {
		$this->writeTagValueToStream(TagType::TAG_BYTE, TagType::identify($tag), $file);
		$this->writeTagValueToStream(TagType::TAG_STRING, $tag->getName(), $file);
		return $this->writeTagDataToStream($tag, $file);
	}

	public function writeTagDataToStream(AbstractTag $tag, $file) {
		$genericType = null;
		if ($tag instanceof \Nbt\Tag\ListTag) {
			$genericType = TagType::identifyClassName($tag->getGenericType());
		}
		return $this->writeTagValueToStream(TagType::identify($tag), $tag->getValue(), $file, $genericType);
	}

	protected function writeTagValueToStream($type, $value, $file, $genericType = null) {


		switch ($type) {
			case TagType::TAG_BYTE:
				return $this->writeData($file, "c", [$value]);
			case TagType::TAG_SHORT:
				if ($value < 0) {
					$value += pow(2, 16);
				}
				return $this->writeData($file, "n", [$value]);
			case TagType::TAG_INT:
				if ($value < 0) {
					$value += pow(2, 32);
				}
				return $this->writeData($file, "N", [$value]);
			case TagType::TAG_LONG:
				$highMap = 0xffffffff00000000;
				$lowMap  = 0x00000000ffffffff;

				$higher = ($value & $highMap) >> 32;
				$lower  = ($value & $lowMap);

				return $this->writeData($file, "NN", [$higher, $lower]);
			case TagType::TAG_FLOAT:
				return $this->writeData($file, "f", [$value], ! $this->isBigEndian());
			case TagType::TAG_DOUBLE:
				return $this->writeData($file, "d", [$value], ! $this->isBigEndian());
			case TagType::TAG_BYTE_ARRAY:
				if ($this->writeTagValueToStream(TagType::TAG_INT, count($value), $file)) {
					for ($i = 0; $i < count($value); $i++) {
						$this->writeTagValueToStream(TagType::TAG_BYTE, $value[$i]->getValue(), $file);
					}
					return true;
				}
				return false;
			case TagType::TAG_STRING:
				$this->writeTagValueToStream(TagType::TAG_SHORT, strlen($value), $file);
				return is_int(fwrite($file, $value));
			case TagType::TAG_LIST:
				$this->writeTagValueToStream(TagType::TAG_BYTE, $genericType, $file);
				$this->writeTagValueToStream(TagType::TAG_INT, count($value), $file);

				foreach ($value as $item) {
					$this->writeTagDataToStream($item, $file);
				}
				return true;
			case TagType::TAG_COMPOUND:
				foreach ($value as $item) {
					$this->writeFullTagToStream($item, $file);
				}
				fwrite($file, "\0");
		}
	}

	protected function writeData($file, $format, array $args, $reverse = false) {

		$args = array_merge([$format], $args);
		$data = call_user_func_array('pack', $args);
		if ($reverse) {
			$data = strrev($data);
		}
		return is_int(fwrite($file, $data));
	}

}
