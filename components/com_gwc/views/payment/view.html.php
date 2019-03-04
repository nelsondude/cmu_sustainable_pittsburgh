<?php
    defined("_JEXEC") or die('Restricted access');
    jimport('joomla.application.component.view');
    class GwcViewPayment extends JViewLegacy {
			$this->setLayout('payment');
			parent::display($tpl);
        }
    }
?>