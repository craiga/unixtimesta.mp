<?php

class TemplateInjectionResponseFilter extends PatternReplacementResponseFilter
{
	private $_template;
	private $_pattern;
	
	public function __construct(Template $template, $pattern)
	{
		$this->_template = $template;
		$this->_pattern = $pattern;
	}
	
	protected function _getPattern($body, $requestIdentifier, $parameters)
	{
		return $this->_pattern;
	}
	
	protected function _getReplacement($body, $requestIdentifier, $parameters)
	{
		return $this->_template->toString();
	}
}