<?php

namespace Nbt\Stream;


trait TagFileAwareTrait {

	private $endianness = null;
	protected function isBigEndian() {
		if (is_null($this->endianness)) {
			$this->endianness = pack('d', 1) == "\77\360\0\0\0\0\0\0" ? 'big' : 'little';
		}
		return $this->endianness == 'big';
	}

}