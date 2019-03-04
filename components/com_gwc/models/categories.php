<?php

defined("_JEXEC") or die('Restricted access');

jimport('joomla.application.component.modellist');

class GwcModelCategories extends JModelList {

	public function getListQuery() {

		$filter = JRequest::getVar('filter');
		
		$list = JRequest::getVar('list');

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query->select('cat.id as cat_id, cat.name as category, GROUP_CONCAT(CONCAT("\'",a.id,"\'")) as action_ids');

		$query->from('#__gwc_action_categories cat');

		$query->join('INNER', '#__gwc_actions a ON a.category = cat.id');

		$query->group('cat.id');
		
		$db->setQuery($query);

		return $query;		

	}

	



	

	public function getActions($ids){

		

		$db = JFactory::getDBO();

		$db->setQuery("SELECT id, name, action_number from #__gwc_actions WHERE id IN (" . $ids . ")");

		return $db->loadObjectList();

	}

}