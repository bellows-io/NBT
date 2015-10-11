<?php

namespace Nbt\Tag;

class ListTag extends AbstractCollectionTag {

	protected $genericType;

	public function __construct($genericType) {
		parent::__construct();
		$this->genericType = $genericType;
	}

	public function getGenericType() {
		return $this->genericType;
	}

	public function push(AbstractTag $value) {
		if (get_class($value) != $this->genericType) {
			throw new \Exception("Invalid type of value in list");
		}
		parent::push($value);
	}

}