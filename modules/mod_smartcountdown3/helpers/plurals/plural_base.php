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

/**
 * Construct and return array of translated labels
 * Add language-specific js script for plural forms management
 */
abstract class modSmartCountdown3Plurals
{
	public static function setupTranslatedLabels($lang_tag = '', $num_plural_forms = 2)
	{
		$translated = array();
		foreach(array(
				'years',
				'months',
				'weeks',
				'days',
				'hours',
				'minutes',
				'seconds'
			) as $asset)
		{
			// add generic form (_MORE)
			$translated[$asset] = JText::_('MOD_SMARTCOUNTDOWN3_N_' . strtoupper($asset));
			// additional plural forms
			for($i = 1; $i < $num_plural_forms; $i++)
			{
				$translated[$asset . '_' . $i] = JText::_('MOD_SMARTCOUNTDOWN3_N_' . strtoupper($asset) . '_' .$i);
			}
		}
		
		// add script
		$lang_suffix = $lang_tag ? '_' . $lang_tag : '';
		JFactory::getDocument()->addScript(JURI::root(true).'/modules/mod_smartcountdown3/helpers/plurals/plural' . $lang_suffix . '.js');
		
		return $translated;
	}
}

