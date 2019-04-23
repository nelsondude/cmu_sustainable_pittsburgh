<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.controller');
JLoader::import('components.com_languages.helpers.jsonresponse', JPATH_ADMINISTRATOR);
class GwcControllerCompanies extends JControllerForm {
    public function __construct($config = array()) {
        parent::__construct($config);
    }
	
    public function add() {
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=companies&layout=edit', false)
        );
    }
 
    public function edit() {
		$id = JRequest::getVar('id');
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=companies&layout=edit&id='.$id[0], false)
        );
    }
    
	public function cancel() {
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=companies', false)
        );
    }
	
	public function publish(){
		JRequest::checkToken() or jexit('Invalid Token');
        $model = $this->getModel('companies');
        $model->publish(1);		
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=companies', false)
        );	
	}
	public function unpublish(){
		JRequest::checkToken() or jexit('Invalid Token');
        $model = $this->getModel('companies');
        $model->publish(0);		
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=companies', false)
        );	
	}	
	public function save() {
		$data['name'] 			= JRequest::getVar('name');
		$data['type'] 			= JRequest::getVar('type');	
		$data['size']			= JRequest::getVar('size');
		$data['points'] 		= JRequest::getVar('points');
		$data['legacy_points'] 	= JRequest::getVar('legacy_points');
		$data['active']		 	= JRequest::getVar('active');
		$model = $this->getModel('companies');
        $model->save($data);
		$id 	= JRequest::getVar('id');
		$task   = $this->input->get('task');
		
		if($task == 'apply' && $id){
			$this->setRedirect(
				JRoute::_('index.php?option=com_gwc&view=companies&layout=edit&id='.$id, false)
			);
		} else {
			$this->setRedirect(
				JRoute::_('index.php?option=com_gwc&view=companies', false)
			);
		}
    }
	
    public function remove() {
        JRequest::checkToken() or jexit('Invalid Token');
        $model = $this->getModel('companies');
        $model->remove();
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=companies', false)
        );
    }
    public function reset() {
        $model = $this->getModel('companies');
        $model->updatePoints();
		$this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=companies', false)
        );
    }		
	public function addUser() {		
		require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_gwc' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'gwc.php');
		gwcHelper::saveUserCompany(JRequest::getVar('userid'),JRequest::getVar('companyid'),JRequest::getInt('type'),JRequest::getInt('size'));
		JFactory::getApplication()->enqueueMessage("updated successfully", 'message');
		echo new JJsonResponse($result);
		
		
		
		$mailer = JFactory::getMailer();
		$config = JFactory::getConfig();
		$sender = array( 
			$config->get( 'mailfrom' ),
			$config->get( 'fromname' )
		);
		$mailer->setSender($sender);

		$user = JFactory::getUser(JRequest::getVar('userid'));
		$recipient = $user->email;
		$mailer->addRecipient($recipient);

		$body   = "<p>Hello, {$user->name},</p><p>Your Sustainable Pittsburgh Challenge account has been activated!  You may now login at <a href=\"www.spchallenge.org/login\">www.spchallenge.org/login</a> and begin planning your winning Challenge strategy.  If you have any questions along the way, you may reach us at <a href=\"mailto:Challenge@sustainablepittsburgh.org\">Challenge@sustainablepittsburgh.org</a> or 412-258-6650.  We are delighted to have your organization on board.</p> <p>Thank you,</p> <p>Sustainable Pittsburgh Challenge Team</p>";
		$mailer->setSubject('Sustainable Pittsburgh Challenge account activation');
		$mailer->setBody($body);
		$mailer->isHtml(true);
		
		$send = $mailer->Send();

		JFactory::getApplication()->close();
		
    }
	public function removeUser() {
		$model = $this->getModel('companies');
		$model->removeUser(JRequest::getVar('userid'),JRequest::getVar('companyid'));
		JFactory::getApplication()->enqueueMessage("updated successfully", 'message');
		echo new JJsonResponse($result);
		JFactory::getApplication()->close();
    }	
	
}
