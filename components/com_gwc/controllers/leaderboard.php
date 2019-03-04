<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
JLoader::import('components.com_languages.helpers.jsonresponse', JPATH_ADMINISTRATOR);
class GwcControllerLeaderboard extends JControllerAdmin {
    public function __construct($config = array()) {
        parent::__construct($config);
    }
	
	public function loadAjax(){
		$size = JRequest::getInt('size');
		$type = JRequest::getInt('type');
		$model = $this->getModel('leaderboard');
		$result = $model->getBoard($size, $type);
		
		echo new JJsonResponse($result);
		JFactory::getApplication()->close();
	}
}	