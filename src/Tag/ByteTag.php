<?php

namespace Nbt\Tag;

class ByteTag extends AbstractTag {

	public function getType() {
		return TagType::TAG_BYTE;
	}

}