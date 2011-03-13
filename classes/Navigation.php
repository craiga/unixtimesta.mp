<?php

class Navigation implements ArrayAccess, Iterator
{
	private $_children;
	private $_pointer;
	
	public function __construct()
	{
		$this->_children = array();
		$this->_pointer = 0;
	}
	
	/**
	 * Returns the current element.
	 */
	public function current()
	{
		return $this->_children[$this->_pointer];
	}
	
	/**
	 * Return the key of the current element.
	 */
	public function key()
	{
		return $this->_pointer;
	} 
	
	/**
	 * Moves the current position to the next element.
	 */
	public function next()
	{
		$this->_pointer++;
	}
	
	/**
	 * Checks if current position is valid.
	 */
	public function valid()
	{
		return $this->_pointer < count($this->_children);
	}
	
	/**
	 * Rewind the Iterator to the first element.
	 */
	public function rewind()
	{
		$this->_pointer = 0;
	}
	
	/**
	 * Whether a offset exists.
	 * @param {string} $offset
	 * @throws InvalidArgumentException
	 */
	public function offsetExists($offset)
	{
		if(!is_numeric($offset))
		{
			throw new InvalidArgumentException();
		}
		return isset($this->_children[(int)$offset]);
	}
	
	/**
	 * Returns the value at specified offset.
	 * @param {string} $offset
	 * @throws InvalidArgumentException
	 */
	public function offsetGet($offset)
	{
		if(!is_numeric($offset))
		{
			throw new InvalidArgumentException();
		}
		return $this->_children[(int)$offset];
	}
	
	/**
	 * Assigns a value to the specified offset.
	 * @param {string} $offset
	 * @param {string} $value
	 * @throws InvalidArgumentException
	 */
	public function offsetSet($offset, $value)
	{
		if(is_null($offset))
		{
			$offset = count($this->_children);
		}
		if(!is_numeric($offset))
		{
			throw new InvalidArgumentException();
		}
		if(!($value instanceof NavigationItem))
		{
			throw new InvalidArgumentException();
		}
		$this->_children[(int)$offset] = $value;
	}
	
	/**
	 * Unsets an offset.
	 * @param {string} $offset
	 * @throws InvalidArgumentException
	 */
	public function offsetUnset($offset)
	{
		if(!is_numeric($offset))
		{
			throw new InvalidArgumentException();
		}
		unset($this->_children[(int)$offset]);
	}
	
	public function getNumberOfChildren()
	{
		return count($this->_children);
	}
}