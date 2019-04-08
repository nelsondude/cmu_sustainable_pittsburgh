<?php
die("TEST");
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class GwcControllerCompanies extends JControllerAdmin {
    public function __construct($config = array()) {
        parent::__construct($config);
    }
	
	public function updateComment(){
		$submission = JRequest::getVar("submission");
		$comments = JRequest::getVar("comments");
		$model = $this->getModel('companies');
		$model->updateComment($submission,$comments);
	}
		
	public function newDoc(){
		$submission = JRequest::getVar("submission");
		$model = $this->getModel('companies');
		$data = $model->newDoc($submission,$_FILES['upload_file']);
		$submission_model = $this->getModel('submissions');
		$submission_model->uploadDoc($data);
		$app = JFactory::getApplication();
		$app->enqueueMessage("File(s) uploaded");
		$app->redirect(JRoute::_('index.php?option=com_gwc&view=companies'),false);
	}
	public function removeDoc(){
		$submission = JRequest::getVar("submission");
		$file = JRequest::getVar("file");
		$model = $this->getModel('companies');
		$model->removeDoc($submission,$file);
	}
	public function updatePlannedAction(){
        $action_id = JRequest::getVar("action_id");
        $deadline = JRequest::getVar("deadline");
        $model = $this->getModel('companies');
        $model->updatePlannedAction($action_id, $deadline);
    }
    public function removePlannedAction(){
        $action_id = JRequest::getVar("action_id");
        $model = $this->getModel('companies');
        $model->removePlannedAction($action_id);
    }
}
