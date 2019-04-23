<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_messages
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include the HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');


JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');
JHtml::_('bootstrap.tooltip', '.hasTooltip', array('placement' => 'bottom'));

JFactory::getDocument()->addScriptDeclaration(
	"
		Joomla.submitbutton = function(task)
		{
			if (task == 'config.cancel' || document.formvalidator.isValid(document.getElementById('config-form')))
			{
				Joomla.submitform(task, document.getElementById('config-form'));
			}
		};
	"
);
?>
<div class="container-popup">
	<form action="<?php echo JRoute::_('index.php?option=com_gwc&view=config'); ?>" method="post" name="adminForm" id="gwc-form" class="form-validate form-horizontal">
		<fieldset>
			<?php echo $this->form->renderField('category_id'); ?>
			<?php echo $this->form->renderField('delete_category'); ?>
		</fieldset>
		<button id="saveBtn" type="button" class="hidden" onclick="Joomla.submitform('reasign', this.form);"></button>

		<input type="hidden" name="task" value="" />
		<input type="hidden" name="from_id" id="from_id" value="<?php echo $_GET['from_id']; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
