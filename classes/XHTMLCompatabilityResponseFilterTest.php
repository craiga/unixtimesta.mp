<?php

class XHTMLCompatabilityResponseFilterTest extends UnitTestCase
{
	function testFilterResponseBody()
	{
		$filter = new XHTMLCompatabilityResponseFilter();
		$result = $filter->filterResponseHeaders(array("Content-Type: application/xhtml+xml; charset=utf-8"));
		if(!in_array("Content-Type: text/html; charset=utf-8", $result))
		{
			$this->fail("Should not still be application/xhtml+xml");
		}
	}
}