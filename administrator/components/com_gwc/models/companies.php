<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.modellist');
class GwcModelCompanies extends JModelList {

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
		$cycle = gwcHelper::getCycle();
		$filter = JRequest::getVar('filter');
		$list = JRequest::getVar('list');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('c.id, c.name, ct.name as type, cs.name as size,  c.legacy_points, GROUP_CONCAT(u.email SEPARATOR "<br/> ") as users, c.active, (SELECT SUM(s.final_points) FROM #__gwc_submittals s WHERE s.company_id = c.id AND year = '.$cycle.' AND approved = 1) as points');
		$query->from('#__gwc_companies c');
		$query->join('INNER', '#__gwc_company_types ct ON c.type = ct.id');
		$query->join('INNER', '#__gwc_company_sizes cs ON c.size = cs.id');
		$query->join('LEFT OUTER', '#__gwc_company_user cu ON  c.id = cu.company_id');
		$query->join('LEFT OUTER', '#__users u ON  cu.user_id = u.id');
		if($filter){
			foreach($filter as $key=>$val){
				
				if($key == 'search' && $val){
					$search = $db->quote('%' . $db->escape($filter['search'], true) . '%');
					$query->where('(c.name LIKE ' . $search . ')');
				} elseif($val) {
					$query->where($key . ' = "' . $val . '"');
				}
			}
		}
		$query->group('c.id');
		if($list['fullordering']){
			$query->order($list['fullordering']);
		} else {
			$query->order('c.id ASC');
		}
		parent::populateState('c.id', 'ASC');
		//die($query);
		return $query;
	}
	public function remove() {
		$db = JFactory::getDBO();
		$request = JRequest::get();
		$query = "DELETE FROM #__gwc_companies WHERE id IN (".implode(',',$request['cid']).")";
		$db->setQuery($query);
		if(!$db->query()) JError(500,$db->getErrorMsg());
		$query = "DELETE FROM #__gwc_company_user WHERE company_id IN (".implode(',',$request['cid']).")";
		$db->setQuery($query);
		if(!$db->query()) JError(500,$db->getErrorMsg());
		
	}
	public function removeUser($uid,$cid) {
		$db = JFactory::getDBO();
		$query = "DELETE FROM #__gwc_company_user WHERE user_id = $uid AND company_id = $cid";
		$db->setQuery($query);
		if(!$db->query()) JError(500,$db->getErrorMsg());
	}
	
	public function getCompanyUsers() {
		$id = JRequest::getVar("id");
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,name,email');
		$query->from('#__users u');
		$query->join('INNER', '#__gwc_company_user cu ON cu.user_id = u.id');
		$query->where('company_id = '.$id);
		$db->setQuery($query);
		return $db->loadObjectList();
	
	}
	
	public function getUsers() {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('u.id,u.name,email,cu.company_id,c.name as co');
		$query->from('#__users u');
		$query->join('LEFT', '#__gwc_company_user cu ON cu.user_id = u.id');
		$query->join('LEFT', '#__gwc_companies c ON cu.company_id = c.id');
		//$query->where('(SELECT COUNT(*) FROM #__gwc_company_user cu WHERE cu.user_id = u.id) = 0');
		//$query->where('(company_id IS NULL OR c.name IS NULL)');
		$query->order('name ASC');
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	public function displayEditPage() {
		$id = JRequest::getVar("id");
		if(!$id) return null;
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id, name, type, size, points, legacy_points, active');
		$query->from('#__gwc_companies');
		$query->where('id = '.$id);
		$db->setQuery($query);
		return $db->loadObject();	
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
	
	public function getSizes() {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id,name');
		$query->from('#__gwc_company_sizes');
		$db->setQuery($query);
		$data = $db->loadObjectList();
		$sizes = array_reduce(
			$data,
			function(&$result, $item){
				$result[$item->id] = $item->name;
				return $result;
			}
		);
		return $sizes;
	}	
	
	public function getCompanyActions(){
		$cycle = gwcHelper::getCycle();
		$id = JRequest::getVar("id");
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('s.id, action_id, a.name as action_name, s.final_points, s.approved');
		$query->from('#__gwc_submittals s');
		$query->join('INNER', '#__gwc_companies c ON c.id = s.company_id');
		$query->join('INNER', '#__gwc_actions a ON a.id = s.action_id');
		$query->where("s.year = {$cycle}");
		$query->where('s.company_id = '. $id);
		$db->setQuery($query);
		$actions = $db->loadObjectList();
		$return['pending'] = array_filter($actions,function($a){return !($a->approved);});
		$return['approved'] = array_filter($actions,function($a){return $a->approved;});
		return $return;
	}
	
	public function pointsByYear($cos,$y){
		$db = JFactory::getDBO();
		foreach($cos as $i=>$co){
			$query = "SELECT SUM(s.final_points) AS total FROM #__gwc_submittals s
					LEFT OUTER JOIN #__gwc_company_user u ON u.user_id = s.user_id
					LEFT OUTER JOIN #__gwc_companies c ON u.company_id = c.id
					WHERE c.id = ".$co->id . " AND s.year = " . $y . " AND s.approved = 1";
			$db->setQuery($query);
			if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());
			$points[$co->id] = $db->loadResult();
		}
		return $points;
	}
	
	public function updatePoints(){
		$db = JFactory::getDBO();
		$q = $this->getListQuery();
		$db->setQuery($q);
		$allcompanies = $db->loadObjectList();
		
		$points = self::pointsByYear($allcompanies,gwcHelper::getCycle());
		foreach($points as $key=>$val){
			$query = "UPDATE #__gwc_companies SET points = " . intval($val) . " WHERE id = " . $key;
			$db->setQuery($query);
			if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());
		}
		$past_cycles = gwcHelper::getArchives();
		$past = array();
		foreach($past_cycles as $cycle){
			$past_points = self::pointsByYear($allcompanies,$cycle);
			foreach($past_points as $key=>$val){
				$query = "SELECT past_cycles FROM #__gwc_companies WHERE id = " . $key;
				$db->setQuery($query);
				$co_object = json_decode($db->loadResult(),true);
				
				if(!in_array($cycle,array_keys($co_object))){
					$past[$key][$cycle] = $val;
				}
			}
		}
		
		foreach($past as $co=>$p){
			$query = 'UPDATE #__gwc_companies SET past_cycles = "{';
			foreach($p as $pv=>$param){
				$query .= '\"'.$pv.'\" : \"'.intval($param).'\",';
			}
			
			$query = rtrim($query,",") . '}" WHERE id='.$co;
			$db->setQuery($query);
			if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());
		}
	}

	public function save($data){
		$db = JFactory::getDBO();
		$id = JRequest::getVar("id");
		if($id){
			$db->setQuery("UPDATE #__gwc_companies SET name='".$data['name']."', type='".$data['type']."', size='".$data['size']."', points = {$data['points']}, legacy_points = {$data['legacy_points']}, active = {$data['active']} WHERE id = {$id}");
		} else {
			$keys = array_keys($data);
			$db->setQuery("INSERT INTO #__gwc_companies (".implode(',',$keys).") VALUES('".$data['name']."','".$data['type']."','".$data['size']."','".$data['points']."','".$data['legacy_points']."','".$data['active']."')");
		}
		if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());
	}
	
	public function publish($val){
		$cid = JRequest::getVar('cid');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query
			->update("#__gwc_companies")
			->set("active = {$val}")
			->where("id IN (".implode(',',$cid).")");
		$db->setQuery($query);
		$db->execute();
	}
}