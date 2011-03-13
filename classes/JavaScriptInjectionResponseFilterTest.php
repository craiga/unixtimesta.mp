<?php

class JavaScriptInjectionResponseFilterTest extends UnitTestCase
{
	public function testFilterResponseBody()
	{
		$filter = new JavaScriptInjectionResponseFilter("/this/js");
		$this->assertPattern("/<p>Hello, world!<\/p>\s*<script src=\"\/this\/js\" type=\"text\/javascript\"><\/script>\s*<\/body>/",
				$filter->filterResponseBody(
						"<html><head><title>yeah!</title></head><body><p>Hello, world!</p></body></html>"));
	}
	
	public function testInjectBeforeInlineScript()
	{
		$filter = new JavaScriptInjectionResponseFilter("/this/js");
		
		$this->assertPattern("/<body>\s*<script src=\"\/this\/js\" type=\"text\/javascript\"><\/script>\s*<script type=\"text\/javascript\">alert\(yeah\);<\/script>/m",
				$filter->filterResponseBody(
					"<html><head><title>yeah!</title></head><body><script type=\"text/javascript\">alert(yeah);</script><p>Hello, world!</p></body></html>"));
	}
	
	public function testInjectAfterExternalScript()
	{
		$filter = new JavaScriptInjectionResponseFilter("/this/js");
		$this->assertPattern("/<p>Hello, world!<\/p>\s*<script src=\"whatever\"><\/script>\s*<script src=\"\/this\/js\" type=\"text\/javascript\"><\/script>\s*<\/body>/",
				$filter->filterResponseBody(
					"<html><head><title>yeah!</title></head><body><p>Hello, world!</p><script src=\"whatever\"></script></body></html>"));
	}
	
	public function testInjectBeforeFirstInlineScript()
	{
		$filter = new JavaScriptInjectionResponseFilter("/this/js");
		
		$this->assertPattern("/<body>\s*<script src=\"\/this\/js\" type=\"text\/javascript\"><\/script>\s*<script>alert\(yeah\);<\/script><script>alert\(whatever\);<\/script>/m",
				$filter->filterResponseBody(
					"<html><head><title>yeah!</title></head><body><script>alert(yeah);</script><script>alert(whatever);</script><p>Hello, world!</p></body></html>"));
	}
	
	public function testInjectBeforeInlineScriptInOrder()
	{
		$filter = new AggregateResponseFilter(array(
			new JavaScriptInjectionResponseFilter("/one"),
			new JavaScriptInjectionResponseFilter("/two")
		));
		
		$this->assertPattern("/<body>\s*<script src=\"\/one\" type=\"text\/javascript\"><\/script>\s*<script src=\"\/two\" type=\"text\/javascript\"><\/script>\s*<script>alert\(yeah\);<\/script>/m",
				$filter->filterResponseBody(
					"<html><head><title>yeah!</title></head><body><script>alert(yeah);</script><p>Hello, world!</p></body></html>"));
	}
}