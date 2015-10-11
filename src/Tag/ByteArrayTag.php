<?php

namespace Nbt\Tag;

class ByteArrayTag extends AbstractCollectionTag {

	public function push(AbstractTag $value) {
		if (! ($value instanceof ByteTag)) {
			throw new \Exception("Invalid type of value in list");
		}
		parent::push($value);
	}

}