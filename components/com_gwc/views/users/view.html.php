<?php
    defined("_JEXEC") or die('Restricted access');
    jimport('joomla.application.component.view');
    class GwcViewUsers extends JViewLegacy {
		public function display($tpl = null) {
            $this->user = JRequest::getVar('user_id');
            parent::display($tpl);
        }
    }
?>  	