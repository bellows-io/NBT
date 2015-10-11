<?php

namespace Nbt;

use Nbt\Tag\TagType;

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
		$out = [];
		$this->readIntoNode($file, $out);
		return $out;
	}

	protected function readIntoNode($file, &$node) {
		if (feof($file)) {
			return false;
		}
		$tagType = $this->readType($file, TagType::TAG_BYTE);
		if ($tagType == TagType::TAG_END) {
			return false;
		}
		$tagName = $this->readType($file, TagType::TAG_STRING);
		$tagValue = $this->readType($file, $tagType);

		$node[] = [
			'type' => $tagType,
			'name' => $tagName,
			'value' => $tagValue
		];

		return true;
	}

	protected function readType($file, $tagType) {
		switch ($tagType) {
			case TagType::TAG_BYTE:
				return $this->unpackNBytes($file, 1, "c");
			case TagType::TAG_SHORT:
				$unsigned = $this->unpackNBytes($file, 2, "n");
				if ($unsigned >= pow(2, 15)) { // convert to signed
					return $unsigned - pow(2, 16);
				}
				return $unsigned;
			case TagType::TAG_INT:
				$unsigned = $this->unpackNBytes($file, 4, "N");
				if ($unsigned >= pow(2, 31)) { // conert to signed
					return $unsigned - pow(2, 32);
				}
				return $unsigned;
			case TagType::TAG_LONG:
				$first  = $this->unpackNBytes($file, 4, "N");
				$second = $this->unpackNBytes($file, 4, "N");
				return ($first << 32) + $second;
			case TagType::TAG_FLOAT:
				if ($this->endianness == self::BIG_ENDIAN) {
					return $this->unpackNBytes($file, 4, 'f');
				}
				return $this->unpackNBytes($file, 4, 'f', true);
			case TagType::TAG_DOUBLE:
				if ($this->endianness == self::BIG_ENDIAN) {
					return $this->unpackNBytes($file, 8, 'd');
				}
				return $this->unpackNBytes($file, 8, 'd', true);
			case TagType::TAG_BYTE_ARRAY:
				$length = $this->readType($file, TagType::TAG_INT);
				$out = [];
				for ($i = 0; $i < $length; $i++) {
					$out[] = $this->readType($file, TagType::TAG_BYTE);
				}
				return $out;
			case TagType::TAG_STRING:
				if (!($length = $this->readType($file, TagType::TAG_SHORT))) {
					return "";
				}
				return utf8_decode(fread($file, $length));
			case TagType::TAG_LIST:
				$type   = $this->readType($file, TagType::TAG_BYTE);
				$length = $this->readType($file, TagType::TAG_INT);
				$out = [
					'type' => $type,
					'value' => []
				];
				for ($i = 0; $i < $length; $i++) {
					if (feof($file)) {
						break;
					}
					$out['value'][] = $this->readType($file, $type);
				}
				return $out;
			case TagType::TAG_COMPOUND:
				$out = [];
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