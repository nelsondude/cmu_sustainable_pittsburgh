<?php
defined('_JEXEC') or die("Restricted access");
jimport('joomla.application.component.controller');
class GwcController extends JControllerLegacy{
	public function display($cachable = false, $urlparams = false)	{
		$request = JRequest::get();
		$view = $request['view'] ? $request['view'] : 'actions';
		$id     = $this->input->getVar('id');
		parent::display();
	}
	
	public function submenu($submenu){	
		JSubMenuHelper::addEntry('Actions', 'index.php?option=com_gwc&view=actions', $submenu == 'actions');
		JSubMenuHelper::addEntry('Categories', 'index.php?option=com_gwc&view=categories', $submenu == 'categories');
		JSubMenuHelper::addEntry('Companies', 'index.php?option=com_gwc&view=companies', $submenu == 'companies');
		JSubMenuHelper::addEntry('Submissions', 'index.php?option=com_gwc&view=submissions', $submenu == 'submissions');
        JSubMenuHelper::addEntry('Planned', 'index.php?option=com_gwc&view=planned', $submenu == 'planned');
	}
	
	public function editBar($view, $isNew){
		JToolBarHelper::title($isNew ? JText::_('New')
									: JText::_('Edit'));
		JToolBarHelper::apply('apply');
		JToolBarHelper::save('save');
		JToolBarHelper::cancel('cancel', 'JTOOLBAR_CANCEL');				
	}	
}
