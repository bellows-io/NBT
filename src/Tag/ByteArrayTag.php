<?php

namespace Nbt\Tag;

class ByteArrayTag extends AbstractCollectionTag {

	public function push(AbstractTag $value) {
		if ($value->getType() != TagType::TAG_BYTE) {
			throw new \Exception("Invalid type of value in list");
		}
		parent::push($value);
	}

	public function getType() {
		return TagType::TAG_BYTE_ARRAY;
	}

}