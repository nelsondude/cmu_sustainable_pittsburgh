<?php

// no direct access

defined('_JEXEC') or die;

$disabled = '';

if ($this->item->id) $disabled = 'disabled';



$year = gwcHelper::getCycle();

?>



<form action="index.php" method="post" name="adminForm" id="adminForm">

	<div class="admin_block container">

		<div class="span4">

			<div class="control-group">

				<label class="control-label" for="action_id">Action ID:</label>

				<?php if ($this->item->id) :?>

					<input type="text" <?php echo $disabled;?> name="action_id" id="action_id" value="<?php echo $this->item->action_id; ?>" maxlength="250" />

				<?php else :?>

					<?php echo JHTML::_('select.genericlist', $this->actions, "action_id", 'class="inputbox"', 'value', 'text', '');?>

				<?php endif;?>				

			</div>

			<div class="control-group">

			<?php if ($this->item->id) :?>

				<label class="control-label" for="name">Action Name:</label>

				<input type="text" <?php echo $disabled;?> name="name" id="name" value="<?php echo $this->item->action; ?>" maxlength="250" />

			<?php endif;?>				

			</div>

			<div>

				<label class="control-label" for="created">Date</label>

				<input type="text" name="created" id="created" value="<?php echo $this->item->created; ?>" />

			</div>					

			<div>

				<label class="control-label" for="company">Company:</label>

				<?php if ($this->item->id) :?>

					<input type="text" <?php echo $disabled;?> name="company_name" id="company_name" value="<?php echo $this->item->company; ?>" maxlength="250" />

					<input type="hidden" name="company" value="<?php echo $this->item->company_id; ?>" />

				<?php else :?>

					<?php echo JHTML::_('select.genericlist', $this->companies, "company", 'class="inputbox"', 'value', 'text', '');?>

				<?php endif;?>

			</div>

			<div>

				<label class="control-label" for="category">Category:</label>

				<?php if ($this->item->id) :?>

					<input type="text" <?php echo $disabled;?> name="category" id="category" value="<?php echo $this->item->category;?>" maxlength="250" />

				<?php endif;?>

			</div>

		</div>

		<div class="span8">

			<div>

				<label class="control-label" for="points">Point Value:</label>

				<input type="text" name="points" id="points" value="<?php echo ($this->item->final_points ? $this->item->final_points : "POINTS VARY"); ?>" maxlength="250" />

			</div>	

			<div>

				<label class="control-label" for="approved">Approval Status:</label>

				<select name="approved">

					<option value="0">Pending</option>
					
					<option value="-1" <?php echo ($this->item->approved==-1 ? "selected" : ""); ?>>Disapproved</option>

					<option value="1" <?php echo ($this->item->approved==1 ? "selected" : ""); ?>>Approved</option>

				</select>

				<input type="hidden" name="original" value="<?php echo $this->item->approved; ?>" />

			</div>

			<div>

				<label class="control-label">Uploaded File(s):</label>
				<?php if(count($this->files)):?>

				<ul>

					<?php //foreach($this->files as $file):?>

						<?php //if(file_exists(JURI::root() . 'media/com_gwc/2015/'.$this->item->company_id.'/'.$this->item->action_id.'/'.$file)) {?>

							<!--

							<li><a href="<?php echo JURI::root() . 'media/com_gwc/2015/'.$this->item->company_id.'/'.$this->item->action_id.'/'.$file;?>"><?php echo $file;?></a></li>

							-->

						<?php //} else {

							$check_files = scandir('/usr/home/sustainablepgh/public_html/spchallenge_submission/'.$year.'/'.$this->item->company_id.'/'.$this->item->action_id);

							//die('<pre>'.print_r(array_slice($check_files,2),1));

							//die('<pre>'.print_r(JURI::root() . 'media/com_gwc/2015/'.$this->item->company_id.'/'.$this->item->action_id.'/'.$check_files[2],1));

							foreach($check_files as $file){

								$path = '/usr/home/sustainablepgh/public_html/spchallenge_submission/'.$year.'/'.$this->item->company_id.'/'.$this->item->action_id.'/'.$file;

								if(file_exists($path)  && (!is_dir($path))){
									echo '<li><a target="_blank" href="/administrator/index.php?option=com_gwc&view=submissions&task=download&id=7539&company='.$this->item->company_id.'&action='.$this->item->action_id.'&file='.$file.'">'.$file.'</a></li>';

								}

							}

						//}

						?>

					<?php //endforeach;?>

				</ul>

				<?php else :?>

				<p><em>No file uploaded for this submittal.</em></p>

				<?php endif;?>

			</div>

			<div>

				User Comments:

				<p>

				<?php echo isset($this->item->comments) ? $this->item->comments : "<em>No comments for this submittal</em>" ; ?>

				</p>

			</div>

			<div class="clearfix" style="width:250px;">

				<label class="control-label">Legacy Item:</label>

					<input id="jform_legacy0" type="radio" <?php echo ($this->item->legacy ? 'checked="checked"' : '');?> value="1" name="legacy">

					<label class="btn <?php echo ($this->item->legacy ? 'active btn-success' : '');?>" for="jform_legacy0">Yes</label>

					<input id="jform_legacy1" type="radio" <?php echo ($this->item->legacy ? '' : 'checked="checked"');?> value="0" name="legacy">

					<label class="btn <?php echo ($this->item->legacy ? '' : 'active btn-danger');?>" for="jform_legacy1">No</label>

			</div>

			<div>
				<label class="control-label" for="admin_comments">
					Admin Comments:
				</label>
				<textarea name="admin_comments"><?php echo trim($this->item->admin_comments);?></textarea>
			</div>
		</div>

		<input type="hidden" name="option" value="com_gwc" />

		<input type="hidden" name="id" value="<?php echo JRequest::getVar('id'); ?>" />

		<input type="hidden" name="orig_points" value="<?php echo $this->item->final_points; ?>" />

		<input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>" />

		<input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>" />

	</div>

	<?php echo JHTML::_('form.token'); ?>

</form>

<style>

	input[id^='jform_legacy']{

		display:none;

	}

	label[for^='jform_legacy'] {

		width:50%;

		float:left;

		box-sizing:border-box;

		-moz-box-sizing:border-box;

		-webkit-box-sizing:border-box;			

	}

	label[for='jform_legacy0']{border-radius:4px 0 0 4px;}

	label[for='jform_legacy1']{border-radius:0 4px 4px 0;}

</style>

<script>

jQuery(document).ready(function(){

	jQuery("label[for^='jform_legacy']").click(function(){

		var val = jQuery(this).attr('for').replace('jform_legacy','');

		jQuery(this).addClass('active').siblings('label').removeClass('active');

		if(val == 0){

			jQuery(this).addClass('btn-success').siblings('label').removeClass('btn-danger')

		} else {

			jQuery(this).addClass('btn-danger').siblings('label').removeClass('btn-success')

		}

	});

});

</script>