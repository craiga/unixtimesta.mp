<?php

Mock::generate("Template", "MockTemplate");

class HeaderTemplateInjectionResponseFilterTest extends UnitTestCase
{
	public function testFilterResponseBody()
	{
		$template = new MockTemplate();
		$template->expectOnce("toString");
		$template->setReturnValue("toString", "<!-- injection! -->");
		$filter = new HeaderTemplateInjectionResponseFilter($template);
		$this->assertPattern("/<body>\s*<!-- injection! -->\s*<p>Hello, world!<\/p>/", $filter->filterResponseBody("<html><head><title>yeah!</title></head><body><p>Hello, world!</p></body></html>"));
	}
}