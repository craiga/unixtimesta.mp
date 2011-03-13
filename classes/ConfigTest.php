<?php

class ConfigTest extends UnitTestCase
{
	public function testGet()
	{
		$key = uniqid(get_class());
		// not set
		try
		{
			Config::Get($key);
			$this->fail("Allowed retrieval of an unset config key");
		}
		catch(ConfigNotSetException $e)
		{
			$this->pass();
		}
		
		// not set with default value
		$this->assertEqual("foobar", Config::Get($key, "foobar"));
		// set boolean
		Config::Set($key, true);
		$this->assertEqual(true, Config::Get($key, "foobar"));
		// set integer
		Config::Set($key, 44);
		$this->assertEqual(44, Config::Get($key, "foobar"));
		// set string
		Config::Set($key, "Hello, world!");
		$this->assertEqual("Hello, world!", Config::Get($key, "foobar"));
		// set array
		Config::Set($key, array("one", "two", "three"));
		$this->assertEqual(array("one", "two", "three"), Config::Get($key, "foobar"));
		// unset
		Config::Set($key, null);
		try
		{
			Config::Get($key);
			$this->fail("Allowed retrieval of an unset config key");
		}
		catch(ConfigNotSetException $e)
		{
			$this->pass();
		}
		// unset with default value
		$this->assertEqual("foobar", Config::Get($key, "foobar"));
	}
}