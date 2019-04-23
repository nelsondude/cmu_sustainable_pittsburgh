<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.view');
class GwcViewPlanned extends JViewLegacy {
    public function display($tpl = null) {
        $view = JRequest::getCmd('view');
        $model = $this->getModel();

        $this->items 			= $this->get("Items");
        $this->pagination		= $this->get("Pagination");
        $this->state 			= $this->get("State");
        $this->filterForm 		= $this->get("FilterForm");
        $this->activeFilters 	= $this->get("ActiveFilters");

        GwcController::submenu('planned');

        parent::display($tpl);
    }



}
?>
