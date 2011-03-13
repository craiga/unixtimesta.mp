<?php

class PatternReplacementResponseFilterTest extends UnitTestCase
{
	public function testFilterResponseBody()
	{
		$filter = new DummyPatternReplacementResponseFilter();
		$this->assertEqual("There's a evil boy.", $filter->filterResponseBody("There's a good boy.", "/", array("evil = true")));
		$this->assertEqual("There's a good boy.", $filter->filterResponseBody("There's a good boy.", "/", array("evil = false")));
	}
}

class DummyPatternReplacementResponseFilter extends PatternReplacementResponseFilter
{
	protected function _getPattern($body, $requestIdentifier, $parameters)
	{
		return "/good/";
	}
	
	protected function _getReplacement($body, $requestIdentifier, $parameters)
	{
		return "evil";
	}
	
	protected function _shouldFilter($body, $requestIdentifier, $parameters)
	{
		return in_array("evil = true", $parameters);
	}
}