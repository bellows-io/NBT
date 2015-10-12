<?php

namespace Nbt\Tag;

trait IterableTagTrait {

	public function current() {
		return current($this->value);
	}

	public function key() {
		return key($this->value);
	}

	public function next() {
		return next($this->value);
	}

	public function rewind() {
		return rewind($this->value);
	}

	public function valid() {
		return isset($this->value[$this->key()]);
	}


}