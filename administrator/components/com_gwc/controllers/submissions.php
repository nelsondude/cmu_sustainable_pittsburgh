<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class GwcControllerSubmissions extends JControllerForm {
    public function __construct($config = array()) {
        parent::__construct($config);
    }
	 
    function edit() {
		$id = JRequest::getVar('id');
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=submissions&id='.$id[0], false)
        );
    }
 
	public function save() {
		
		$model = $this->getModel('submissions');
        $model->save(
			array(
				'id' => JRequest::getVar('id'),
				'points' => JRequest::getVar('points'),
				'approved' => JRequest::getVar('approved'),
				'legacy' => JRequest::getVar('legacy'),
				'company' => JRequest::getVar('company'),
				'admin_comments' => JRequest::getVar('admin_comments'),
				'action_id' => JRequest::getVar('action_id'),
				'original' => JRequest::getVar('original'),
				'orig_points' => JRequest::getVar('orig_points')
			)	
		);
		if(JRequest::getVar('task') === 'apply') {
			$id = JRequest::getVar('id');
			$this->setRedirect(
				JRoute::_('index.php?option=com_gwc&view=submissions&layout=edit&id='.$id, false)
			);		
		} else {
			$this->setRedirect(
				JRoute::_('index.php?option=com_gwc&view=submissions', false)
			);
		}
	}
	 
	function publish() {
		$status = str_replace('submissions.', '', JRequest::getVar('task'));
		$approved = (bool)($status == 'publish');
		$model = $this->getModel('submissions');
		$model->changeStatus(array(
			'id' => $id = JRequest::getVar('cid'),
			'approved' => $approved
			)
		);
        $this->setRedirect(
			$_SERVER['HTTP_REFERER']
        );		
	}

	function unpublish() {
		$status = str_replace('submissions.', '', JRequest::getVar('task'));
		$approved = (bool)($status == 'publish');
		$model = $this->getModel('submissions');
		$model->changeStatus(array(
			'id' => $id = JRequest::getVar('cid'),
			'approved' => $approved
			)
		);
        $this->setRedirect(
			$_SERVER['HTTP_REFERER']
        );		
	}
	
    function remove() {
        JRequest::checkToken() or jexit('Invalid Token');
        $model = $this->getModel('submissions');
        $model->remove();
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=submissions', false)
        );
    }	
 	public function cancel() {
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=submissions', false)
        );
    }	
	function download(){
		//die(JComponentHelper::getParams('com_gwc')->get('current_cycle'));
		$model = $this->getModel('submissions');
		$model->download(array(
				'action_id' => JRequest::getVar('action'),
				'company_id' => JRequest::getVar('company'),
				'file' => JRequest::getVar('file')
			)
		);
	}
	function export(){
		$model = $this->getModel('submissions');
		$model->export();
	}
}
