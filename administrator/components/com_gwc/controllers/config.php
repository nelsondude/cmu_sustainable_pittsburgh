<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_gwc
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Messages Component Message Model
 *
 * @since  1.6
 */
class GwcControllerConfig extends JControllerLegacy
{
	/**
	 * Method to save a record.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function reasign()
	{
		$data  = $this->input->post->get('jform', array(), 'array');
		$data['from_id'] = JRequest::get( 'post' )['from_id'];

		$model = $this->getModel('config');
        $model->reasign($data);
        die(print_r($data));
        if($data['delete_category']){
        	$catModel = $this->getModel('categories');
        	$catModel->remove(array($data['from_id']));
        }

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
}
