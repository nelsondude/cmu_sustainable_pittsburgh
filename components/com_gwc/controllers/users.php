<?php
defined("_JEXEC") or die('Restricted access');

class GwcControllerUsers extends JControllerForm {
    public function __construct($config = array()) {
        parent::__construct($config);
    }
	
	public function activateuser() {
		$app = JFactory::getApplication();
		$model = $this->getModel('users');
		$uid = JRequest::getVar('uid');
		$model->activateuser($uid);
		$app->enqueueMessage('Your account has been reactivated', 'notice');
		$app->redirect(JRoute::_('index.php?option=com_users&view=profile'));
	}
}