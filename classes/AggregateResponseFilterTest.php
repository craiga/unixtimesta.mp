<?php

class AggregateResponseFilterTest extends UnitTestCase
{
	function testFilterResponseBody()
	{
		$filter = new AggregateResponseFilter(array(
			new DummyResponseFilter("inner pre", "inner post"),
			new DummyResponseFilter("outer pre", "outer post"),
		));
		$this->assertEqual("outer pre inner pre middle inner post outer post", $filter->filterResponseBody("middle"));
	}
	
	function testFilterResponseHeaders()
	{
		$filter = new AggregateResponseFilter(array(
			new DummyResponseFilter("inner pre", "inner post"),
			new DummyResponseFilter("outer pre", "outer post"),
		));
		$originalHeaders = array("middle");
		$newHeaders = $filter->filterResponseHeaders($originalHeaders);
		$this->assertEqual(1, count($newHeaders));
		$this->assertEqual("outer pre inner pre middle inner post outer post", $newHeaders[0]);
	}
	
	function testConstructorObjectArgument()
	{
		try
		{
			$filter = new AggregateResponseFilter(new DummyResponseFilter("inner pre", "inner post"));
			$this->fail();
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
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
		$newHeaders = array();
		foreach($headers as $header)
		{
			$newHeaders[] = sprintf("%s %s %s", $this->_pre, $header, $this->_post);
		}
		return $newHeaders;
	}
}