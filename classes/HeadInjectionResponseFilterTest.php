<?php

class HeadInjectionResponseFilterTest extends UnitTestCase
{
	public function testFilterResponseBody()
	{
		$filter = new DummyFilter();
		$this->assertPattern("/<title>yeah!<\/title>\s*<!-- injection! -->\s*<\/head>/", $filter->filterResponseBody("<html><head><title>yeah!</title></head><body></body></html>"));
	}
	
	public function testNoInjectionWithNonHtml()
	{
		$filter = new DummyFilter();
		$this->assertNoPattern("/<!-- injection! -->/", $filter->filterResponseBody("An HTML document required <head> and </head>. Or does it?"));
	}
}

class DummyFilter extends HeadInjectionResponseFilter
{
	protected function _getInjection()
	{
		return "<!-- injection! -->";
	}
}