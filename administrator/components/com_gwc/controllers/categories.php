<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.controlleradmin');
class GwcControllerCategories extends JControllerForm {
    public function __construct($config = array()) {
        parent::__construct($config);
    }
	
    function add() {
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=categories&layout=edit', false)
        );
    }
 
    function edit() {
		$id = JRequest::getVar('id');
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=categories&layout=edit&id='.$id[0], false)
        );
    }

	public function apply() {
		$id = JRequest::getVar('id');
		$model = $this->getModel('categories');
        $id = $model->save(
			array(
				'id' => JRequest::getVar('id'),
				'name' => JRequest::getVar('name'),
				'alias' => JRequest::getVar('alias'),
				'color' => JRequest::getVar('color')
			)	
		);
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=categories&layout=edit&id='.$id, false)
        );		
	}	
	public function publish(){
		JRequest::checkToken() or jexit('Invalid Token');
        $model = $this->getModel('categories');
        $model->publish(1);		
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=categories', false)
        );	
	}
	public function unpublish(){
		JRequest::checkToken() or jexit('Invalid Token');
        $model = $this->getModel('categories');
        $model->publish(0);		
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=categories', false)
        );	
	}	
	public function reasign(){
		die("INFO: " .JRequest::getVar('myInfo'));
		JRequest::checkToken() or jexit('Invalid Token');
        $model = $this->getModel('categories');
        $model->publish(0);		
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=categories', false)
        );	
	}
	public function save() {
		//die("<pre>".print_r(JRequest::getVar('type_ids'),1));
		$model = $this->getModel('categories');
        $model->save(
			array(
				'id' => JRequest::getVar('id'),
				'name' => JRequest::getVar('name'),
				'alias' => JRequest::getVar('alias'),
				'color' => JRequest::getVar('color'),
				'type_ids' =>  implode(",", JRequest::getVar('type_ids')),
				'legacy_percentage' => JRequest::getVar('legacy_percentage'),
				'active'	 		=> JRequest::getVar('active')
			)	
		);
		if(JRequest::getVar('task') === 'apply'){
			$id = JRequest::getVar('id');
			$this->setRedirect(
				JRoute::_('index.php?option=com_gwc&view=categories&layout=edit&id='.$id, false)
			);			
		} else {
			$this->setRedirect(
				JRoute::_('index.php?option=com_gwc&view=categories', false)
			);
		}
	}	
 	public function cancel() {
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=categories', false)
        );
    }
    function remove() {

        JRequest::checkToken() or jexit('Invalid Token');
        $model = $this->getModel('categories');
        $model->remove();
        $this->setRedirect(
            JRoute::_('index.php?option=com_gwc&view=categories', false)
        );
    }		
}
