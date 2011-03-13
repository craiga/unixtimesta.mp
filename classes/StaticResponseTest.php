<?php

class StaticResponseTest extends UnitTestCase
{
	function testDefaultValues()
	{
		$response = new StaticResponse("Hello, world!");
		$text = $response->getResponseBody();
		$headers = $response->getResponseHeaders();
		
		$this->assertEqual("Hello, world!", $text);
		$this->assertEqual(2, count($headers));
		$this->assertEqual($headers[0], "HTTP/1.1 200");
		$this->assertEqual($headers[1], "Content-type: application/xhtml+xml; charset=utf-8");
	}
	
	function testParamsToConstructor()
	{
		$response = new StaticResponse("Hello, world!", 777, "text/xml", "EBCDIC", array("Foo: Bar", "This: That", "Something"));
		$text = $response->getResponseBody();
		$headers = $response->getResponseHeaders();
		
		$this->assertEqual("Hello, world!", $text);
		$this->assertEqual(5, count($headers));
		$this->assertEqual($headers[0], "HTTP/1.1 777");
		$this->assertEqual($headers[1], "Content-type: text/xml; charset=EBCDIC");
		$this->assertEqual($headers[2], "Foo: Bar");
		$this->assertEqual($headers[3], "This: That");
		$this->assertEqual($headers[4], "Something");
	}
	
	function testBodyNotString()
	{
		try
		{
			$response = new StaticResponse(null);
			$response->getResponseBody();
			$this->fail("Should not allow non-string body");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	function testStatusCode404()
	{
		$response = new StaticResponse("Hello, world!", 404);
		$text = $response->getResponseBody();
		$headers = $response->getResponseHeaders();
		
		$this->assertEqual("Hello, world!", $text);
		$this->assertEqual(2, count($headers));
		$this->assertEqual($headers[0], "HTTP/1.1 404 Not Found");
		$this->assertEqual($headers[1], "Content-type: application/xhtml+xml; charset=utf-8");
	}
	
	function testStatusCodeNotInteger()
	{
		try
		{
			$response = new StaticResponse("Hello, world!", "404");
			$response->getResponseHeaders();
			$this->fail("Should not allow non-integer status code");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	function testStatusCodeNegative()
	{
		try
		{
			$response = new StaticResponse("Hello, world!", -404);
			$response->getResponseHeaders();
			$this->fail("Should not allow negative status code");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	function testStatusCodeZero()
	{
		try
		{
			$response = new StaticResponse("Hello, world!", 0);
			$response->getResponseHeaders();
			$this->fail("Should not allow zero status code");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	function testContentTypeNotString()
	{
		try
		{
			$response = new StaticResponse("Hello, world!", 404, null);
			$response->getResponseHeaders();
			$this->fail("Should not allow non-string content type");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	function testInvalidContentType()
	{
		try
		{
			$response = new StaticResponse("Hello, world!", 404, "xml");
			$response->getResponseHeaders();
			$this->fail("Should not allow invalid content type");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	function testEncodingNotString()
	{
		try
		{
			$response = new StaticResponse("Hello, world!", 404, "text/html", null);
			$response->getResponseHeaders();
			$this->fail("Should not allow non-string encoding");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	function testHeadersNotArray()
	{
		try
		{
			$response = new StaticResponse("Hello, world!", 404, "text/html", "EBCDIC", "headers");
			$response->getResponseHeaders();
			$this->fail("Should not allow non-array header list");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	function testFilter()
	{
		$response = new StaticResponse("to", 777, "text/xml", "EBCDIC", array("Foo: Bar", "This: That", "Something"), new DummyResponseFilter());
		$text = $response->getResponseBody();
		$headers = $response->getResponseHeaders();
		
		$this->assertEqual("A-to-Z", $text);
		$this->assertEqual(5, count($headers));
		$this->assertEqual($headers[0], "HTTP/1.1 777");
		$this->assertEqual($headers[1], "Content-type: text/xml; charset=EBCDIC");
		$this->assertEqual($headers[2], "Foo: Bar");
		$this->assertEqual($headers[3], "This: That");
		$this->assertEqual($headers[4], "Something");
	}
	
	function testFilterNotFilter()
	{
		try
		{
			$response = new StaticResponse("Hello, world!", 404, "text/html", "EBCDIC", array("Foobar"), new Exception());
			$response->getResponseBody();
			$this->fail("Should not allow filter that's not a ResponseFilter or null");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	function testSetFilter()
	{
		$response = new StaticResponse("to");
		$response->setFilter(new DummyResponseFilter());
		$text = $response->getResponseBody();
		$headers = $response->getResponseHeaders();
		
		$this->assertEqual("A-to-Z", $text);
		$this->assertEqual(2, count($headers));
		$this->assertEqual($headers[0], "HTTP/1.1 200");
		$this->assertEqual($headers[1], "Content-type: application/xhtml+xml; charset=utf-8");
	}
	
	function testSetFilterNotFilter()
	{
		try
		{
			$response = new StaticResponse("to");
			$response->setFilter(null);
			$response->getResponseBody();
			$this->fail("Should not allow filter that's not a ResponseFilter");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
}

class DummyResponseFilter extends ResponseFilter
{
	public function filterResponseBody($original, $requestIdentifier, $parameters)
	{
		return sprintf("A-%s-Z", $original);
	}
}