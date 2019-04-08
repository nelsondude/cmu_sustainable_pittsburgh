<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.modelitem');
class GwcModelActions extends JModelItem {
	public function getItem(){
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$id = JRequest::getVar('id');
		$query->select('name, action_number, description, points');
		$query->from('#__gwc_actions');
		$query->where('id = ' . $id);
		$db->setQuery($query);
		return $db->loadObject();
	}
	public function getList(){
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$company = gwcHelper::getCompanyByUser($user->id);
		$query
			->select('a.id, a.name, a.action_number, a.points, c.type_ids')
			->select('
					CASE 
						WHEN CHAR_LENGTH(c.category_identifier) = 1 THEN ABS(SUBSTR(a.action_number,2))
						ELSE ABS(SUBSTR(a.action_number,3))
					END as test
				')			
			->select('
					CASE 
						WHEN c.parent IS NOT NULL THEN c2.name
						ELSE c.name
					END as category
				')
            ->select('
                    CASE
                        WHEN p.id IS NULL THEN 0
                        ELSE 1
                    END as is_planned
                ')
			->from('#__gwc_actions a')
            ->leftJoin('#__gwc_planned_actions p on p.action_id = a.id AND p.company_id = '.$company)
			->join('LEFT', '#__gwc_action_categories c ON a.category = c.id')
			->join('LEFT', '#__gwc_action_categories c2 ON c.parent = c2.id')
			->where('year = ' . gwcHelper::getCycle())
			->order('category')
			->order('test')
			->order('a.id');
		$db->setQuery($query);
		$results = $db->loadObjectList();
		if($_GET['debugActions']) die('<pre>'.count($results));
		return $db->loadObjectList();
	}
}
