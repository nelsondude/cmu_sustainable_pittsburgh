<?php
defined('_JEXEC') or die("Restricted access");
jimport('joomla.application.component.controller');
class GwcController extends JControllerLegacy{
	public function display($cachable = false, $urlparams = false)
	{
		$view   = $this->input->get('view', 'actions');
		$id     = $this->input->getInt('id');
		parent::display();
	}
}
