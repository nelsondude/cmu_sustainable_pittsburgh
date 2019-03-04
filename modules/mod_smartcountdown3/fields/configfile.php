<?php
/**
 * @package Module Smart Countdown 3 for Joomla! 3.0
 * @version 3.0
 * @author Alex Polonski
 * @copyright (C) 2012-2015 - Alex Polonski
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
defined('JPATH_BASE') or die;

jimport('joomla.filesystem.folder');
JFormHelper::loadFieldClass('list');

class JFormFieldConfigFile extends JFormFieldList
{
	public $type = 'ConfigFile';
	
	protected function getOptions()
	{		
		$options = array();
		
		$context = (string)$this->element['context'];
		$dir = $context == 'layout' ? '/layouts' : '/fx';
		$configs_dir = JPATH_SITE.'/modules/mod_smartcountdown3' . $dir;
		
		$items = JFolder::files($configs_dir, '\.xml$', true, false);
		
		// Build the field options.
		if (!empty($items))
		{
			foreach ($items as $item)
			{	
				$options[] = JHtml::_('select.option', $item, ucfirst(str_replace('_', ' ', substr($item, 0, -4))));
			}
		}

		// Merge any additional options in the XML definition.
		$options = array_merge(parent::getOptions(), $options);

		return $options;
	}
}
