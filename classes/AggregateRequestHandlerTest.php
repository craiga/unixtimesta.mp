<?php

class AggregateRequestHandlerTest extends UnitTestCase
{
	function testHandleRequest()
	{
		$handler = new AggregateRequestHandler(array(
			new DummyRequestHandler("/^\/one/", "one"),
			new DummyRequestHandler("/^\/two/", "two"),
			new DummyRequestHandler("/^\/three/", "three")
		));
		$this->assertEqual("one", $handler->handleRequest("/one"));
		$this->assertEqual("two", $handler->handleRequest("/two"));
		$this->assertEqual("three", $handler->handleRequest("/three"));
		$this->assertEqual(false, $handler->handleRequest("/four"));
	}
	
	function testConstructorWithEmptyArray()
	{
		$handler = new AggregateRequestHandler(array());
		$this->assertEqual(false, $handler->handleRequest("/anything"));
	}
	
	function testConstructorWithNonArray()
	{
		try
		{
			$handler = new AggregateRequestHandler(null);
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
}

class DummyRequestHandler extends RequestHandler
{
	private $_pattern;
	private $_response;
	
	public function __construct($pattern, Response $response)
	{
		$this->_pattern = $pattern;
		$this->_response = $response;
	}
	
	public function _handleRequest($requestIdentifier, $parameters, $requestMethod)
	{
		$response = false;
		if($this->_requestIdentifierMatches($this->_pattern, $requestIdentifier))
		{
			$response = $this->_response;
		}
		return $response;
	}
}