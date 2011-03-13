<?php

Mock::generate("Template", "MockTemplate");

class TemplateResponseTest extends UnitTestCase
{
	public function testTemplateResponse()
	{
		$template = new MockTemplate();
		$template->expectOnce("toString");
		$template->setReturnValue("toString", "body");
		$response = new TemplateResponse($template);
		$responseBody = $response->getResponseBody();
		$this->assertEqual("body", $responseBody);
	}
	
	public function testToStringNotCalledInConstructor()
	{
		$template = new MockTemplate();
		$template->expectCallCount("toString", 0);
		$response = new TemplateResponse($template);
	}
	
	
}