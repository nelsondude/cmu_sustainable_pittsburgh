<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.modellist');
class GwcModelSubmissions extends JModelList {
	public function getListQuery() {
		
	}
	
	public function update($data){
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$fields = array(
			$db->quoteName('comments') . ' = ' . $db->quote($data['comments']),
			$db->quoteName('upload_file') . ' = ' . $db->quote($data['upload_file'])
		);
		$conditions = array(
			$db->quoteName('id') . ' = ' . $db->quote($data['id'])
		);
		$query->update($db->quoteName('#__gwc_submittals'))->set($fields)->where($conditions);
		$db->setQuery($query);
		$result = $db->execute();
	}
	public function save($data){
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$data['comments'] = $db->quote($data['comments']);
		if(strlen($data['comments']) == 0) $data['comments'] = NULL;
		$data['year'] = gwcHelper::getCycle();
		//$query = "INSERT INTO #__gwc_submittals (" . implode(',',array_keys($data)) . ") VALUES (" . implode(',',array_values($data)) .")";
		$query
			->insert($db->quoteName('#__gwc_submittals'))
			->columns($db->quoteName(array_keys($data)))
			->values(implode(',', $data));
			
		$db->setQuery($query);
		if(!$db->query())JError::raiseError(500,$db->getErrorMsg());
		self::uploadDoc($data);
	}
	
	public function edit(){
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$id = JRequest::getVar('id');
		$query
			->select('action_id,user_id,company_id,comments,upload_file,a.name,a.action_number,s.year,s.final_points')
			->from('#__gwc_submittals s')
			->join("INNER", "#__gwc_actions a ON a.id = s.action_id")
			->where("s.id = {$id}");
		$db->setQuery($query);
		return $db->loadObject();
	}
	
	function uploadDoc($data){
		/*
		$db = $this->getDbo();
		$db->setQuery("SELECT company_id FROM #__gwc_company_user WHERE user_id = " . $data['user_id']);
		$company_id = $db->loadResult();
		*/
		$company_id = $data['company_id'];
		if(!isset($company_id))
			$company_id = 0;
		$year = JComponentHelper::getParams('com_gwc')->get('current_cycle');
		$path = array("/usr/home/sustainablepgh/public_html", "spchallenge_submission", $year, $company_id, $data['action_id']);

		$fullpath = '';
		foreach($path as $dir){
			$fullpath .= $dir . DIRECTORY_SEPARATOR;
			if(!is_dir($fullpath)){
				mkdir($fullpath);
			}
		}
		foreach($_FILES["upload_file"]["tmp_name"] as $i=>$tmpfile){
			move_uploaded_file($tmpfile,
				$fullpath . DIRECTORY_SEPARATOR . preg_replace("/[^a-zA-Z0-9\._]/", "", $_FILES["upload_file"]["name"][$i])
			);
		}
	}
	public function download($data){
		$user = JFactory::getUser();
		$companyid = gwcHelper::getCompanyByUser($user->id);
		if($companyid != $data['company_id']) {
			header('HTTP/1.0 403 Forbidden');
			die();
		}
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
}