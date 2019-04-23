<?php
defined("_JEXEC") or die('Restricted access');
jimport('joomla.application.component.controller');
define('DS', DIRECTORY_SEPARATOR);
$view = JRequest::getWord('view', 'actions');
$input = JFactory::getApplication()->input;
$input->set('view', $input->getCmd('view', 'actions'));
require_once( JPATH_ROOT . DS . 'components' . DS . 'com_gwc' . DS . 'helpers' . DS . 'gwc.php' );
require_once( JPATH_COMPONENT . DS . 'controllers' . DS . $view . '.php' );
$classname = JControllerLegacy::getInstance('Gwc') . $view;
$controller = new $classname();

$controller->execute(JRequest::getWord('task'));
$controller->redirect();