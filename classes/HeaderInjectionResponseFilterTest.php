<?php

class HeaderInjectionResponseFilterTest extends UnitTestCase
{
	public function testFilterResponseBody()
	{
		$filter = new DummyFilter();
		$this->assertPattern("/<body>\s*<!-- injection! -->\s*<p>Hello, world!<\/p>/", $filter->filterResponseBody("<html><head><title>yeah!</title></head><body><p>Hello, world!</p></body></html>"));
	}
}

class DummyFilter extends HeaderInjectionResponseFilter
{
	protected function _getInjection()
	{
		return "<!-- injection! -->";
	}
}