<?php

class RequestHandlerTest extends UnitTestCase
{
	function testHandleRequest()
	{
		$handler = new DummyRequestHandler();
		$this->assertEqual("yes", $handler->handleRequest("/yes"));
	}
	
	function testHandleRequestNoHandle()
	{
		$handler = new DummyRequestHandler();
		$this->assertEqual(false, $handler->handleRequest("/no"));
	}
	
	function testMatches()
	{
		$handler = new DummyRequestHandler();
		$this->assertEqual("yes?=no", $handler->handleRequest("/yesno"));
	}
	
	function testHandleRequestWithParameters()
	{
		$handler = new DummyRequestHandler();
		$this->assertEqual("yes-one-two-three", $handler->handleRequest("/yes", array("one", "two", "three")));
	}
}

class DummyRequestHandler extends RequestHandler
{
	protected function _handleRequest($requestIdentifier, $parameters, $requestMethod)
	{
		$response = false;
		if($this->_requestIdentifierMatches("/^\/yes(\w+)?$/", $requestIdentifier, $matches))
		{
			$response = "yes";
			if(count($matches) > 1)
			{
				$response .= "?=" . $matches[1];
			}
		}
		
		if(count($parameters) > 0)
		{
			$response .= "-" . implode("-", $parameters);
		}
		
		return $response;
	}
}