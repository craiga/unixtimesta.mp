<?php

class ResponseRequestHandler extends RequestHandler
{
	private $_response;
	private $_requestIdentifierPattern;
	
	public function __construct($requestIdentifierPattern, Response $response)
	{
		$this->_response = $response;
		$this->_requestIdentifierPattern = $requestIdentifierPattern;
	}
	
	protected function _handleRequest($requestIdentifier, $parameters, $requestMethod)
	{
		$response = false;
		if($this->_requestIdentifierMatches($this->_requestIdentifierPattern, $requestIdentifier))
		{
			$response = $this->_response;
		}
		return $response;
	}
}