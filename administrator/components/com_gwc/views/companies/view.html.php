<?php
    defined("_JEXEC") or die('Restricted access');
    jimport('joomla.application.component.view');
    class GwcViewCompanies extends JViewLegacy {
		public function display($tpl = null) {
			if(JRequest::getVar("layout") == "edit"){
				$document = JFactory::getDocument();	
				// Add styles
				$style = "input[id^='jform_active']{
								display:none;
							}
							label[for^='jform_active'] {
								width:50%;
								float:left;
								box-sizing:border-box;
								-moz-box-sizing:border-box;
								-webkit-box-sizing:border-box;			
							}
							label[for='jform_active0']{border-radius:4px 0 0 4px;}
							label[for='jform_active1']{border-radius:0 4px 4px 0;}"; 
				$document->addStyleDeclaration($style);

				$view 	= JRequest::getCmd('view');
				$id 	= JRequest::getVar('id');
				$model 	= $this->getModel();
				if($id){
					$this->item 		= $model->displayEditPage();
					$this->actions		= $model->getCompanyActions();
					$this->companyusers	= $model->getCompanyUsers();
				}
				$this->types	= $model->getTypes();
				$this->sizes	= $model->getSizes();
				GwcController::editBar($view, $this->item->id == 0);
			} elseif(JRequest::getVar("layout") == "userlist") {
				$view 			= JRequest::getCmd('view');
				$model 			= $this->getModel();
				$this->item 	= $model->displayEditPage();
				$this->users	= $model->getUsers();
			} else {		
				$this->items 			= $this->get("Items");
				$this->pagination		= $this->get("Pagination");
				$this->state 			= $this->get("State");
				$this->filterForm 		= $this->get("FilterForm");
				$this->activeFilters 	= $this->get("ActiveFilters");
				$this->filters			= JRequest::getVar('filter');
				GwcController::submenu('companies');
				$this->addToolbar();
			}			
            parent::display($tpl);
        }
		protected function addToolbar() {
			JToolBarHelper::title('Companies');
			JToolBarHelper::addNew();
			JToolBarHelper::editList();
			JToolBarHelper::divider();
			JToolBarHelper::deleteList();
			JToolBarHelper::divider();
			JToolBarHelper::custom('reset', 'lightning', 'pending', 'Reset', false);
			JToolBarHelper::custom('updateLegacy', 'database', 'database', 'Update Legacy Points', false);
			JToolBarHelper::custom('publish', 'publish', 'publish', 'Activate', false);
			JToolBarHelper::custom('unpublish', 'unpublish', 'unpublish', 'Deactivate', false);
			JToolBarHelper::preferences('com_gwc');
		}
    }
?>  	