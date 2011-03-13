<?php

class NavigationInjectionResponseFilter extends FooterInjectionResponseFilter
{
	private $_nav;
	private $_id;
	
	public function __construct(Navigation $nav, $id = null)
	{
		$this->_nav = $nav;
		$this->_id = $id;
	}
	
	protected function _getInjection()
	{
		return $this->_buildNavigation($this->_nav, $this->_id);
	}
	
	protected function _buildNavigation($nav, $id = null)
	{
		$html = "";
		if($nav->getNumberOfChildren() > 0)
		{
			$html .= "\n<ul class=\"nav\"";
			if(!is_null($id))
			{
				$html .= " id=\"" . $id . "\"";
			}
			$html .= ">\n";

			foreach($nav as $navItem)
			{
				$html .= "\t<li><a href=\"" . $navItem->getURL() . "\">" . $navItem->getLabel() . "</a>";

				{
					$html .= $this->_buildNavigation($navItem);
				}
				$html .= "</li>\n";
			}

			$html .= "</ul>\n";
		}
		
		return $html;
	}
}