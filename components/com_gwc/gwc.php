<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.controller');
$user = JFactory::getUser();
$view = JRequest::getVar('view');
define('DS', DIRECTORY_SEPARATOR);
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_gwc' . DS . 'helpers' . DS . 'gwc.php' );
$controller = JControllerLegacy::getInstance('Gwc');
if($user->guest && $view != 'leaderboard'){
//if($user->guest){
	JFactory::getApplication()->redirect(
		JRoute::_('index.php?option=com_users&view=login')
	);		
} else {
	$controller->execute(JFactory::getApplication()->input->get('task'));
	$controller->redirect();
}