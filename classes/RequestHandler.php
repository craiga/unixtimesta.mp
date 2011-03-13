<?php

abstract class RequestHandler
{
	const REQUEST_METHOD_GET = "GET";
	const REQUEST_METHOD_POST = "POST";
	
	/**
	 * Handle the request, returning either false if the request is unhandled,
	 * or a Response to the request.
	 * @throws RuntimeException
	 * @throws InvalidArgumentException
	 */
	public function handleRequest($requestIdentifier = null, $parameters = null, $requestMethod = null)
	{
		// if request identifier is not supplied, get it from $_SERVER
		if(is_null($requestIdentifier))
		{
			if(!isset($_SERVER) || !isset($_SERVER["REQUEST_URI"]))
			{
				throw new RuntimeException("Request Identifier wasn't supplied to constructor, and \$_SERVER[\"REQUEST_URI\"] is not available.");
			}
			$requestIdentifier = $_SERVER["REQUEST_URI"];
			Logger::Log(sprintf("Using request identifier \"%s\"", $requestIdentifier), Logger::INFO);
		}
		
		// if parameters are not supplied, use $_POST
		if(is_null($parameters))
		{
			if(!isset($_POST))
			{
				throw new RuntimeException("Parameters weren't supplied to constructor, and \$_POST is not available.");
			}
			$parameters = $_POST;
		}
		
		// determine request method if not supplied
		if(is_null($requestMethod))
		{
			if(!isset($_SERVER) || !isset($_SERVER["REQUEST_METHOD"]))
			{
				throw new RuntimeException("Request Method wasn't supplied to constructor, and \$_SERVER[\"REQUEST_METHOD\"] is not available.");
			}
			$requestMethod = $_SERVER["REQUEST_METHOD"];
			Logger::Log(sprintf("Using request method \"%s\"", $requestMethod), Logger::INFO);
		}
		
		// validate request method
		if(!in_array($requestMethod, array(self::REQUEST_METHOD_GET, self::REQUEST_METHOD_POST)))
		{
			throw new InvalidArgumentException("Unsupported request method was supplied.");
		}
		
		$response = $this->_handleRequest($requestIdentifier, $parameters, $requestMethod);
		
		return $response;
	}
	
	abstract protected function _handleRequest($requestIdentifier, $parameters, $requestMethod);
	
	protected function _requestIdentifierMatches($pattern, $requestIdentifier, &$matches = null)
	{
		Logger::Log(sprintf("Comparing \"%s\" with pattern \"%s\"", $requestIdentifier, $pattern), Logger::INFO);
		return preg_match($pattern, $requestIdentifier, $matches) !== 0;
	}
}
