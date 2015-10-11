<?php

namespace Nbt\Tag;

class CompoundTag extends AbstractTag {

	public function __construct() {
		parent::__construct([]);
	}

	public function getType() {
		return TagType::TAG_COMPOUND;
	}

	public function set($name, AbstractTag $value) {
		$this->value[$name] = $value;
	}

}