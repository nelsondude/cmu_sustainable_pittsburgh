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
		<!-- <div class="clearfix" style="width:250px;">
			<label class="control-label">Active:</label>
				<input id="jform_active0" type="radio" <?php echo ($this->item->active ? 'checked="checked"' : '');?> value="1" name="active">
				<label class="btn <?php echo ($this->item->active ? 'active btn-success' : '');?>" for="jform_active0">Yes</label>
				<input id="jform_active1" type="radio" <?php echo ($this->item->active ? '' : 'checked="checked"');?> value="0" name="active">
				<label class="btn <?php echo ($this->item->active ? '' : 'active btn-danger');?>" for="jform_active1">No</label>
		</div> -->	
		<div>
			<label class="control-label" for="alias">Alias:</label>
			<input type="text" name="alias" id="alias" value="<?php echo isset($this->item->alias) ? $this->item->alias : str_replace(' ', '-', strtolower($this->item->name)); ?>" maxlength="250" />
		</div>
		<div>
			<label class="control-label" for="color">Color: (use a hex value)</label>
			<input type="text" name="color" id="color" value="<?php echo empty($this->item) ? "999" : $this->item->color; ?>" />
		</div>
		<div>
			<label class="control-label" for="color">Legacy point percentage (any integer from 0 to 100)</label>
			<input type="text" name="legacy_percentage" id="legacy_percentage" value="<?php echo empty($this->item) ? "0" : $this->item->legacy_percentage; ?>" />
		</div>
		<div>
			<label class="control-label" for="cat_id">Organization Types (Select the types these actions should be allowed for):</label>
			<?php echo JHTML::_('select.genericlist',  $this->types, 'type_ids[]', 'class="inputbox" multiple="multiple" size="8"', 'id', 'title', explode(',',$this->item->type_ids)); ?>

		</div>
		<div>
			Description:
		</div>
		<div>
			<textarea id="description" name="description" style="width: 99%; height: 300px;"><?php echo empty($this->item) ? "" : $this->item->description?></textarea>
		</div>
		<input type="hidden" name="option" value="com_gwc" />
		<input type="hidden" name="myInfo" id="myInfo" value="test test test " >

		<input type="hidden" name="id" value="<?php echo JRequest::getVar('id'); ?>" />
		<input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>" />
		<input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>" />
	</div>
	<?php echo JHTML::_('form.token'); ?>
</form>

<script>
(function($){

	$(document).ready(function(){
		$("label[for='jform_active0'], label[for='jform_active1']").click(function(){
			$("label[for='jform_active0']").toggleClass('btn-success');
			$("label[for='jform_active1']").toggleClass('btn-danger');
		});
		$('#modal-cog').on('hidden.bs.modal', function () {
			window.location.replace("/administrator/index.php?option=com_gwc&view=categories");
		})

	});

})(jQuery);
</script>