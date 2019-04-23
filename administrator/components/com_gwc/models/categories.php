<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.modellist');
class GwcModelCategories extends JModelList {

	public function __construct($config = array()) {
		$app = JFactory::getApplication();
		$option = JRequest::getVar('option');
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
		
			);
		}
		
		// Get pagination request variables
        $limit = $app->getUserStateFromRequest('limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', 0, '', 'int');
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
	    $app->setUserState('limit', $limit);
		parent::__construct($config);
	}

	public function getListQuery() {
		$app 	= JFactory::getApplication();
		$filter = JRequest::getVar('filter');
		$list = JRequest::getVar('list');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id, name, years, color, active');
		$query->from('#__gwc_action_categories');
		if($filter['search']){
			$search = $db->quote('%' . $db->escape($filter['search'], true) . '%');
			$query->where('(name LIKE ' . $search . ')');
		}
		if($filter['cycle']){
			$cycle = $app->getUserStateFromRequest('filter.cycle', 'filter_cycle', gwcHelper::getCycle());
			$this->setState('filter.cycle', $cycle);
			$query->where($cycle . ' IN (years)');
		}	
		if($list['fullordering']){
			$query->order($list['fullordering']);
		} else {
			$query->order('id ASC');
		}
		parent::populateState('id', 'asc');
		return $query;	
	}

	public function getState($property = null, $default = null) {
		return parent::getState($property, $default);
	}

	public function getTypes() {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,name');
		$query->from('#__gwc_company_types');
		$db->setQuery($query);
		$data = $db->loadObjectList();
		$types = array_reduce(
			$data,
			function(&$result, $item){
				$result[$item->id] = $item->name;
				return $result;
			}
		);
		return $types;
	}
	
	public function displayEditPage() {
		$id = JRequest::getVar("id");
		if($id){
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('id, name, alias, color, legacy_percentage, type_ids, active');
			$query->from('#__gwc_action_categories');
			$query->where('id = '.$id);
			$db->setQuery($query);
			return $db->loadObject();
		}
	}	

	public function save($data){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		if($data['id']){
			$fields = array(
				$db->quoteName("name") . '="' . $data['name'] . '"',
				$db->quoteName("alias") . '="' . $data['alias'] . '"',
				$db->quoteName("color") . '="' . $data['color'] . '"',
				$db->quoteName("type_ids") . '="' . $data['type_ids'] . '"',
				$db->quoteName("legacy_percentage") . '="' . $data['legacy_percentage'] . '"',
				$db->quoteName("active") . '="' . $data['active'] . '"',
			);
			$conditions = array(
				$db->quoteName("id") . '=' . $data['id']
			);
			$query->update($db->quoteName("#__gwc_action_categories"))->set($fields)->where($conditions);
			$db->setQuery($query);
			if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());
			$query = "INSERT INTO #__categories (extension, title, alias, parent_id, published) VALUES('com_gwc', '".$data['name']."', '".$data['alias']."', 1, 1)
				ON DUPLICATE KEY UPDATE title = '".$data['name']."'";
			$db->setQuery($query);
			if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());
			return $data['id'];
		} else {
			$values = array($db->quote($data['id']), $db->quote($data['name']), $db->quote($data['alias']), $db->quote($data['color']), $db->quote($data['type_ids']), $db->quote($data['legacy_percentage']), $db->quote($data['active']));
            $query
				->insert($db->quoteName("#__gwc_action_categories"))
                ->columns($db->quoteName(array_keys($data)))
                ->values(implode(',', $values));
				
			$db->setQuery($query);
			if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());
			return $db->insertid();
		}
		
	}
	public function publish($val){
		$cid = JRequest::getVar('cid');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query
			->update("#__gwc_action_categories")
			->set("active = {$val}")
			->where("id IN (".implode(',',$cid).")");
		$db->setQuery($query);
		$db->execute();
	}

	public function remove(){

		$cid = JRequest::getVar('cid');

		//die("<pre>".print_r($cid,1));

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query

			->delete("#__gwc_action_categories")

			->where("id IN (".implode(',',$cid).")");

		$db->setQuery($query);

		$db->execute();

		//remove actions in that category
		$query2 = $db->getQuery(true);

		$query2

			->delete("#__gwc_actions")

			->where("category IN (".implode(',',$cid).")");

		$db->setQuery($query2);

		$db->execute();
	}
}