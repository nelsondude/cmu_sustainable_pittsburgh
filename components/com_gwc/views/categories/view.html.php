<?php

    defined("_JEXEC") or die('Restricted access');

    jimport('joomla.application.component.view');

    class GwcViewCategories extends JViewLegacy {

		public function display($tpl = null) {

			$model = $this->getModel();

            $this->items = $this->get("Items");

			if(JRequest::getVar('cid')){

				$this->filtered_items = array_filter($this->items, function($a){return in_array($a->cat_id, JRequest::getVar('cid'));});

			} else {

				$this->filtered_items = $this->items;

			}

			foreach($this->filtered_items as $i=>$item){

				$item->actions = $model->getActions($item->action_ids);

            }
			$this->user = JFactory::getUser();
			$this->userinfo = gwcHelper::getCompanyNameByUser($this->user->id);

			parent::display($tpl);

        }

    }

?>  	