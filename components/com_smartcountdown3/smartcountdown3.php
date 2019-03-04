<?php
/**
 * @package Smart Countdown 3 AJAX server for Joomla! 3.0
 * @version 3.2.6
 * @author Alex Polonski
 * @copyright (C) 2012-2015 - Alex Polonski
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
// no direct access
defined('_JEXEC') or die;

$controller = JControllerLegacy::getInstance('smartCountdown3');
$controller->execute('getevent' /*JFactory::getApplication()->input->get('task')*/);
// we have only 1 controller for AJAX, the line below will never be hit
//$controller->redirect();
