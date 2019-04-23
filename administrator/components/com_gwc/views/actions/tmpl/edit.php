<?php
// no direct access
defined('_JEXEC') or die;

?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="admin_block">
		<div class="control-group">
			<label class="control-label" for="name">Name:</label>
			<input type="text" name="name" id="name" value="<?php echo empty($this->item) ? "" : $this->item->name; ?>" maxlength="250" />
		</div>
		<div>
			<label class="control-label" for="action_number">Action Number:</label>
			<input type="text" name="action_number" id="action_number" value="<?php echo $this->item->action_number; ?>" maxlength="250" />
		</div>		
		<div>
			<label class="control-label" for="alias">Alias:</label>
			<input type="text" name="alias" id="alias" value="<?php echo strlen($this->item->alias) ? $this->item->alias : str_replace(' ', '-', strtolower($this->item->name)); ?>" maxlength="250" />
		</div>
		<div>
			<label class="control-label" for="cat_id">Category:</label>
			<?php echo JHTML::_('select.genericlist', $this->categories, "cat_id", 'class="inputbox"', 'value', 'text', $this->item->cat_id);?>
		</div>
		<div>
			<label class="control-label" for="points">Default Point Value:</label>
			<input type="text" name="points" id="points" value="<?php echo ($this->item->points ? $this->item->points : "POINTS VARY"); ?>" maxlength="250" />
		</div>			
		<div>
			Description:
		</div>
		<div>
			<textarea id="description" name="description" style="width: 99%; height: 300px;"><?php echo empty($this->item) ? "" : $this->item->description?></textarea>
		</div>
		<input type="hidden" name="option" value="com_gwc" />
		<input type="hidden" name="id" value="<?php echo JRequest::getVar('id'); ?>" />
		<input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>" />
		<input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>" />
	</div>
	<?php echo JHTML::_('form.token'); ?>
</form>