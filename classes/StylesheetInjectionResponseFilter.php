<?php

class StylesheetInjectionResponseFilter extends HeadInjectionResponseFilter
{
	private $_href;
	private $_media;
	
	public function __construct($href, $media = 'all')
	{
		$this->_href = $href;
		$this->_media = $media;
	}
	
	protected function _getInjection()
	{
		return sprintf("<link href=\"%s\" media=\"%s\" rel=\"stylesheet\" type=\"text/css\" />", $this->_href, $this->_media);
	}
}