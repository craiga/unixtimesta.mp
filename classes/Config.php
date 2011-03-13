<?php

class Config
{
	private static $_Config = array();
	
	public static function Set($key, $value)
	{
		self::$_Config[$key] = $value;
	}
	
	public static function Get($key, $defaultValue = null)
	{
		if(!isset(self::$_Config[$key]) && is_null($defaultValue))
		{
			throw new ConfigNotSetException();
		}
		
		$value = $defaultValue;
		if(isset(self::$_Config[$key]))
		{
			$value = self::$_Config[$key];
		}
		return $value;
	}
}