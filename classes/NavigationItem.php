<?php

class NavigationItem extends Navigation
{
	private $_label;
	private $_url;
	
	public function __construct($label, $url)
	{
		if(!is_string($label))
		{
			throw new InvalidArgumentException();
		}
		if(!is_string($url))
		{
			throw new InvalidArgumentException();
		}
		$this->_label = $label;
		$this->_url = $url;
	}
	
	public function getLabel()
	{
		return $this->_label;
	}
	
	public function getURL()
	{
		return $this->_url;
	}
}