<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;
//JHtml::_('behavior.mootools');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_gwc' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'gwc.php');
//require_once( JPATH_ROOT . DS . 'components' . DS . 'com_gwc' . DS . 'helpers' . DS . 'gwc.php' );
gwcHelper::getCompanies();


$company_options = gwcHelper::getTypesSizes();
?>

<div id="system">
	

	
	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1 class="title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>

	<form class="submission small style" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post">
		<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
			<?php $fields = $this->form->getFieldset($fieldset->name); ?>
			
			<?php if (count($fields)): ?>
			
				<fieldset>
					<?php foreach ($fields as $field): ?>	
						<?php if(strpos($field->label,'captcha') > 0) : ?>
						<?php
							echo '<div><label>Organization Type</label>'. JHTML::_('select.genericlist', $company_options['types'], "type", 'class="inputbox"', 'value', 'text', 1) . '</div>';
							echo '<div><label>Organization Size</label><div>'. JHTML::_('select.genericlist', $company_options['sizes'], "size", 'class="inputbox"', 'value', 'text', 1) . '</div>';
						?>
						<?php endif; ?>
						<?php if ($field->hidden): ?>
							<?php echo $field->input; ?>
						<?php else: ?>
							<div><?php echo $field->label; ?>
								<?php echo ($field->type!='Spacer') ? $field->input : "&#160;"; ?>
								<?php if (!$field->required && $field->type != 'Spacer'): ?>
									<span class="optional"><?php echo JText::_('COM_USERS_OPTIONAL'); ?></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>					
				</fieldset>
			<?php endif; ?>
		<?php endforeach; ?>

		<div>
			<button class="validate" type="submit"><?php echo JText::_('JREGISTER'); ?></button>
		</div>
		
		
		<input type="hidden" id="jform_company_name" name="jform_company_id" value="" />
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="registration.register" />
		<?php echo JHtml::_('form.token');?>
		
	</form>

</div>