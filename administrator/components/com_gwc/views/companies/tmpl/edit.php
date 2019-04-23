<?php
// no direct access
defined('_JEXEC') or die;
JHTML::_('behavior.modal');
?>

<form action="<?php echo JRoute::_('index.php?option=com_gwc&view=companies'); ?>" method="post" name="adminForm" id="adminForm">

	<div class="admin_block row-fluid">
	<div class="span3">
		<h5>Company Details</h5>
		<div class="control-group">
			<label class="control-label" for="name">Name:</label>
			<input class="input-xlarge input-large-text" type="text" name="name" id="name" value="<?php echo empty($this->item) ? "" : $this->item->name; ?>" maxlength="255" />
		</div>
		
		<div class="clearfix" style="width:250px;">
			<label class="control-label">Active:</label>
				<input id="jform_active0" type="radio" <?php echo ($this->item->active ? 'checked="checked"' : '');?> value="1" name="active">
				<label class="btn <?php echo ($this->item->active ? 'active btn-success' : '');?>" for="jform_active0">Yes</label>
				<input id="jform_active1" type="radio" <?php echo ($this->item->active ? '' : 'checked="checked"');?> value="0" name="active">
				<label class="btn <?php echo ($this->item->active ? '' : 'active btn-danger');?>" for="jform_active1">No</label>
		</div>		
		
		<div class="control-group">
			<label class="control-label" for="points">Points:</label>
			<input class="input-large" type="text" name="points" id="points" value="<?php echo empty($this->item) ? "0" : $this->item->points; ?>" maxlength="255" />
		</div>	
		<div class="control-group">
			<label class="control-label" for="legacy_points">Legacy Points:</label>
			<input class="input-large" type="text" name="legacy_points" id="legacy_points" value="<?php echo empty($this->item) ? "0" : $this->item->legacy_points; ?>" maxlength="255" />
		</div>
		<div>
			<label class="control-label" for="type">Type:</label>
			<?php echo JHTML::_('select.genericlist', $this->types, "type", 'class="inputbox"', 'value', 'text', $this->item->type);?>
		</div>
		<div>
			<label class="control-label" for="size">Size:</label>
			<?php echo JHTML::_('select.genericlist', $this->sizes, "size", 'class="inputbox"', 'value', 'text', $this->item->size);?>
		</div>
	</div>	
	<div class="span9">
		
		<h5 style="background:#f2f2f2;margin-bottom:0;border:1px solid #ddd;border-width:1px 0 0;font-style:italic;padding:0.5em;">
			Users
		</h5>
			
			<table style="border:1px solid #f2f2f2;" class="table table-striped">
				<thead>
					<th width="40%">Name</th>
					<th width="40%">Email</th>
					<th width="20%">
						<a 
						<?php echo ($this->item->id) ? '' : ' disabled title="Must save company before assigning users"';?>
						rel="{handler:'iframe', size: {x: 800, y: 460}}" href="index.php?option=com_gwc&view=companies&tmpl=component&layout=userlist&id=<?php echo $this->item->id;?>" class="modal btn btn-primary" id="adduser" style="width:80px;">Add User</a>
					</th>
				</thead>
				<tbody>
				<?php if(count($this->companyusers)):?>
				<?php foreach($this->companyusers as $i=>$user) : ?>
				<tr>
					<td><?php echo $user->name;?></td>
					<td><?php echo $user->email;?></td>	
					<td><a id="user_<?php echo $user->id;?>" href="#" class="btn btn-danger" style="width:80px;">Remove User</a></td>	
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				</tbody>
			</table>
			<!--
		<div class="clearfix">
			<div id="toolbar-unpublish" class="btn-wrapper" style="float:right;">
				<button class="btn btn-small" onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('submissions.unpublish')}">
					<span class="icon-unpublish"></span>
					Unapprove
				</button>
			</div>			
			<div id="toolbar-publish" class="btn-wrapper" style="float:right;margin:0 5px;">		
				<button class="btn btn-small" onclick="if (document.adminForm.boxchecked.value==0){alert('Please first make a selection from the list');}else{ Joomla.submitbutton('submissions.publish')}">
					<span class="icon-publish"></span>
					Approve
				</button>
			</div>		
		</div>	
-->		<?php if(count($this->actions)):?>
		<?php foreach($this->actions as $label=>$actions) :?>
		<h5 style="background:#f2f2f2;margin-bottom:0;border:1px solid #ddd;border-width:1px 0 0;font-style:italic;padding:0.5em;">
			<?php echo ucwords($label);?> Actions
		</h5>
		<?php if (count($actions)) :?>
			<table id="user_list" style="border:1px solid #f2f2f2;" class="table table-striped">
				<thead>
					<!--<th width="1%" class="hidden-phone">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>				
					<th width="5%"></th>-->
					<th width="80%">Name</th>
					<th width="15%" style="text-align:right;">Points</th>
					
				</thead>
				<tbody>
				<?php foreach($actions as $i=>$action) : ?>
				<tr>
					<!--<td class="center">
						<?php echo JHtml::_('grid.id', $i, $action->id); ?>
					</td>				
					<td><?php echo JHtml::_('jgrid.published', $action->approved, $i, 'submissions.', 1, 'cb'); ?></td>	-->
					<td><a href="?option=com_gwc&view=submissions&layout=edit&id=<?php echo $action->id;?>"><?php echo $action->action_name;?></a></td>
					<td style="text-align:right;"><?php echo $action->final_points;?></td>	
				</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		<?php else :?>
			<p style="margin:1em 0 2em;"><em>No <?php echo $label;?> actions.</em></p>
		<?php endif; ?>
		<?php endforeach; ?>
		<?php endif; ?>
	</div>	

		<input type="hidden" name="option" value="com_gwc" />
		<input type="hidden" name="id" value="<?php echo JRequest::getVar('id'); ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />		

	</div>
	<?php echo JHTML::_('form.token'); ?>
</form>

<style>
	input[id^='jform_active']{
		display:none;
	}
	label[for^='jform_active'] {
		width:50%;
		float:left;
		box-sizing:border-box;
		-moz-box-sizing:border-box;
		-webkit-box-sizing:border-box;			
	}
	label[for='jform_active0']{border-radius:4px 0 0 4px;}
	label[for='jform_active1']{border-radius:0 4px 4px 0;}
</style>

<script>
(function($){
	function remove_user(userid){
		$.post( "index.php?option=com_gwc&view=companies&task=removeUser", { userid: userid, companyid: "<?php echo $this->item->id;?>" } )
			.done(function(r){
				var json = $.parseJSON(r);
				window.parent.location.reload();
			});
	}
	
	$(document).ready(function(){
		$("[id^='user_']").click(function(){
			var uid = $(this).attr("id").replace("user_","");
			remove_user(uid);
		});
		$("label[for='jform_active0'], label[for='jform_active1']").click(function(){
			$("label[for='jform_active0']").toggleClass('btn-success');
			$("label[for='jform_active1']").toggleClass('btn-danger');
		});
	});
})(jQuery);
</script>