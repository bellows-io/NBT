<?php

namespace Nbt\Tag;

abstract class AbstractTag {

	protected $name;
	protected $value;

	public function __construct($value, $name = null) {
		$this->value = $value;
		$this->name = $name;
	}

	public function getValue() {
		return $this->value;
	}

	public function setValue($value) {
		$this->value = $value;
		return $this;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
		return $this;
	}
}