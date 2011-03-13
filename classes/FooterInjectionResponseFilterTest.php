<?php

class FooterInjectionResponseFilterTest extends UnitTestCase
{
	public function testFilterResponseBody()
	{
		$filter = new DummyFilter();
		$this->assertPattern("/<p>Hello, world!<\/p>\s*<!-- injection! -->\s*<\/body>/", $filter->filterResponseBody("<html><head><title>yeah!</title></head><body><p>Hello, world!</p></body></html>"));
	}
	
	public function testNoFilterMetaTag()
	{
		$filter = new DummyFilter();
		$this->assertPattern("/<p>Hello, world!<\/p>\s*<\/body>/", $filter->filterResponseBody("<html><head><title>yeah!</title><meta name=\"sol-no-injection\" value=\"footer\" /></head><body><p>Hello, world!</p></body></html>"));
	}
}

class DummyFilter extends FooterInjectionResponseFilter
{
	protected function _getInjection()
	{
		return "<!-- injection! -->";
	}
}