<?php

namespace itertools;

use Iterator;
use Traversable;
use EmptyIterator;
use ArrayIterator;


class ChainIterator implements Iterator {

	public $iterator;
	public $currentSubIterator;

	function __construct($iterator) {
		if($iterator instanceof Traversable) {
			$this->iterator = $iterator;
		} else if(is_array($iterator)) {
			$this->iterator = new ArrayIterator($iterator);
		} else {
			throw new Exception('Constructing ChainIterator with unkown param type: ' . gettype($iterator));
		}
		$this->currentSubIterator = new EmptyIterator();
	}

	function setNextValidSubIterator() {
		while($this->iterator->valid()) {
			$this->currentSubIterator = $this->iterator->current();
			$this->currentSubIterator->rewind();
			if($this->currentSubIterator->valid()) {
				return;
			}
			$this->iterator->next();
		}
		$this->currentSubIterator = new EmptyIterator();
	}

    function rewind() {
		$this->iterator->rewind();
		$this->setNextValidSubIterator();
    }

    function current() {
        return $this->currentSubIterator->current();
    }

    function key() {
        return $this->currentSubIterator->key();
    }

    function next() {
		$this->currentSubIterator->next();
		if(!$this->currentSubIterator->valid()) {
			$this->iterator->next();
			$this->setNextValidSubIterator();
		}
    }

    function valid() {
        return $this->currentSubIterator->valid();
    }
}

