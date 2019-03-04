<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

// load user_profile plugin language
$lang = JFactory::getLanguage();
$lang->load( 'plg_user_profile', JPATH_ADMINISTRATOR);

require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_gwc' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'gwc.php');
gwcHelper::getCompanies();
$this->data->companyid = gwcHelper::getCompanyByUser($this->data->id);
$this->data->organization = gwcHelper::getCompanyNameByUser($this->data->id);
$company_options = gwcHelper::getTypesSizes();
?>

<div id="system">

	<?php if ($this->params->get('show_page_heading')) : ?>
	<h1 class="title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
	<?php endif; ?>

	<?php if($this->data->companyid):?>
	<h3>View Organization information for 
		<a href="<?php echo JRoute::_('index.php?option=com_gwc&view=companies&id='.$this->data->companyid);?>">
			<?php echo $this->data->organization->name;?>
		</a>
	</h3>
	<?php endif; ?>	
	
	
		<?php

		$fieldsets = $this->form->getFieldsets();

		//$fieldsets = array_reverse($fieldsets);
		unset($fieldsets['params']);
		unset($fieldsets['gwc']);
		
		foreach ($fieldsets as $fieldset): ?>
			<?php 
				$fields = $this->form->getFieldset($fieldset->name);
					//die('<pre>'.print_r($fields,1));
			?>
			
			<?php if (count($fields)): ?>
				<?php foreach ($fields as $field): ?>
					<?php if (!$field->hidden && $field->type !="Password" && $field->fieldname != "email2"): ?>
						<div class="userprof">
							<span><?php echo $field->label; ?></span>
							<span><?php echo $field->value; ?></span>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>				
			<?php endif; ?>
		<?php endforeach; ?>


	</form>

</div>