<?php

class ResponseTest extends UnitTestCase
{
	function testDummyResponse()
	{
		$response = new DummyResponse();
		$text = $response->getResponseBody();
		$headers = $response->getResponseHeaders();
		
		$this->assertEqual("body", $text);
		$this->assertEqual(4, count($headers));
		$this->assertEqual($headers[0], "HTTP/1.1 200");
		$this->assertEqual($headers[1], "Content-type: text/plain; charset=utf-8");
		$this->assertEqual($headers[2], "Header One");
		$this->assertEqual($headers[3], "Header Two");
	}
	
	function testBodyNotString()
	{
		try
		{
			$response = new DummyResponse();
			$response->body = null;
			$response->getResponseBody();
			$this->fail("Should not allow non-string body");
		}
		catch(InvalidBodyException $e)
		{
			$this->pass();
		}
	}
	
	function testStatusCode404()
	{
		$response = new DummyResponse();
		$response->status = 404;
		$text = $response->getResponseBody();
		$headers = $response->getResponseHeaders();
		
		$this->assertEqual("body", $text);
		$this->assertEqual(4, count($headers));
		$this->assertEqual($headers[0], "HTTP/1.1 404 Not Found");
		$this->assertEqual($headers[1], "Content-type: text/plain; charset=utf-8");
		$this->assertEqual($headers[2], "Header One");
		$this->assertEqual($headers[3], "Header Two");
	}
	
	function testStatusCodeNotInteger()
	{
		try
		{
			$response = new DummyResponse();
			$response->status = null;
			$response->getResponseHeaders();
			$this->fail("Should not allow non-integer status code");
		}
		catch(InvalidHTTPStatusException $e)
		{
			$this->pass();
		}
	}
	
	function testStatusCodeNegative()
	{
		try
		{
			$response = new DummyResponse();
			$response->status = -404;
			$response->getResponseHeaders();
			$this->fail("Should not allow negative status code");
		}
		catch(InvalidHTTPStatusException $e)
		{
			$this->pass();
		}
	}
	
	function testStatusCodeZero()
	{
		try
		{
			$response = new DummyResponse();
			$response->status = 0;
			$response->getResponseHeaders();
			$this->fail("Should not allow zero status code");
		}
		catch(InvalidHTTPStatusException $e)
		{
			$this->pass();
		}
	}
	
	function testContentTypeNotString()
	{
		try
		{
			$response = new DummyResponse();
			$response->contentType = 0;
			$response->getResponseHeaders();
			$this->fail("Should not allow non-string content type");
		}
		catch(InvalidContentTypeException $e)
		{
			$this->pass();
		}
	}
	
	function testInvalidContentType()
	{
		try
		{
			$response = new DummyResponse();
			$response->contentType = "xml";
			$response->getResponseHeaders();
			$this->fail("Should not allow invalid content type");
		}
		catch(InvalidContentTypeException $e)
		{
			$this->pass();
		}
	}
	
	function testEncodingNotString()
	{
		try
		{
			$response = new DummyResponse();
			$response->textEncoding = 0;
			$response->getResponseHeaders();
			$this->fail("Should not allow non-string encoding");
		}
		catch(InvalidTextEncodingException $e)
		{
			$this->pass();
		}
	}
	
	function testHeadersNotArray()
	{
		try
		{
			$response = new DummyResponse();
			$response->headers = 0;
			$response->getResponseHeaders();
			$this->fail("Should not allow non-array header list");
		}
		catch(InvalidHeaderListException $e)
		{
			$this->pass();
		}
	}
	
	function testFilter()
	{
		$response = new DummyResponse(new DummyResponseFilter());
		$text = $response->getResponseBody();
		$headers = $response->getResponseHeaders();
		
		$this->assertEqual("A-body-Z", $text);
		$this->assertEqual(4, count($headers));
		$this->assertEqual($headers[0], "HTTP/1.1 200");
		$this->assertEqual($headers[1], "CONTENT-TYPE: TEXT/PLAIN; CHARSET=UTF-8");
		$this->assertEqual($headers[2], "HEADER ONE");
		$this->assertEqual($headers[3], "HEADER TWO");
	}
	
	function testFilterNotFilter()
	{
		try
		{
			$response = new DummyResponse(new Exception());
			$this->getResponseBody();
			$this->fail("Should not allow filter that's not a ResponseFilter or null");
		}
		catch(InvalidResponseFilterException $e)
		{
			$this->pass();
		}
	}
	
	function testSetFilter()
	{
		$response = new DummyResponse();
		$response->setFilter(new DummyResponseFilter());
		$text = $response->getResponseBody();
		$headers = $response->getResponseHeaders();
		
		$this->assertEqual("A-body-Z", $text);
		$this->assertEqual(4, count($headers));
		$this->assertEqual($headers[0], "HTTP/1.1 200");
		$this->assertEqual($headers[1], "CONTENT-TYPE: TEXT/PLAIN; CHARSET=UTF-8");
		$this->assertEqual($headers[2], "HEADER ONE");
		$this->assertEqual($headers[3], "HEADER TWO");
	}
	
	function testSetFilterNotFilter()
	{
		try
		{
			$response = new DummyResponse();
			$response->setFilter(null);
			$this->getResponseBody();
			$this->fail("Should not allow filter that's not a ResponseFilter");
		}
		catch(InvalidResponseFilterException $e)
		{
			$this->pass();
		}
	}
}

class DummyResponse extends Response
{
	public $body = "body";
	public $status = 200;
	public $contentType = "text/plain";
	public $textEncoding = "utf-8";
	public $headers = array("Header One", "Header Two");
	
	protected function _getUnfilteredResponseBody()
	{
		return $this->body;
	}
	
	protected function _getHTTPStatus()
	{
		return $this->status;
	}
	
	protected function _getContentType()
	{
		return $this->contentType;
	}
	
	protected function _getTextEncoding()
	{
		return $this->textEncoding;
	}
	
	protected function _getHeaders()
	{
		return $this->headers;
	}
}

class DummyResponseFilter extends ResponseFilter
{
	public function filterResponseBody($original, $requestIdentifier, $parameters)
	{
		return sprintf("A-%s-Z", $original);
	}
	
	public function filterResponseHeaders($headers, $requestIdentifier, $parameters)
	{
		$newHeaders = array();
		foreach($headers as $header)
		{
			$newHeaders[] = strtoupper($header);
		}
		return $newHeaders;
	}
}