<?php

class FooterTemplateInjectionResponseFilter extends FooterInjectionResponseFilter
{
	private $_template;
	
	public function __construct(Template $template)
	{
		$this->_template = $template;
	}
	
	protected function _getInjection()
	{
		return $this->_template->toString();
	}
}