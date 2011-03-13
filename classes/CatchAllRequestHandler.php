<?php

class CatchAllRequestHandler extends RequestHandler
{
	private $_response;
	
	public function __construct(Response $response)
	{
		$this->_response = $response;
	}
	
	protected function _handleRequest($requestIdentifier, $parameters, $requestMethod)
	{
		return $this->_response;
	}
}