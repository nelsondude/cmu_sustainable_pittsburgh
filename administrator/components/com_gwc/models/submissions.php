<?php

defined("_JEXEC") or die('Restricted access');

jimport('joomla.application.component.modellist');

class GwcModelSubmissions extends JModelList {



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

	

	protected function populateState($ordering = 'a.id', $direction = 'desc')

	{

		$app = JFactory::getApplication();



		// Adjust the context to support modal layouts.

		if ($layout = $app->input->get('layout'))

		{

			$this->context .= '.' . $layout;

		}



		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');

		$this->setState('filter.search', $search);



		$action_number = $this->getUserStateFromRequest($this->context . '.filter.action_number', 'filter_action_number');

		$this->setState('filter.action_number', $action_number);



		$action = $app->getUserStateFromRequest($this->context . '.filter.action', 'filter_action');

		$this->setState('filter.action', $action);



		$company = $this->getUserStateFromRequest($this->context . '.filter.company', 'filter_company', '');

		$this->setState('filter.company', $company);



		$category = $this->getUserStateFromRequest($this->context . '.filter.category', 'filter_category');

		$this->setState('filter.category', $category);



		$approved = $this->getUserStateFromRequest($this->context . '.filter.approved', 'filter_approved');

		$this->setState('filter.approved', $approved);



		$final_points = $this->getUserStateFromRequest($this->context . '.filter.final_points', 'filter_final_points', '');

		$this->setState('filter.final_points', $final_points);



		$created = $this->getUserStateFromRequest($this->context . '.filter.created', 'filter_created', '');

		$this->setState('filter.created', $created);



		// List state information.

		parent::populateState($ordering, $direction);

	}

	

	public function getListQuery() {

		$app 	= JFactory::getApplication();

		$filter = JRequest::getVar('filter');

		$list = JRequest::getVar('list');

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query->select('s.id, a.action_number, a.name as action, co.name as company, ac.name as category, s.approved, s.final_points, s.legacy, a.year, s.created');

		$query->from('#__gwc_submittals s');

		$query->join('INNER', '#__gwc_companies co ON s.company_id = co.id');

		$query->join('INNER', '#__gwc_actions a ON s.action_id = a.id');

		$query->join('INNER', '#__gwc_action_categories ac ON a.category = ac.id');

		$cycle = $app->getUserStateFromRequest($layout . '.filter.cycle', 'filter_cycle', gwcHelper::getCycle());

		

		if(strlen($filter['category_id'])){

			$query->join('INNER', '#__categories cat ON cat.id = '.$filter['category_id']);

			$query->where('a.alias = cat.alias');

		}

		if(strlen($filter['approved'])){

			$query->where('s.approved = '.$filter['approved']);

		}

		if(strlen($filter['search'])){

			$search = $db->quote('%' . $db->escape($filter['search'], true) . '%');

			$query->where('(a.name LIKE ' . $search . ' OR ac.name LIKE ' . $search . ' OR co.name LIKE ' . $search . ' OR a.action_number LIKE ' . $search . ')' );

		}

		if($list['fullordering']){

			$query->order($list['fullordering']);

		} else {

			$query->order('s.id ASC');

		}	

		$query->where('a.year = '.$cycle);		

		parent::populateState('s.id', 'ASC');		

		return $query;		

	}

		

	public function getActions() {

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query->select('id, CONCAT(action_number, " - ", name) as name');

		$query->from('#__gwc_actions');

		$query->where('year=2018');

		$query->order('action_number ASC');

		$db->setQuery($query);

		return $db->loadObjectList('id');

	}

	

	public function getCategories() {

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query->select('id, name');

		$query->from('#__gwc_action_categories');

		$db->setQuery($query);

		return $db->loadObjectList('id');

	}

	public function getCompanies() {

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query->select('id, name');

		$query->from('#__gwc_companies c');

		$query->join("INNER","#__gwc_company_user ce ON c.id = ce.company_id");

		$query->order('id ASC');

		$db->setQuery($query);

		return $db->loadObjectList('id');		

	}

	public function displayEditPage() {

		$id = JRequest::getVar("id");

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		if($id){

			$query->select('s.id, s.action_id, a.name as action, co.name as company, ac.name as category, s.approved, s.final_points, s.comments, s.admin_comments, s.upload_file, co.id as company_id, s.legacy, s.created');

			$query->from('#__gwc_submittals s');

			$query->join('INNER', '#__gwc_companies co ON s.company_id = co.id');

			$query->join('INNER', '#__gwc_actions a ON s.action_id = a.id');

			$query->join('INNER', '#__gwc_action_categories ac ON a.category = ac.id');		

			$query->where('s.id = '.$id);

			$db->setQuery($query);

			return $db->loadObject();

		}

		else{

			$obj = new stdClass();

			$obj->created = date();

		}

	}

	

	public function updateCompanyPoints($submittal, $phi = 0){

		

		$db = JFactory::getDBO();

		

		$query = $db->getQuery(true);

		$query->select("company_id, final_points, approved");

		$query->from("#__gwc_submittals");

		$query->where("id = {$submittal}");

		$db->setQuery($query);

		$result = $db->loadObject();

		$company = $result->company_id;

		

		$query = $db->getQuery(true);

		/*$query

			->select("SUM(final_points)")

			->from("zpaq2_gwc_submittals")

			->where("company_id = {$company}")

			->where("year = 2015")

			->where("approved = 1")

			->group("company_id");*/

		$query

			->select("points")

			->from("#__gwc_companies")

			->where("id = {$company}");

			

		$db->setQuery($query);

		if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());

