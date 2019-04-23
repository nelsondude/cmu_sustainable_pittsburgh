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
 * Message configuration model.
 *
 * @since  1.6
 */
class GwcModelConfig extends JModelForm
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the model
	 * configuration flag to ignore the request is set.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState()
	{
		$user = JFactory::getUser();

		$this->setState('user.id', $user->get('id'));

		// Load the parameters.
		$params = JComponentHelper::getParams('com_gwc');
		$this->setState('params', $params);
	}


	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm	 A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_gwc.config', 'config', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function reasign($data)
	{
		$db = JFactory::getDBO();
		$from_id = $data['from_id'];
		$to_id = $data['category_id'];
		$db->setQuery("UPDATE #__gwc_actions SET category='".$to_id."' WHERE category = '".$from_id."'");
		$db->execute();

		if($data['category_id']){
			$query = $db->getQuery(true);

			$query

				->delete("#__gwc_action_categories")

				->where("id = ".$from_id);

			$db->setQuery($query);

			$db->execute();
		}
		

		if(!$db->query()) JError::raiseError(500,$db->getErrorMsg());

	
	}
}
