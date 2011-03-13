<?php

class JavaScriptInjectionResponseFilter extends FooterInjectionResponseFilter
{
	private $_href;
	private $_hook;
	
	public function __construct($href)
	{
		$this->_href = $href;
	}
	
	protected function _getInjection()
	{
		return sprintf("<script src=\"%s\" type=\"text/javascript\"></script>", $this->_href);
	}
	
	protected function _getPattern($body, $requestIdentifier, $parameters)
	{
		$pattern = "/<\/body>/";
		$this->_hook = "</body>";
		
		// check for any existing inline scripts
		if(preg_match("/<script[^>]*>\s*[^\s<]/", $body, $matches) == 1)
		{
			// found inline script
			$pattern = "/" . preg_quote($matches[0], "/") . "/";
			$this->_hook = $matches[0];
		}
		
		return $pattern;
	}
	
	protected function _getReplacement($body, $requestIdentifier, $parameters)
	{
		return sprintf("\n%s\n%s", $this->_getInjection(), $this->_hook);
	}
}