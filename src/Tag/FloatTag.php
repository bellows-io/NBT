<?php

namespace Nbt\Tag;

class FloatTag extends AbstractTag {

	public function getType() {
		return TagType::TAG_FLOAT;
	}

}