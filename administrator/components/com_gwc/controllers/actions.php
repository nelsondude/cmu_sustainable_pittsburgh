<?php

defined("_JEXEC") or die('Restricted access');

jimport('joomla.application.component.controller');

class GwcControllerActions extends JControllerForm {

	

    public function __construct($config = array()) {

        parent::__construct($config);

    }

	

    public function add() {

        $this->setRedirect(

            JRoute::_('index.php?option=com_gwc&view=actions&layout=edit', false)

        );

    }

 

    public function edit() {

		$id = JRequest::getVar('boxchecked');

        $this->setRedirect(

            JRoute::_('index.php?option=com_gwc&view=actions&layout=edit&id='.$id[0], false)

        );

    }

	

	public function save() {

		$desc = str_replace('"', '\"', JRequest::getVar('description', '', 'post', 'string', JREQUEST_ALLOWHTML));

		$model = $this->getModel('actions');

        $id = $model->save(

			array(

				'id' => JRequest::getVar('id'),

				'name' => JRequest::getVar('name'),

				'action_number' => JRequest::getVar('action_number'),

				'alias' => JRequest::getVar('alias'),

				'category' => JRequest::getInt('cat_id'),

				'points' => JRequest::getVar('points'),

				'description' => $desc

			)	

		);

		if(JRequest::getVar('task') === 'apply') {

		    $this->setRedirect(

				JRoute::_('index.php?option=com_gwc&view=actions&layout=edit&id='.$id, false)

			);

		} else {

			$this->setRedirect(

				JRoute::_('index.php?option=com_gwc&view=actions', false)

			);

		}

	}



	public function cancel() {

        $this->setRedirect(

            JRoute::_('index.php?option=com_gwc&view=actions', false)

        );

    }

    public function remove() {

        JRequest::checkToken() or jexit('Invalid Token');

        $model = $this->getModel('actions');

        $model->remove();

        $this->setRedirect(

            JRoute::_('index.php?option=com_gwc&view=actions', false)

        );

    }	

}

