<?php
    defined("_JEXEC") or die('Restricted access');
    jimport('joomla.application.component.view');
    class GwcViewCategories extends JViewLegacy {
		public function display($tpl = null) {
			$mainframe = JFactory::getApplication();
			$option = JRequest::getCmd('option');
			$view = JRequest::getCmd('view');
			$user = JFactory::getUser();		
			$model = $this->getModel();
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

				$this->item = $model->displayEditPage();
				$this->types	= $model->getTypes();
				GwcController::editBar($view, $this->item->id == 0);
				$this->addToolbarEdit();

			} else {
				$this->items 			= $this->get("Items");
				$this->pagination 		= $this->get("Pagination");
				$this->filterForm 		= $this->get("FilterForm");
				$this->state 			= $this->get("State");
				$this->activeFilters 	= $this->get("ActiveFilters");	

				$this->types	= $model->getTypes();
		

				GwcController::submenu('categories');
				$this->addToolbar();
			}
            parent::display($tpl);
        }
		protected function addToolbar() {
			JToolBarHelper::title('Categories');
			JToolBarHelper::addNew();
			JToolBarHelper::editList();
			JToolBarHelper::divider();
			JToolBarHelper::deleteList("Warning! Deleting a category from the list page will also delete all of its actions!  You can re-assign actions to a new category from the 'Edit Category' page.");
			JToolBarHelper::divider();
			// JToolBarHelper::custom('publish', 'publish', 'publish', 'Activate', false);
			// JToolBarHelper::custom('unpublish', 'unpublish', 'unpublish', 'Deactivate', false);
			JToolBarHelper::preferences('com_gwc');
		}	
		protected function addToolBarEdit() {
			$bar = JToolBar::getInstance('toolbar');
			$bar->appendButton(
				'Popup',
				'cog',
				'Re-Assign Actions',
				'index.php?option=com_gwc&amp;view=config&amp;tmpl=component&amp;from_id='.$this->item->id,
				500,
				250,
				0,
				0,
				'',
				'',
				'<button class="btn" type="button" data-dismiss="modal" aria-hidden="true">'
				. JText::_('JCANCEL')
				. '</button>'
				. '<button class="btn btn-success" type="button" data-dismiss="modal" aria-hidden="true"'
				. ' onclick="jQuery(\'#modal-cog iframe\').contents().find(\'#saveBtn\').click();">'
				. JText::_('JSAVE')
				. '</button>'
			);


		}
    }
	

?>  	