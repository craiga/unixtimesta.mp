<?php

class FilesystemResponse extends Response
{
	private $_fileName;
	
	public function __construct($fileName)
	{
		// if(is_null($cacheTimestamp))
		// {
		// 	Venus::dump($_SERVER);
		// 	exit(0);
		// }
		// if(!is_numeric($cacheTimestamp))
		// {
		// 	throw new InvalidArgumentException("Cache timestamp, when supplied, must be a number.");
		// }
		
		$this->_fileName = $fileName;
	}
	
	protected function _getUnfilteredResponseBody()
	{
		return file_get_contents($this->_fileName);
	}
	
	protected function _getContentType()
	{
		$contentType = "text/plain";
		if(preg_match("/\.(\w+)$/", $this->_fileName, $matches) == 1)
		{
			$extension = $matches[1];
			switch(strtolower($extension))
			{
				case "js":
					$contentType = "text/javascript";
					break;
				case "css":
					$contentType = "text/css";
					break;
				case "htm":
				case "html":
					$contentType = "text/html";
					break;
				case "jpg":
				case "jpeg":
				case "jpe":
					$contentType = "image/jpeg";
					break;
				case "png":
					$contentType = "image/png";
					break;
				case "gif":
					$contentType = "image/gif";
					break;
				case "pdf":
					$contentType = "application/pdf";
					break;
				case "doc":
				case "dot":
					$contentType = "application/msword";
					break;
				case "mp3":
					$contentType = "audio/mpeg3";
					break;
				case "swf":
					$contentType = "application/x-shockwave-flash";
					break;
				case "zip":
					$contentType = "application/zip";
					break;
			}
		}
		return $contentType;
	}
	
	protected function _getTextEncoding()
	{
		return "utf-8";
	}
	
	protected function _getHTTPStatus()
	{
		return 200;
	}
	
	protected function _getHeaders()
	{
		$file = new SplFileInfo($this->_fileName);
		return array(
			sprintf("Last-Modified: %s GMT", gmdate("D, d M Y H:i:s", $file->getMTime()))
		);
	}
	
	public function getFileName()
	{
		return $this->_fileName;
	}
	
	
}