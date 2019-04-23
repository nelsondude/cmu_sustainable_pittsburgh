<?php
    defined("_JEXEC") or die('Restricted access');
    jimport('joomla.application.component.view');
    class GwcViewSubmissions extends JViewLegacy {
		public function display($tpl = null) {
			$view = JRequest::getCmd('view');
			$model = $this->getModel();
			if(JRequest::getVar("layout") == "edit"){
				$this->item = $model->displayEditPage();
				$this->files = explode(", ",$this->item->upload_file);
				$categories = $this->get("Categories");
				$this->categories = array_map(function($a){return $a->name;},$categories);
				$companies = $this->get("Companies");
				$this->companies = array_map(function($a){return $a->name;},$companies);
				$actions = $this->get("Actions");
				$this->actions = array_map(function($a){return substr($a->name,0,45);},$actions);
				GwcController::editBar($view, $this->item->id == 0);
			} else {
				$this->items 			= $this->get("Items");
				$this->pagination		= $this->get("Pagination");
				$this->state 			= $this->get("State");
				$this->filterForm 		= $this->get("FilterForm");
				$this->activeFilters 	= $this->get("ActiveFilters");
				$this->addToolbar();
				GwcController::submenu('submissions');
			}
            parent::display($tpl);
        }
    
		protected function addToolbar() {
			JToolBarHelper::title('Submissions');
			JToolBarHelper::addNew();
			JToolBarHelper::editList();
			JToolBarHelper::divider();
			JToolBarHelper::deleteList();
			JToolBarHelper::divider();
			JToolbarHelper::publish('publish', 'Approve Actions', true);
			JToolbarHelper::unpublish('unpublish', 'Unapprove Actions', true);
			JToolbarHelper::custom('export', 'calendar', 'calendar', 'Export Submissions', false);
			JToolBarHelper::preferences('com_gwc');
		}

	}
?>  	