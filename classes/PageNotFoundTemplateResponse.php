<?php

class PageNotFoundTemplateResponse extends TemplateResponse
{
	protected function _getHTTPStatus()
	{
		return 404;
	}
}