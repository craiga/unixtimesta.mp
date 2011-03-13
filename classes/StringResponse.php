<?php

class StringResponse extends Response
{
	private $_string;
	
	public function __construct($string)
	{
		$this->_string = $string;
	}
	
	protected function _getUnfilteredResponseBody()
	{
		return $this->_string;
	}
	
	protected function _getHeaders()
	{
		return array();
	}
	
	protected function _getHTTPStatus()
	{
		return 200;
	}
	
	protected function _getContentType()
	{
		return "application/xhtml+xml";
	}
	
	protected function _getTextEncoding()
	{
		return "utf-8";
	}
}