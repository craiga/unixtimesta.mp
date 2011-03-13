<?php

class TemporaryRedirectResponse extends Response
{
	private $_location;
	
	public function __construct($location)
	{
		$this->_location = $location;
	}
	
	protected function _getUnfilteredResponseBody()
	{
		return "";
	}
	
	protected function _getContentType()
	{
		return "text/plain";
	}
	
	protected function _getTextEncoding()
	{
		return "utf-8";
	}
	
	protected function _getHTTPStatus()
	{
		return 302;
	}
	
	protected function _getHeaders()
	{
		return array("Location: " . $this->_location);
	}
	
	public function getLocation()
	{
		return $this->_location;
	}
}