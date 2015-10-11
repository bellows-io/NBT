<?php

namespace Nbt\Tag;

class StringTag extends AbstractTag {

	public function getType() {
		return TagType::TAG_STRING;
	}

}