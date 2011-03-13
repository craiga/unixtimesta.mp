<?php

Mock::generate("Template", "MockTemplate");

class NavigationInjectionResponseFilterTest extends UnitTestCase
{
	public function testFilterResponseBody()
	{
		// create navigation
		$nav = new Navigation();
		$nav[] = new NavigationItem("one", "//one");
		$subnav = new NavigationItem("two", "//two");
		$subnav[] = new NavigationItem("two-a", "//two/a");
		$subnav[] = new NavigationItem("two-b", "//two/b");
		$nav[] = $subnav;
		$nav[] = new NavigationItem("three", "//three");
		
		$original = "<html><body><p>Hello, world!</p></body></html>";
		
		// run test
		$filter = new NavigationInjectionResponseFilter($nav);
		$this->assertPattern("/<p>Hello, world!<\/p>\s*<ul class=\"nav\">\s*<li><a href=\"\/\/one\">one<\/a><\/li>\s*<li><a href=\"\/\/two\">two<\/a>\s*<ul class=\"nav\">\s*<li><a href=\"\/\/two\/a\">two-a<\/a><\/li>\s*<li><a href=\"\/\/two\/b\">two-b<\/a><\/li>\s*<\/ul>\s*<\/li>\s*<li><a href=\"\/\/three\">three<\/a><\/li>\s*<\/ul>\s*<\/body>/", $filter->filterResponseBody($original));
	}
	
	public function testFilterResponseBodyWithId()
	{
		// create navigation
		$nav = new Navigation();
		$nav[] = new NavigationItem("one", "//one");
		$subnav = new NavigationItem("two", "//two");
		$subnav[] = new NavigationItem("two-a", "//two/a");
		$subnav[] = new NavigationItem("two-b", "//two/b");
		$nav[] = $subnav;
		$nav[] = new NavigationItem("three", "//three");
		
		$original = "<html><body><p>Hello, world!</p></body></html>";
		
		// run test
		$filter = new NavigationInjectionResponseFilter($nav, "theNav");
		$this->assertPattern("/<p>Hello, world!<\/p>\s*<ul class=\"nav\" id=\"theNav\">\s*<li><a href=\"\/\/one\">one<\/a><\/li>\s*<li><a href=\"\/\/two\">two<\/a>\s*<ul class=\"nav\">\s*<li><a href=\"\/\/two\/a\">two-a<\/a><\/li>\s*<li><a href=\"\/\/two\/b\">two-b<\/a><\/li>\s*<\/ul>\s*<\/li>\s*<li><a href=\"\/\/three\">three<\/a><\/li>\s*<\/ul>\s*<\/body>/", $filter->filterResponseBody($original));
	}
	
	public function testFilterResponseBodyEmptyNav()
	{
		// create navigation
		$nav = new Navigation();
		
		$original = "<html><body><p>Hello, world!</p></body></html>";
		
		// run test
		$filter = new NavigationInjectionResponseFilter($nav, "theNav");
		$this->assertPattern("/<p>Hello, world!<\/p>\s*<\/body>/", $filter->filterResponseBody($original));
	}
}