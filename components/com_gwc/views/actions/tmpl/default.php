<?php
// no direct access
defined('_JEXEC') or die;
$ongoing = gwcHelper::ongoing();
	
?>
<div class="page-header">
	<h6><?php echo ($this->item->points ? $this->item->points . " points" : "Points Vary");?></h6>
	<h2 itemprop="name"><?php echo $this->item->action_number . ': ' .$this->item->name;?></h2>
</div>
<div class="page-body">
	<div class="description">
		<?php echo $this->item->description;?>
	</div>
</div>
<?php if($ongoing):?>
	<form action="index.php?option=com_gwc&task=submissions.save" method="post" id="submittal_form" name="submittal_form" enctype="multipart/form-data">
		<h3>Submit your document for this action</h3>
		<label for="upload_file">Upload document here:</label>
		<div>
		<input id="file_1" type="file" name="upload_file[]" /> <button id="del1" type="button" onclick="deleteFile(1);">Delete</button>
			<div><button onclick="add_another()" type="button" id="addanother" class="btn btn-primary">Add another file</button></div>
		</div>
		<label for="comments">Add any comments about this submission that you have below:</label>
		<textarea name="comments"></textarea>
		<input type="hidden" name="id" value="<?php echo JRequest::getVar('id')?>" />
		<input type="hidden" name="points" value="<?php echo $this->item->points;?>" />
		<input type="hidden" name="user_id" value="<?php echo $this->user->id;?>" />
		<div>
			<input type="submit" value="submit" />
			<input onclick="window.history.back();" type="button" value="cancel" />
		</div>
	</form>

	<script>
	n = 1;
	function add_another(){
		n++;
		jQuery("#addanother").parent().before('<br/><input id="file_'+n+'" type="file" name="upload_file[]" /> <button id="del'+n+'" type="button" onclick="deleteFile('+n+');">Delete</button>');
	}
	function deleteFile(f){
		jQuery("#file_"+f+", #del"+f).remove();
	}
	/*function submittal(){
		var form = document.getElementById('submittal_form');
		console.log(form);
		document.getElementById("submit").addEventListener("click", function () {
			form.submit();
		});	
	}
	*/	
	</script>
<?php endif;?>