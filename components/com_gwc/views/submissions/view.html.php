<?php
    defined("_JEXEC") or die('Restricted access');
    jimport('joomla.application.component.view');
    class GwcViewSubmissions extends JViewLegacy {
		public function display($tpl = null) {
            $model = $this->getModel();
            $items = $this->get("Items");
            $pagenation = $this->get("Pagination");
            $this->items = $items;
            $this->pagination = $pagenation;
			$layout = JRequest::getVar('layout')?JRequest::getVar('layout'):'default';

			if($layout == 'edit'){
                //$model = $this->getModel();
                $this->item = $model->edit();
            }

            if($layout == 'download'){
                //$model = $this->getModel();
                $model->download();
            }
			
            parent::display($tpl);
        }
    }
?>  	