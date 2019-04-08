<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.modellist');

class GwcModelCompanies extends JModelList {
	public function getListQuery() {
		$id = JRequest::getVar('id');
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select('s.id, a.action_number, s.action_id, a.name as action, co.name as company, ac.name as category, s.approved, s.final_points, s.upload_file, s.comments');
		$query->from('#__gwc_submittals s');
		$query->join('INNER', '#__gwc_companies co ON s.company_id = co.id');
		$query->join('INNER', '#__gwc_actions a ON s.action_id = a.id');
		$query->join('INNER', '#__gwc_action_categories ac ON a.category = ac.id');
		$query->where('s.company_id = ' . $id);
		return $query;
	}
	
	public function getCompanyActions(){	
		$user = JFactory::getUser();
		$id = gwcHelper::getUserByCompany($user->id);
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		if($id){
			$query->select('s.id, action_id, a.name as action_name, s.final_points, s.approved, s.upload_file, s.comments, s.admin_comments, action_number');
			$query->from('#__gwc_submittals s');
			$query->join('LEFT', '#__gwc_company_user u ON u.user_id = s.user_id');		
			$query->join('INNER', '#__gwc_actions a ON a.id = s.action_id');
			$query->where('s.company_id = '. $id);
			$query->where('s.year = '. gwcHelper::getCycle());
			$db->setQuery($query);
			$actions = $db->loadObjectList();
			$return['approved'] = array_filter($actions,function($a){return $a->approved == 1;});
			$return['pending'] = array_filter($actions,function($a){return $a->approved==0;});
			$return['disapproved'] = array_filter($actions,function($a){return $a->approved == -1;});
			return $return;
		}
	}

	public function getPlannedActions() {
        $user = JFactory::getUser();
        $id = gwcHelper::getUserByCompany($user->id);
        $db = JFactory::getDBO();
        if(!$id) return null;

        $query = $db->getQuery(true);
        $query->select('p.id, p.action_id, a.name as action_name, p.deadline, ac.name as category');
        $query->from('#__gwc_planned_actions p');
        $query->join('INNER', '#__gwc_actions a ON a.id = p.action_id');
        $query->join('INNER', '#__gwc_action_categories ac ON a.category = ac.id');
        $query->where('p.company_id = '.$id);
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return $results;
    }

    public function getNumberOfPlannedActions() {
	    return count($this->getPlannedActions());
    }

    public function updatePlannedAction($action_id, $deadline) {
        $user = JFactory::getUser();
        $id = gwcHelper::getUserByCompany($user->id);
        $db = JFactory::getDBO();
        if(!$id) return null;

        $query = $db->getQuery(true);
        $fields = array(
            $db->quoteName('deadline') . ' = ' . $db->quote($deadline),
        );

        $conditions = array(
            $db->quoteName('id') . ' = '.$action_id,
            $db->quoteName('company_id') . ' = ' . $id
        );

        $query
            ->update($db->quoteName('#__gwc_planned_actions'))
            ->set($fields)
            ->where($conditions);

        $db->setQuery($query);
        $result = $db->execute();
    }

    public function removePlannedAction($action_id) {
        $user = JFactory::getUser();
        $id = gwcHelper::getUserByCompany($user->id);
        $db = JFactory::getDBO();
        if(!$id) return null;

        $query = $db->getQuery(true);
        $conditions = array(
            $db->quoteName('id') . ' = '.$action_id,
            $db->quoteName('company_id') . ' = ' . $id
        );
        $query->delete($db->quoteName('#__gwc_planned_actions'));
        $query->where($conditions);

        $db->setQuery($query);
        $result = $db->execute();
    }

    public function addPlannedAction($action_id) {
        $user = JFactory::getUser();
        $id = gwcHelper::getUserByCompany($user->id);
        $db = JFactory::getDBO();
        if(!$id) return null;

        // Insert columns.
        $columns = array('action_id', 'company_id', 'user_id');

        // Insert values.
        $values = array($action_id, $id, $user->id);

        $query = $db->getQuery(true);
        $query
            ->insert($db->quoteName('#__gwc_planned_actions'))
            ->columns($db->quoteName($columns))
            ->values(implode(',', $values));

        // Set the query using our newly populated query object and execute it.
        $db->setQuery($query);
        $db->execute();
    }

	public function getCompanyInfo(){
		$user = JFactory::getUser();
		$id = gwcHelper::getUserByCompany($user->id);
		$cycle = gwcHelper::getCycle();
		$db = JFactory::getDBO();
		if(!$id) return null;
		$query = $db->getQuery(true);
		$query->select("id,name,type,size,legacy_points,(SELECT SUM(final_points) FROM #__gwc_submittals s WHERE s.company_id = c.id AND year = {$cycle} AND approved = 1) AS points");
		$query->from("#__gwc_companies c");
		$query->where("c.id = $id");
		$db->setQuery($query);
		
		return $db->loadObject();
	}
	
	public function newDoc($submission,$file){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query
			->select("action_id,upload_file,company_id")
			->from("#__gwc_submittals")
			->where("id = {$submission}");
		$db->setQuery($query);
		$obj = $db->loadObject();
		foreach($file['name'] as $i=>$f){
			$file['name'][$i] = preg_replace("/[^a-zA-Z0-9\._]/", "", $f);
		}
		$fields = array($db->quoteName('upload_file') . ' = ' . $db->quote(strlen($obj->upload_file) ? $obj->upload_file . ', ' . implode(', ',$file['name']) : implode(', ',$file['name'])));
		$conditions = array($db->quoteName('id') . ' = ' . $submission);
		$query = $db->getQuery(true);
		$query
			->update($db->quoteName('#__gwc_submittals'))
			->set($fields)
			->where($conditions);
		$db->setQuery($query);
		$result = $db->execute();
		
		return array(
			'company_id' => $obj->company_id,
			'action_id'  => $obj->action_id
		);
	}
	
	public function removeDoc($submission,$file){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query
			->select("action_id,upload_file,company_id")
			->from("#__gwc_submittals")
			->where("id = {$submission}");
		$db->setQuery($query);
		$obj = $db->loadObject();
		$path = JPATH_SITE . DS . 'media' . DS . 'com_gwc' . DS . gwcHelper::getCycle() . DS . $obj->company_id . DS . $obj->action_id . DS . $file;
		if(is_file($path)) unlink($path);
		
		$files = explode(", ", $obj->upload_file);
		$files = array_diff($files, [$file]);
		
		$fields = array($db->quoteName('upload_file') . ' = ' . $db->quote(implode(", ", $files)));
		$conditions = array($db->quoteName('id') . ' = ' . $submission);
		
		$query = $db->getQuery(true);
		$query
			->update($db->quoteName('#__gwc_submittals'))
			->set($fields)
			->where($conditions);
		$db->setQuery($query);
		$result = $db->execute();
	}
	
	public function updateComment($submission,$comments){
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query
			->update($db->quoteName('#__gwc_submittals'))
			->set("comments = " . $db->quote($comments))
			->where("id = {$submission}");
		$db->setQuery($query);
		$result = $db->execute();		
	}
}
