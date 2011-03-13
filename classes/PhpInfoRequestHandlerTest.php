<?php

class PhpInfoRequestHandlerTest extends UnitTestCase
{
	function testHandleRequest()
	{
		$handler = new PhpInfoRequestHandler();
		$this->assertFalse($handler->handleRequest("/stuff"));
		$this->assertFalse($handler->handleRequest("/phpinfos"));
		$this->assertNotEqual(false, $handler->handleRequest("/phpinfo"));
	}
}