		$points = $db->loadResult();			

			

		$multiplier = $result->approved ? 1 : -1;

		$delta = (intval($result->final_points) + $phi) * intval($multiplier);

		$points = $points + $delta;

		$query = $db->getQuery(true);

		$query->update("#__gwc_companies");

		$query->set("points = {$points}");

		$query->where("id = {$company}");

		$db->setQuery($query);

		if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());

		

	}

	

	public function changeStatus($data){

		$ids = $data['id'];

		$status = intval($data['approved']);

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		foreach($ids as $id){

			$query = "UPDATE #__gwc_submittals SET approved = " . $status . " WHERE id = " . $id;

			$db->setQuery($query);

			if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());

			self::updateCompanyPoints($id);

		}

	}

	

	public function save($data){

		$db = JFactory::getDBO();

		if($data['id']){

			$query = "UPDATE #__gwc_submittals SET approved = " . $data['approved'] . ", final_points = " . $data['points'] . ", legacy = " . $data['legacy'] . ", admin_comments = '" . $data['admin_comments'] . "' WHERE id = " . $data['id'];

			$db->setQuery($query);

			if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());

			if($data['original'] != $data['approved'])

				self::updateCompanyPoints($data['id']);

			elseif($data['orig_points'] != $data['points'])

				self::updateCompanyPoints($data['id'], $data['points'] - $data['orig_points']);

		} else {

			$query = "INSERT INTO #__gwc_submittals (id,action_id,approved,final_points,legacy,company_id,year,admin_comments) VALUES(NULL," . $data['action_id'] . "," . $data['approved'] . "," . $data['points'] . "," . $data['legacy'] . ",".$data['company'].",2018,'".$data['admin_comments']."')";

			$db->setQuery($query);

			if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());

			$id = $db->insertid();

			self::updateCompanyPoints($id);

		}

		

	}



	public function remove(){

		$cids = JRequest::getVar('cid');

		$ids  = implode(',',$cids);

		$db = JFactory::getDBO();

		$query = "DELETE FROM #__gwc_submittals WHERE id IN ({$ids})";

		$db->setQuery($query);

		if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());

	}

	

	public function download($data){
		//die("Model download");
		$imgs = array('png','jpg','gif','jpeg');

		$fullpath = "/usr" . DS . "home" . DS . "sustainablepgh" . DS . "public_html" . DS . "spchallenge_submission" . DS . 

					JComponentHelper::getParams('com_gwc')->get('current_cycle') . DS . 

					$data['company_id'] . DS . 

					$data['action_id'] . DS . 

					urlencode($data['file']);

		$pathinfo = pathinfo($fullpath);

		$ext = pathinfo($data['file'], PATHINFO_EXTENSION);
		


		//die(mime_content_type($fullpath));

		// mostly from: http://www.php.net/manual/en/function.readfile.php

		header('Content-Description: File Transfer');

		if(in_array($ext,$imgs) ) {
		    header('Content-Type: '.mime_content_type($fullpath));
		}
		else {
			header('Content-Type: application/octet-stream');
        	header('Content-Disposition: attachment; filename="' . $data['file'] . '"');
		}

        header('Content-Transfer-Encoding: binary');

        header('Expires: 0');

        header('Cache-Control: must-revalidate');

        header('Pragma: public');

        header("Content-Length: " . filesize($fullpath));
        ob_end_clean();

        ob_clean();

        flush();

        readfile($fullpath);
        die();



	}

	

	public function export(){

		$cycle = JComponentHelper::getParams('com_gwc')->get('current_cycle');

		$db = JFactory::getDBO();

		$query = $db->getQuery(true);

		$query

			->select("c.name as company, CONCAT(a.action_number, ': ', a.name) as action, s.final_points, s.approved, s.created")

			->from("#__gwc_submittals s")

			->join("INNER", "#__gwc_companies c ON s.company_id = c.id")

			->join("INNER", "#__gwc_actions a ON s.action_id = a.id")

			->where("s.year = {$cycle}")

			->order("c.name");

		$db->setQuery($query);

		$rows = $db->loadObjectList();

		$output = "Company, Action, Points, Approved, Date" . PHP_EOL;

		foreach($rows as $i=>$row){

			$arr = (array)$row;

			

			$arr = array_map(function($a){

				return ((strpos($a,",") === FALSE) ? $a : '"'.$a.'"');

			},$arr);

			

			$output .= implode(',',$arr);

			$output .= PHP_EOL;

		}





		$filename = "gwc_submissions_" . date("m-d-y") . ".csv";



		$now = gmdate("D, d M Y H:i:s");

		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");

		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");

		header("Last-Modified: {$now} GMT");



		// force download  

		header("Content-Type: application/force-download");

		header("Content-Type: application/octet-stream");

		header("Content-Type: application/download");



		// disposition / encoding on response body

		header("Content-Disposition: attachment;filename={$filename}");

		header("Content-Transfer-Encoding: binary");

		

		echo $output;

		die();		

	}

}