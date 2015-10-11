<?php

namespace Nbt;

use Nbt\Tag\TagType;
use Nbt\Tag\AbstractCollectionTag;
use Nbt\Tag\CompoundTag;

class StreamReader {

	const BIG_ENDIAN = 1;
	const LITTLE_ENDIAN = 2;

	protected $endianness;

	public function __construct() {
		$this->endianness = pack('d', 1) == "\77\360\0\0\0\0\0\0" ? self::BIG_ENDIAN : self::LITTLE_ENDIAN;
	}

	public function readFromPath($filename, $wrapper = "compress.zlib://") {
		$file = fopen("{$wrapper}{$filename}", "rb");
		return $this->readFromResource($file);
	}

	public function readFromResource($file) {
		if (! is_resource($file)) {
			throw new \Exception("Input is not a resource");
		}
		$out = new \Nbt\Tag\RootTag();
		$this->readIntoNode($file, $out);
		return $out;
	}

	protected function readIntoNode($file, CompoundTag &$tag) {
		if (feof($file)) {
			return false;
		}
		$tagType = $this->readTag($file, TagType::TAG_BYTE)->getValue();
		if ($tagType == TagType::TAG_END) {
			return false;
		}
		$tagName = $this->readTag($file, TagType::TAG_STRING)->getValue();
		$childTag = $this->readTag($file, $tagType);

		$tag->set($tagName, $childTag);

		return true;
	}

	protected function readTag($file, $tagType) {
		switch ($tagType) {
			case TagType::TAG_BYTE:
				return new \Nbt\Tag\ByteTag($this->unpackNBytes($file, 1, "c"));
			case TagType::TAG_SHORT:
				$unsigned = $this->unpackNBytes($file, 2, "n");
				if ($unsigned >= pow(2, 15)) { // convert to signed
					$unsigned -= pow(2, 16);
				}
				return new \Nbt\Tag\ShortTag($unsigned);
			case TagType::TAG_INT:
				$unsigned = $this->unpackNBytes($file, 4, "N");
				if ($unsigned >= pow(2, 31)) { // conert to signed
					$unsigned = $unsigned - pow(2, 32);
				}
				return new \Nbt\Tag\IntTag($unsigned);
			case TagType::TAG_LONG:
				$first  = $this->unpackNBytes($file, 4, "N");
				$second = $this->unpackNBytes($file, 4, "N");
				return new \Nbt\Tag\LongTag(($first << 32) + $second);
			case TagType::TAG_FLOAT:
				if ($this->endianness == self::BIG_ENDIAN) {
					return new \Nbt\Tag\FloatTag($this->unpackNBytes($file, 4, 'f'));
				}
				return new \Nbt\Tag\FloatTag($this->unpackNBytes($file, 4, 'f', true));
			case TagType::TAG_DOUBLE:
				if ($this->endianness == self::BIG_ENDIAN) {
					return new \Nbt\Tag\DoubleTag($this->unpackNBytes($file, 8, 'd'));
				}
				return new \Nbt\Tag\DoubleTag($this->unpackNBytes($file, 8, 'd', true));
			case TagType::TAG_BYTE_ARRAY:
				$length = $this->readTag($file, TagType::TAG_INT)->getValue();
				$out = new \Nbt\Tag\ByteArrayTag();
				for ($i = 0; $i < $length; $i++) {
					$out->push($this->readTag($file, TagType::TAG_BYTE));
				}
				return $out;
			case TagType::TAG_STRING:
				if (!($length = $this->readTag($file, TagType::TAG_SHORT)->getValue())) {
					return new \Nbt\Tag\StringTag("");
				}
				return new \Nbt\Tag\StringTag(utf8_decode(fread($file, $length)));
			case TagType::TAG_LIST:
				$type   = $this->readTag($file, TagType::TAG_BYTE)->getValue();
				$length = $this->readTag($file, TagType::TAG_INT)->getValue();

				$out = new \Nbt\Tag\GenericList($type);
				for ($i = 0; $i < $length; $i++) {
					if (feof($file)) {
						break;
					}
					$out->push($this->readTag($file, $type));
				}
				return $out;
			case TagType::TAG_COMPOUND:
				$out = new \Nbt\Tag\CompoundTag();
				while ($this->readIntoNode($file, $out));
				return $out;
			default:
				throw new \Exception("Unsupported type `$tagType`");
		}

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