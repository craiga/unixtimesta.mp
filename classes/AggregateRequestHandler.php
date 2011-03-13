<?php

class AggregateRequestHandler extends RequestHandler
{
	private $_requestHandlers;
	
	/**
	 * @throws InvalidArgumentException
	 */
	public function __construct(array $requestHandlers)
	{
		if(!is_array($requestHandlers))
		{
			throw new InvalidArgumentException();
		}
		$this->_requestHandlers = $requestHandlers;
	}
	
	protected function _handleRequest($requestIdentifier, $parameters, $requestMethod)
	{
		$response = false;
		foreach($this->_requestHandlers as $requestHandler)
		{
			Logger::Log(sprintf("Attempting to handle request with %s", get_class($requestHandler), Logger::DEBUG));
			$response = $requestHandler->handleRequest($requestIdentifier, $parameters, $requestMethod);
			if($response)
			{
				Logger::Log(sprintf("Successfully handled request with %s", get_class($requestHandler), Logger::DEBUG));
				break; // exit foreach loop
			}
		}
		return $response;
	}
}