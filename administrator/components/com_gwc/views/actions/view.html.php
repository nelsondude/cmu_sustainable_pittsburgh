<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.view');
class GwcViewActions extends JViewLegacy {
	public function display($tpl = null) {
		$mainframe 	= JFactory::getApplication();
		$option 	= JRequest::getCmd('option');
		$view 		= JRequest::getCmd('view');
		$user 		= JFactory::getUser();			
		$model 		= $this->getModel();
		if(JRequest::getVar("layout") == "edit"){
			$this->item = $model->displayEditPage();
			$categories = $model->getCategories();
			$this->categories = array_map(function($a){return $a->name;},$categories);
			GwcController::editBar($view, isset($this->item->id));
		} else {
			$this->items 			= $this->get("Items");
			$this->pagination 		= $this->get("Pagination");
			$this->state 			= $this->get("State");
			$this->filterForm 		= $this->get("FilterForm");
			$this->activeFilters 	= $this->get("ActiveFilters");
			$this->addToolbar();
			GwcController::submenu('actions');
		}
		
		parent::display($tpl);
	}
	protected function addToolbar() {
		JToolBarHelper::title('Actions');
		JToolBarHelper::addNew();
		JToolbarHelper::editList();
		JToolBarHelper::divider();
		JToolBarHelper::deleteList();
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_gwc');
	}
	
	protected function editBar($isNew){
		JToolBarHelper::title($isNew ? JText::_('New')
									: JText::_('Edit'));
		JToolBarHelper::apply('apply');
		JToolBarHelper::save('save');
		JToolBarHelper::cancel('cancel', 'JTOOLBAR_CANCEL');
	}
	
	protected function getSortFields()
	{
		return array(
			'a.id'           => JText::_('JGRID_HEADING_ID'),
			'a.state'        => JText::_('JSTATUS'),
			'a.name'        => JText::_('JGLOBAL_TITLE'),
			'category' => JText::_('JCATEGORY'),
		);
	}		
}
?>