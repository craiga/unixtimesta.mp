<?php

class TemplateResponse extends Response
{
	private $_template;
	
	public function __construct(Template $template)
	{
		$this->_template = $template;
	}
	
	protected function _getUnfilteredResponseBody()
	{
		return $this->_template->toString();
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
	
	public function getTemplate()
	{
		return $this->_template;
	}
}