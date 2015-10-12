<?php

namespace Nbt\Tag;

class ByteArrayTag extends AbstractTag implements \Iterator {

	use IterableTagTrait;

	public function push(ByteTag $value) {
		$this->value[] = $value;
		return $this;
	}

}