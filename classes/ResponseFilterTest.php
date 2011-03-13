<?php

class ResponseFilterTest extends UnitTestCase
{
	function testFilterResponseBody()
	{
		$filter = new DummyResponseFilter("pre", "post");
		$this->assertEqual("pre middle post", $filter->filterResponseBody("middle"));
	}
	
	function testFilterResponseHeaders()
	{
		$filter = new DummyResponseFilter("pre", "post");
		$originalHeaders = array("one", "two", "three");
		$headers = $filter->filterResponseHeaders($originalHeaders);
		$this->assertEqual(3, count($headers));
		$this->assertEqual("uno", $headers[0]);
		$this->assertEqual("deux", $headers[1]);
		$this->assertEqual("trois", $headers[2]);
	}
}

class DummyResponseFilter extends ResponseFilter
{
	private $_pre;
	private $_post;
	
	public function __construct($pre, $post)
	{
		$this->_pre = $pre;
		$this->_post = $post;
	}
	
	public function filterResponseBody($body, $requestIdentifier, $parameters)
	{
		return sprintf("%s %s %s", $this->_pre, $body, $this->_post);
	}
	
	public function filterResponseHeaders($headers, $requestIdentifier, $parameters)
	{
		return array("uno", "deux", "trois");
	}
}