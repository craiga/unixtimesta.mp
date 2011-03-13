<?php

class NavigationTest extends UnitTestCase
{
	public function testStuff()
	{
		$nav = new Navigation();
		$nav[] = new NavigationItem("Test", "http://test.com");
		$nav[] = new NavigationItem("Example", "http://example.org");
		
		$result = "";
		foreach($nav as $key => $value)
		{
			$result .= sprintf("%s:%s;", $key, $value->getLabel());
		}
		
		$this->assertEqual("0:Test;1:Example;", $result);
	}
	
	public function testSetNonNumericIndex()
	{
		try
		{
			$nav = new Navigation();
			$nav["foobar"] = new NavigationItem();
			$this->fail("Should not allow index of foobar when setting an element");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	public function testGetNonNumericIndex()
	{
		try
		{
			$nav = new Navigation();
			$navItem = $nav["foobar"];
			$this->fail("Should not allow index of foobar when getting an element");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	public function testSetNotNavigationItem()
	{
		try
		{
			$nav = new Navigation();
			$nav[] = new Navigation();
			$this->fail("Should not setting of anything other than a NavigationItem");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	public function testGetNumberOfChildren()
	{
		$nav = new Navigation();
		$this->assertEqual(0, $nav->getNumberOfChildren());
		$nav[] = new NavigationItem("Test", "http://test.com");
		$this->assertEqual(1, $nav->getNumberOfChildren());
		$nav[] = new NavigationItem("Example", "http://example.org");
		$this->assertEqual(2, $nav->getNumberOfChildren());
	}
}