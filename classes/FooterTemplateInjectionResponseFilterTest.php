<?php

Mock::generate("Template", "MockTemplate");

class FooterTemplateInjectionResponseFilterTest extends UnitTestCase
{
	public function testFilterResponseBody()
	{
		$template = new MockTemplate();
		$template->expectOnce("toString");
		$template->setReturnValue("toString", "<!-- injection! -->");
		$filter = new FooterTemplateInjectionResponseFilter($template);
		$this->assertPattern("/<p>Hello, world!<\/p>\s*<!-- injection! -->\s*<\/body>/", $filter->filterResponseBody("<html><head><title>yeah!</title></head><body><p>Hello, world!</p></body></html>"));
	}
}