<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class GwcControllerSubmissions extends JControllerAdmin {
    public function __construct($config = array()) {
        parent::__construct($config);
    }
	
	public function update() {
		$model = $this->getModel('submissions');
        $model->update(
			array(
				'id' => JRequest::getVar('id'),
				'comments' => '"' . JRequest::getVar('comments') . '"',
				'upload_file' => '"'.implode(', ',$file_name) . '"'
			)	
		);
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=actions', false)
        );		
	}
	
	public function save() {
		$model = $this->getModel('submissions');
		$companyid = gwcHelper::getCompanyByUser(JRequest::getVar('user_id'));
		$app = JComponentHelper::getParams('com_gwc');
		foreach($_FILES['upload_file']['name'] as $i=>$filename){
			$file_name[$i] = preg_replace("/[^a-zA-Z0-9\._]/", "", $filename);
		}
		//die('<pre>'.print_r($file_name,1));
		//$file_name = preg_replace("/[^a-zA-Z0-9\.]/", "", $_FILES['upload_file']['name']);
        $model->save(
			array(
				'action_id' => JRequest::getVar('id'),
				'user_id' => (JRequest::getVar('user_id') ? JRequest::getVar('user_id') : 415),
				'company_id' => $companyid,
				'comments' => JRequest::getVar('comments'),
				'upload_file' => '"'.implode(', ',$file_name) . '"',
				'final_points' => intval(JRequest::getVar('points'))
			)	
		);
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=actions', false)
        );
	}
	public function download(){
		//die("contr dl");
		//die(JComponentHelper::getParams('com_gwc')->get('current_cycle'));
		
			$model = $this->getModel('submissions');
			$model->download(array(
					'action_id' => JRequest::getVar('action'),
					'company_id' => JRequest::getVar('company'),
					'file' => JRequest::getVar('file'),
					'user_id' => JRequest::getVar('user_id')
				)
			);
		
	}	
}
