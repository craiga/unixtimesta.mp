<?php

class XHTMLCompatabilityResponseFilter extends ResponseFilter
{
	private $_userAgent;
	private $_isCompatable;
	
	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct($userAgent = null)
	{
		if(is_null($userAgent) && isset($_SERVER["HTTP_USER_AGENT"]))
		{
			$userAgent = $_SERVER["HTTP_USER_AGENT"];
		}
		if(!is_string($userAgent))
		{
			throw new InvalidArgumentException("Supplied user agent is not a string");
		}
		$this->_userAgent = $userAgent;
	}
	
	/**
	 * Filter the headers sent.
	 * @return The filtered headers.
	 */
	public function filterResponseHeaders($headers, $requestIdentifier, $parameters)
	{
		if($this->_isXHTMLResponse($headers))
		{
			$headers = $this->_replaceContentType($headers);
		}
		return $headers;
	}
	
	private function _isXHTMLResponse($headers)
	{
		$isXHTML = false;
		foreach($headers as $header)
		{
			if(preg_match("/^Content-type:\s*application\/xhtml\+xml/i", $header) == 1)
			{
				$isXHTML = true;
				break;
			}
		}
		return $isXHTML;
	}
	
	private function _replaceContentType($headers)
	{
		$newHeaders = array();
		foreach($headers as $header)
		{
			$newHeaders[] = str_replace("application/xhtml+xml", "text/html", $header);
		}
		return $newHeaders;
	}
}