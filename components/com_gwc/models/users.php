<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.modellist');
class GwcModelUsers extends JModelList {
	public function activateuser($uid){
		$db = $this->getDbo();
		$db->setQuery("UPDATE #__users SET block = 0 WHERE id = $uid");
		if(!$db->query())JError::raiseError(500,$db->getErrorMsg());
	}
}