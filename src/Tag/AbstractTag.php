<?php

namespace Nbt\Tag;

abstract class AbstractTag {

	protected $value;

	public function __construct($value) {
		$this->value = $value;
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
	}

	public abstract function getType();
}