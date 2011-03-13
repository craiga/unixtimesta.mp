<?php

class NavigationItemTest extends UnitTestCase
{
	public function testGetLabelAndUrl()
	{
		$navItem = new NavigationItem("Test", "http://test.com/");
		$this->assertEqual("Test", $navItem->getLabel());
		$this->assertEqual("http://test.com/", $navItem->getURL());
	}
	
	public function testInvalidLabel()
	{
		try
		{
			$navItem = new NavigationItem(2);
			$this->fail("Should not allow invalid label");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	public function testMissingLabel()
	{
		try
		{
			$navItem = new NavigationItem();
			$this->fail("Should not allow missing label");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	public function testInvalidURL()
	{
		try
		{
			$navItem = new NavigationItem("Test", 2);
			$this->fail("Should not allow invalid URL");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
	
	public function testMissingURL()
	{
		try
		{
			$navItem = new NavigationItem("Test");
			$this->fail("Should not allow missing URL");
		}
		catch(InvalidArgumentException $e)
		{
			$this->pass();
		}
	}
}