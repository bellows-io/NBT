<?php

namespace Nbt\Tag;

class IntTag extends AbstractTag {

	public function getType() {
		return TagType::TAG_INT;
	}

}