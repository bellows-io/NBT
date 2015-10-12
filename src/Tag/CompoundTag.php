<?php

namespace Nbt\Tag;

class CompoundTag extends AbstractTag implements \Iterator {

	use IterableTagTrait;

	public function __construct() {
		parent::__construct([]);
	}

	public function add(AbstractTag $value) {
		if (! $value->getName()) {
			throw new \Exception("Cannot add tag with no name");
		}
		if (isset($this->value[$value->getName()])) {
			throw new \Exception("CompoundTag already has a tag with name `".$value->getName()."`");
		}
		$this->value[$value->getName()] = $value;
		return $this;
	}

	public function get($name) {
		if (! isset($this->value[$name])) {
			return null;
		}
		return $this->value[$name];
	}
}
