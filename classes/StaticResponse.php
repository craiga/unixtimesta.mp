<?php

class StaticResponse extends Response
{
	private $_body;
	private $_status;
	private $_contentType;
	private $_encoding;
	private $_headers;
	private $_filter;
	
	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct($body, $status = 200,
			$contentType = "application/xhtml+xml", $encoding = "utf-8",
			array $headers = array(), ResponseFilter $filter = null)
	{
		$this->_body = $body;
		$this->_status = $status;
		$this->_contentType = $contentType;
		$this->_encoding = $encoding;
		$this->_headers = $headers;
		
		parent::__construct($filter);
	}
	
	protected function _getUnfilteredResponseBody()
	{
		return $this->_body;
	}
	
	protected function _getHeaders()
	{
		return $this->_headers;
	}
	
	protected function _getHTTPStatus()
	{
		return $this->_status;
	}
	
	protected function _getContentType()
	{
		return $this->_contentType;
	}
	
	protected function _getTextEncoding()
	{
		return $this->_encoding;
	}
}