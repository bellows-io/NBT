<?php

namespace Nbt\Tag;

abstract class AbstractCollectionTag extends AbstractTag {

	public function __construct() {
		parent::__construct([]);
	}

	public function push(AbstractTag $value) {
		$this->value[] = $value;
		return $this;
	}

}