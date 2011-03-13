<?php

class NoClassFileException extends Exception
{
	public function __construct($className)
	{
		parent::__construct(sprintf("No file could be found for the class %s.", $className));
	}
}