<?php

class MetaTagInjectionResponseFilter extends HeaderInjectionResponseFilter
{
	private $_name;
	private $_content;
	
	public function __construct($name, $content)
	{
		$this->_name = $name;
		$this->_content = $content;
	}
	
	protected function _getInjection()
	{
		return sprintf("<meta name=\"%s\" content=\"%s\" />", $this->_name, $this->_content);
	}
}