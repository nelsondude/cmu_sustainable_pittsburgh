<?php
    defined("_JEXEC") or die('Restricted access');
    jimport('joomla.application.component.view');
    class GwcViewCompanies extends JViewLegacy {
		public function display($tpl = null) {
			
			$view = JRequest::getCmd('view');
			$model = $this->getModel();		
            $items = $this->get("Items");
            $pagenation = $this->get("Pagination");
            $this->items = $items;
			$this->actions	= $model->getCompanyActions();
			$this->info	= $model->getCompanyInfo();
            $this->pagination = $pagenation;
            parent::display($tpl);
        }
    }
?>  	