<?php
/**
 * @package Module Smart Countdown 3 for Joomla! 3.0
 * @version 3.0
 * @author Alex Polonski
 * @copyright (C) 2012-2015 - Alex Polonski
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
defined('JPATH_BASE') or die;


class JFormFieldPreview extends JFormField
{
	public $type = 'Preview';
	
	protected function getInput()
	{
		$id = (int) $this->form->getValue('id', 0);
		if($id) {
			ob_start();
?>
			<a onclick="window.open('<?php echo JRoute::_('index.php?option=com_smartcountdown3&view=render&tmpl=component&id=' . $id, true); ?>', 'newwindow', 'width=600, height=400, left=50, top=50, scrollbars=yes'); return false;" class="btn" href="<?php echo JRoute::_('index.php?option=com_smartcountdown3&view=render&tmpl=component&id=' . $id, true); ?>"><?php echo JText::_('MOD_SMARTCOUNTDOWN3_PREVIEW_OPEN'); ?></a>
<?php
			return ob_get_clean();
		} else {
			return JText::_('MOD_SMARTCOUNTDOWN3_PREVIEW_SAVE_FIRST');
		}
	}
}
