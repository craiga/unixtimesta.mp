<?php

class LynxCompatabilityResponseFilter extends ResponseFilter
{
	private $_userAgent;
	private $_responseFilter;
	
	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(ResponseFilter $responseFilter, $userAgent = null)
	{
		if(is_null($userAgent) && isset($_SERVER["HTTP_USER_AGENT"]))
		{
			$userAgent = $_SERVER["HTTP_USER_AGENT"];
		}
		if(!is_string($userAgent))
		{
			throw new InvalidArgumentException("Supplied user agent is not a string");
		}
		$this->_responseFilter = $responseFilter;
		$this->_userAgent = $userAgent;
	}
	
	public function filterResponseBody($body, $requestIdentifier, $parameters)
	{
		if($this->_isIncompatableUserAgent($this->_userAgent))
		{
			$body = $this->_responseFilter->filterResponseBody($body, $requestIdentifier, $parameters);
		}
		return $body;
	}
	
	public function filterResponseHeaders($headers, $requestIdentifier, $parameters)
	{
		if($this->_isIncompatableUserAgent($this->_userAgent))
		{
			$headers = $this->_responseFilter->filterResponseHeaders($headers, $requestIdentifier, $parameters);
		}
		return $headers;
	}
	
	private function _isIncompatableUserAgent($userAgent)
	{
		$isIncompatable = false;
		if(preg_match("/Lynx/", $userAgent, $matches) == 1)
		{
			$isIncompatable = true;
		}
		return $isIncompatable;
	}
}