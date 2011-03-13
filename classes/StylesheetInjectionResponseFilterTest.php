<?php

class StylesheetInjectionResponseFilterTest extends UnitTestCase
{
	public function testFilterResponseBody()
	{
		$filter = new StylesheetInjectionResponseFilter("/this/css", "screen");
		$this->assertPattern(
			"/<title>yeah!<\/title>\s*<link href=\"\/this\/css\" media=\"screen\" rel=\"stylesheet\" type=\"text\/css\" \/>\s*<\/head>/",
			$filter->filterResponseBody("<html><head><title>yeah!</title></head><body></body></html>")
		);
	}
	
	public function testFilterResponseBodyDefaultMediaType()
	{
		$filter = new StylesheetInjectionResponseFilter("/this/css");
		$this->assertPattern(
			"/<title>yeah!<\/title>\s*<link href=\"\/this\/css\" media=\"all\" rel=\"stylesheet\" type=\"text\/css\" \/>\s*<\/head>/",
			$filter->filterResponseBody("<html><head><title>yeah!</title></head><body></body></html>")
		);
	}
}