<?php

    defined("_JEXEC") or die('Restricted access');

    jimport('joomla.application.component.view');

    class GwcViewActions extends JViewLegacy {

		public function display($tpl = null) {

			$this->user = JFactory::getUser();

			$companyid = gwcHelper::getCompanyByUser($this->user->id);

			if(!intval($companyid)){

				JFactory::getApplication()->enqueueMessage("Please indicate which organization your are affiliated with.", 'error');

				JFactory::getApplication()->redirect(

					JRoute::_('index.php?option=com_users&view=profile#org', false)

				);

			}

			if(!JRequest::getVar('id')){

				$this->items = $this->get('List');
				
				$this->userinfo = gwcHelper::getCompanyNameByUser($this->user->id);

				$this->setLayout('list');

			} else {

				$this->item  = $this->get('Item');

			}

            parent::display($tpl);

        }

		

    }

?>