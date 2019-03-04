<?php
/**
 * @package Module Smart Countdown 3 for Joomla! 3.0
 * @version 3.0
 * @author Alex Polonski
 * @copyright (C) 2012-2015 - Alex Polonski
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
// no direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__).'/plural_base.php';

function scdSetupTranslatedPlurals()
{
	return modSmartCountdown3Plurals::setupTranslatedLabels('fr-fr');
}