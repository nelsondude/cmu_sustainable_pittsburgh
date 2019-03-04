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

//require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_gwc' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'gwc.php');

//require_once( JPATH_ROOT . DS . 'components' . DS . 'com_gwc' . DS . 'helpers' . DS . 'gwc.php' );

//gwcHelper::getCompanies();

//$company_options = gwcHelper::getTypesSizes();



JFactory::getDocument()->addStyleSheet('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css');

JFactory::getDocument()->addStyleDeclaration('

.register .radio input {

	margin-left: -20px !important;

	position:static;

}

form.register > div label {

	width: auto !important;

	margin-left: 0 !important;

}

.orgtype {

	margin-bottom: 0.5em;

}

');

?>



<div id="system">

	<form class="register submission small style" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post">

		<?php

		$fieldsets = $this->form->getFieldsets();

		$fieldsets = array_reverse($fieldsets);

		

		$fieldsById = array();

		foreach ($fieldsets as $fieldset) {

			$fields = $this->form->getFieldset($fieldset->name);

			if(count($fields)) {

				foreach ($fields as $field) {

					$fieldsById[$field->getAttribute('name')] = $field;

				}

			}

		}

		?>

		<div class="row">

			<div class="col-md-12">

				<?php echo $fieldsById['organization']->label; ?>

				<?php echo $fieldsById['organization']->input; ?>

			</div>

		</div>

		<div class="row">

			<div class="col-md-12">

				<label>Please select organization type*</label>

			</div>

			<div class="col-md-12">

				<div class="row">

				<div class="clearfix"></div>

				<?php

					$input = $fieldsById['orgtype']->input;

					$input = str_replace('<input ','<div class="orgtype col-sm-6 col-md-4 col-lg-3"><input ',$input);

					$input = str_replace('</label>','</label></div>',$input);

					echo $input;

				?>

				</div>

			</div>

		</div>

		<div class="row">

			<div class="col-md-12">

				<label>Contact Name*</label>

			</div>

			<div class="col-md-6">

				<?php

				$input = $fieldsById['firstname']->input;

				$input = str_replace('<input','<input placeholder="'.$fieldsById['firstname']->getAttribute('label').'"',$input);

				echo $input;

				?>

			</div>

			<div class="col-md-6">

				<?php

				$input = $fieldsById['lastname']->input;

				$input = str_replace('<input','<input placeholder="'.$fieldsById['lastname']->getAttribute('label').'"',$input);

				echo $input;

				?>

			</div>

		</div>

		<div class="row">

			<div class="col-md-6">

				<?php echo $fieldsById['phonenumber']->label; ?>

				<?php echo $fieldsById['phonenumber']->input; ?>

			</div>

			<div class="col-md-6">

				<?php echo $fieldsById['faxnumber']->label; ?>

				<?php echo $fieldsById['faxnumber']->input; ?>

			</div>

		</div>

		<div class="row">

			<div class="col-md-12">

				<?php echo $fieldsById['email1']->label; ?>

				<?php

				$input = $fieldsById['email1']->input;

				$input = str_replace('class="','class="form-control ',$input);

				echo $input;

				?>

			</div>

		</div>

		<div class="row">

			<div class="col-md-12">

				<?php echo $fieldsById['email2']->label; ?>

				<?php

				$input = $fieldsById['email2']->input;

				$input = str_replace('class="','class="form-control ',$input);

				echo $input;

				?>

			</div>

		</div>

		<div class="row">

			<div class="col-md-12">

				<?php echo $fieldsById['username']->label; ?>

				<?php

				$input = $fieldsById['username']->input;

				$input = str_replace('class="','class="form-control ',$input);

				echo $input;

				?>

			</div>

		</div>

		<div class="row">

			<div class="col-md-6">

				<?php echo $fieldsById['password1']->label; ?>

				<?php

				$input = $fieldsById['password1']->input;

				$input = str_replace('class="','class="form-control ',$input);

				echo $input;

				?>

			</div>

			<div class="col-md-6">

				<?php echo $fieldsById['password2']->label; ?>

				<?php

				$input = $fieldsById['password2']->input;

				$input = str_replace('class="','class="form-control ',$input);

				echo $input;

				?>

			</div>

		</div>

		<div class="row">

			<div class="col-md-12">

				<?php echo $fieldsById['captcha']->label; ?>

			</div>

			<div class="col-md-12">

				<?php echo $fieldsById['captcha']->input; ?>

			</div>

		</div>

		<?php echo $fieldsById['name']->input; ?>

		

		<div>

			<button class="validate" type="submit"><?php echo JText::_('JREGISTER'); ?></button>

		</div>

		<input type="hidden" id="jform_company_name" name="jform_company_id" value="" />

		<input type="hidden" name="option" value="com_users" />

		<input type="hidden" name="task" value="registration.register" />

		<?php echo JHtml::_('form.token');?>

		<script>

jQuery(document).ready(function(){

	jQuery('#jform_profile_firstname,#jform_profile_lastname').change(function(){

		jQuery('#jform_name').val(jQuery('#jform_profile_firstname').val()+" "+jQuery('#jform_profile_lastname').val());

	});

});

		</script>

	</form>



</div>