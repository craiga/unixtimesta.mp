<?php

class MSIECompatabilityResponseFilter extends ResponseFilter
{
	private $_userAgent;
	private $_responseFilter;
	private $_version;
	
	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(ResponseFilter $responseFilter, $version = 6, $userAgent = null)
	{
		if(is_null($userAgent) && isset($_SERVER["HTTP_USER_AGENT"]))
		{
			$userAgent = $_SERVER["HTTP_USER_AGENT"];
		}
		if(!is_string($userAgent))
		{
			throw new InvalidArgumentException("Supplied user agent is not a string");
		}
		$version = (int)$version;
		if(!is_numeric($version) && $version < 1 && $version > 8)
		{
			throw new InvalidArgumentException("Supplied version is not a valid version number");
		}
		$this->_responseFilter = $responseFilter;
		$this->_userAgent = $userAgent;
		$this->_version = $version;
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
		
		
		if(preg_match("/^Mozilla.*MSIE\s+(\d)/U", $userAgent, $matches) == 1)
		{
			$version = (int)$matches[1];
			if($version <= $this->_version)
			{
				if(!strpos($userAgent, "Opera") && !strpos($userAgent, "Lobo") &&
						!strpos($userAgent, "Lunascape") && !strpos($userAgent, "Girafabot") &&
						!strpos($userAgent, "obot") && !strpos($userAgent, "SEOChat") &&
						!strpos($userAgent, "VoilaBot") && !strpos($userAgent, "BecomeBot"))
				{
					$isIncompatable = true;
				}
			}
		}
		else if($userAgent == "Enigma Browser" && $this->_version > 4)
		{
			$isIncompatable = true;
		}
		return $isIncompatable;
	}
}