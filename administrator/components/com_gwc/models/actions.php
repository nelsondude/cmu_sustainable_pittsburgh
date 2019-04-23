<?php

defined("_JEXEC") or die('Restricted access');

jimport('joomla.application.component.modellist');

class GwcModelActions extends JModelList {

	

	public function __construct($config = array()) {

		$app = JFactory::getApplication();

		$option = JRequest::getVar('option');

		if (empty($config['filter_fields'])) {

			$config['filter_fields'] = array(

					'a.id',

					'ac.name',

					'a.name',

					'a.points'			

			);

		}

		

		// Get pagination request variables

        $limit = $app->getUserStateFromRequest('limit', 'limit', $app->getCfg('list_limit'), 'int');

        $limitstart = $app->getUserStateFromRequest('start', 'start', $app->getCfg('list_start'), 'int');

        //$limitstart = JRequest::getVar('limitstart');

        // In case limit has been changed, adjust it

        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        $app->setUserState('limit', $limit);

        $app->setUserState('start', $limitstart);

		parent::__construct($config);

	}

	

/*	protected function populateState($ordering = null, $direction = null) {

		$app = JFactory::getApplication();

		$search = $this->getUserStateFromRequest($layout . '.filter.search', 'filter_search');

		$this->setState('filter.search', $search);

		$categoryId = $this->getUserStateFromRequest($layout . '.filter.category_id', 'filter_category_id');

		$this->setState('filter.category_id', $categoryId);	

		$cycle = $this->getUserStateFromRequest($layout . '.filter.cycle', 'filter_cycle');

		$this->setState('filter.cycle', $cycle);

		

		// Get pagination request variables

        $limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');

        $limitstart = JRequest::getVar('limitstart');		

        $this->setState('limit', $limit);

		$this->setState('limitstart', $limitstart);		

		parent::populateState();

		//parent::populateState('a.id', 'desc');

	}

*/	

	public function getListQuery() {

		$app 	= JFactory::getApplication();

		$filter = JRequest::getVar('filter');

		$list 	= JRequest::getVar('list');

		

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query->select('a.id, a.action_number, a.name as name, ac.name as category, a.points,a.year');

		$query->select('

					CASE 

						WHEN CHAR_LENGTH(ac.category_identifier) = 1 THEN ABS(SUBSTR(a.action_number,2))

						ELSE ABS(SUBSTR(a.action_number,3))

					END as test

				');

		$query->from('#__gwc_actions a');

		$query->join('INNER', '#__gwc_action_categories ac ON a.category = ac.id');

		

		if($filter['category_id']){

			$query->join('INNER', '#__categories cat ON cat.id = '.$filter['category_id']);

			$query->where('ac.alias = cat.alias');

		}

		

		$cycle = $app->getUserStateFromRequest('filter.cycle', 'filter_cycle', gwcHelper::getCycle());

		$this->setState('filter.cycle', $cycle);

		$query->where('a.year = '.$cycle);
		
		echo $cycle;

			

		if($filter['search']){

			$search = $db->quote('%' . $db->escape($filter['search'], true) . '%');

			$query->where('(a.name LIKE ' . $search . ' OR a.alias LIKE ' . $search . ' OR a.action_number LIKE ' . $search .')');

		}

		if(strpos($list['fullordering'], 'a.action_number') != -1){

			$parts = explode(' ',$list['fullordering']);

			$query->order('ac.category_identifier '.$parts[1]);

			$query->order('test '.$parts[1]);

		}elseif($list['fullordering']){

			$query->order($list['fullordering']);

		}

		$query->order('ac.id');

		$query->order('a.id');

		parent::populateState();

		return $query;

	}

	

	public function getState($property = null, $default = null) {

		return parent::getState($property, $default);

	}	

	

	public function displayEditPage() {

		$action = new stdClass ();

		$categories = $this->getCategories();

		foreach($categories as $i=>$cat){

			$action->categories[$i] = $cat->name;

		}

		$id = JRequest::getVar("id");

		if($id){

			$db = JFactory::getDBO();

			$query = $db->getQuery(true);

			$query->select('a.id, a.action_number, a.name as name, a.alias, a.description, c.id as cat_id, a.points');

			$query->from('#__gwc_actions a');

			$query->join('INNER', '#__gwc_action_categories c ON a.category = c.id');

			$query->where('a.id = "'.$id.'"');

			$db->setQuery($query);

			$action = $db->loadObject();

			return $action;

		} else {

			$action->id = '';

			$action->name = '';

			$action->alias = '';

			$action->description = '';

			$action->cat_id = '';

			$action->points = '';

			return $action;

		}

	}	

	

	function getCategories(){

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query->select('id, name');

		$query->from('#__gwc_action_categories');

		$db->setQuery($query);

		return $db->loadObjectList('id');

	}

	

	public function save($data) {

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$id = $data['id'];

		if($id){

			$fields = array(

				$db->quoteName("name") . '="' . $data['name'] . '"',

				$db->quoteName("alias") . '="' . $data['alias'] . '"',

				$db->quoteName("action_number") . '="' . $data['action_number'] . '"',

				$db->quoteName("description") . '="' . $data['description'] . '"',

				$db->quoteName("category") . '=' . $data['category'],

				$db->quoteName("points") . '="' . $data['points'] . '"',

			);

			$conditions = array(

				$db->quoteName("id") . '=' . $data['id']

			);

			$query->update($db->quoteName("#__gwc_actions"))->set($fields)->where($conditions);

			$db->setQuery($query);

		} else {

			unset($data['id']);

			$data['year'] = gwcHelper::getCycle();

			$values = array($db->quote($data['name']), $db->quote($data['action_number']), $db->quote($data['alias']), $db->quote($data['category']), $db->quote($data['points']), $db->quote($data['description']), $data['year']);

            $query

				->insert($db->quoteName("#__gwc_actions"))

                ->columns($db->quoteName(array_keys($data)))

                ->values(implode(',', $values));

			

			$db->setQuery($query);

			$id = $db->insertid();

		}



		

		if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());

		return $id;

	}

	

	public function remove(){

		$cid = JRequest::getVar('cid');

		

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query

			->delete("#__gwc_actions")

			->where("id IN (".implode(',',$cid).")");

		$db->setQuery($query);

		$db->execute();

	}

}