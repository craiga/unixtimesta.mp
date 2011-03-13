<?php

class FilesystemResponseTest extends UnitTestCase
{
	public function testGetResponse()
	{
		$filename = tempnam(sys_get_temp_dir(), "FilesystemResponseTest");
		file_put_contents($filename, "foobar");
		$response = new FilesystemResponse($filename);
		$this->assertEqual($filename, $response->getFileName());
		$this->assertEqual("foobar", $response->getResponseBody());
	}
}