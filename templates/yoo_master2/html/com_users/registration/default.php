<?php

/**

* @package   Warp Theme Framework

* @author    YOOtheme http://www.yootheme.com

* @copyright Copyright (C) YOOtheme GmbH

* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL

*/



// no direct access

defined('_JEXEC') or die;

/*

if(1||JRequest::getVar('test')) {

	require_once('newform.php');

	return;

}

*/

//JHtml::_('behavior.mootools');

JHtml::_('behavior.keepalive');

JHtml::_('behavior.tooltip');

JHtml::_('behavior.formvalidation');

//require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_gwc' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'gwc.php');

//require_once( JPATH_ROOT . DS . 'components' . DS . 'com_gwc' . DS . 'helpers' . DS . 'gwc.php' );

//gwcHelper::getCompanies();





//$company_options = gwcHelper::getTypesSizes();

?>



<div id="system">

	



	

	

	<!--<h1 class="title">Register</h1>-->

	



	<form class="register submission small style" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post">

		<?php

		$fieldsets = $this->form->getFieldsets();

		$fieldsets = array_reverse($fieldsets);

		foreach ($fieldsets as $fieldset): ?>

			<?php $fields = $this->form->getFieldset($fieldset->name); ?>

			<?php if (count($fields)): ?>

					<?php foreach ($fields as $field): ?>	

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

			<?php endif; ?>

		<?php endforeach; ?>

		<div>

			<button class="validate" type="submit"><?php echo JText::_('JREGISTER'); ?></button>

		</div>
		<?php echo JHtml::_('form.token');?>
	</form>



</div>

		<script>

jQuery(document).ready(function(){

	jQuery('#jform_gwc_firstname,#jform_gwc_lastname').keyup(function(){

		jQuery('#jform_name').val(jQuery('#jform_gwc_firstname').val()+" "+jQuery('#jform_gwc_lastname').val());

	});

});

		</script>