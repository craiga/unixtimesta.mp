<?php

class FilesystemRequestHandlerTest extends UnitTestCase
{
	public function testHandleRequest()
	{
		$handler = new FilesystemRequestHandler("/^\/rss$/", "feed.xml");
		$this->assertFalse($handler->handleRequest("/rss?"));
		$this->assertFalse($handler->handleRequest("/rss#"));
		$this->assertFalse($handler->handleRequest("/rss/"));
		
		$response = $handler->handleRequest("/rss");
		$this->assertNotEqual(false, $response);
		if($response)
		{
			$this->assertEqual("feed.xml", $response->getFileName());
		}
	}
	
	public function testMissingPattern()
	{
		try
		{
			$handler = new FilesystemRequestHandler();
			$this->faiL("Should not allow missing pattern");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	public function testMissingDestination()
	{
		try
		{
			$handler = new FilesystemRequestHandler();
			$this->faiL("Should not allow missing pattern");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	public function testPatternMatchesMiddleOfRequestIdentifier()
	{
		$handler = new FilesystemRequestHandler("/its-ajax-(\d+)-times/", "http://ajaxor.com/?times=$1");
		$this->assertFalse($handler->handleRequest("/this/its-ajax-five-times/that"));
		$response = $handler->handleRequest("/this/its-ajax-5-times/that");
		$this->assertNotEqual(false, $response);
		if($response)
		{
			$this->assertEqual("http://ajaxor.com/?times=5", $response->getFileName());
		}
	}
	
	public function testCSS()
	{
		$handler = new FilesystemRequestHandler("/^\/style\/([\w]+)$/", "css/$1.css");
		$this->assertFalse($handler->handleRequest("/style"));
		$this->assertFalse($handler->handleRequest("/style/../screen"));
		
		$response = $handler->handleRequest("/style/screen");
		$this->assertNotEqual(false, $response);
		if($response)
		{
			$this->assertEqual("css/screen.css", $response->getFileName());
			$this->assertTrue(in_array("Content-type: text/css; charset=utf-8", $response->getResponseHeaders()));
		}
	}
	
	public function testHTM()
	{
		$handler = new FilesystemRequestHandler("/^\/static\/([\w]+)$/", "html/$1.htm");
		$response = $handler->handleRequest("/static/foobar");
		$this->assertNotEqual(false, $response);
		if($response)
		{
			$this->assertEqual("html/foobar.htm", $response->getFileName());
			$this->assertTrue(in_array("Content-type: text/html; charset=utf-8", $response->getResponseHeaders()));
		}
	}
	
	public function testHTML()
	{
		$handler = new FilesystemRequestHandler("/^\/static\/([\w]+)$/", "html/$1.html");
		$response = $handler->handleRequest("/static/foobar");
		$this->assertNotEqual(false, $response);
		if($response)
		{
			$this->assertEqual("html/foobar.html", $response->getFileName());
			$this->assertTrue(in_array("Content-type: text/html; charset=utf-8", $response->getResponseHeaders()));
		}
	}
	
	public function testJS()
	{
		$handler = new FilesystemRequestHandler("/^\/script\/([\w]+)$/", "javascript/$1.js");
		$response = $handler->handleRequest("/script/foobar");
		$this->assertNotEqual(false, $response);
		if($response)
		{
			$this->assertEqual("javascript/foobar.js", $response->getFileName());
			$this->assertTrue(in_array("Content-type: text/javascript; charset=utf-8", $response->getResponseHeaders()));
		}
	}
	
	public function testTXT()
	{
		$handler = new FilesystemRequestHandler("/^\/robots.txt$/", "robots.txt");
		$response = $handler->handleRequest("/robots.txt");
		$this->assertNotEqual(false, $response);
		if($response)
		{
			$this->assertEqual("robots.txt", $response->getFileName());
			$this->assertTrue(in_array("Content-type: text/plain; charset=utf-8", $response->getResponseHeaders()));
		}
	}
}