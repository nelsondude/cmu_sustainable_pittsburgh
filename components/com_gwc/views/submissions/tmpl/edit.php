<?php
// no direct access
defined('_JEXEC') or die;
$ongoing = gwcHelper::ongoing();
$year = gwcHelper::getCycle();
$active_company = gwcHelper::checkActive($this->item->company_id);
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
<?php if($ongoing && $active_company):?>
	<form action="index.php?option=com_gwc&task=submissions.update" method="post" id="submittal_form" name="submittal_form" enctype="multipart/form-data">
		<h3>Submit your document for this action</h3>
		<label for="upload_file">Upload document here:</label>
		<div>
<?php
	$check_files = scandir('/usr/home/sustainablepgh/public_html/spchallenge_submission/'.$year.'/'.$this->item->company_id.'/'.$this->item->action_id);
	//die('<pre>'.print_r(array_slice($check_files,2),1));
	//die('<pre>'.print_r(JURI::root() . 'media/com_gwc/2015/'.$this->item->company_id.'/'.$this->item->action_id.'/'.$check_files[2],1));
	foreach($check_files as $file){
		$path = '/usr/home/sustainablepgh/public_html/spchallenge_submission/'.$year.'/'.$this->item->company_id.'/'.$this->item->action_id.'/'.$file;
		if(file_exists($path)  && (!is_dir($path))){
			echo '<li><a href="/usr/home/sustainablepgh/public_html/spchallenge_submission/'.$year.'/'.$this->item->company_id.'/'.$this->item->action_id.'/'.$file.'" target="_blank">'.$file.'</a></li>';
		}
	}
?>							
		<input type="file" name="upload_file[]" /> 
			<div><button onclick="add_another()" type="button" id="addanother" class="btn btn-primary">Add another file</button></div>
		</div>
		<label for="comments">Add any comments about this submission that you have below:</label>
		<textarea name="comments"><?php echo $this->item->comments;?></textarea>
		<input type="hidden" name="id" value="<?php echo JRequest::getVar('id')?>" />
		<input type="hidden" name="points" value="<?php echo $this->item->points;?>" />
		<input type="hidden" name="user_id" value="<?php echo $this->user->id;?>" />
		<div>
			<input type="submit" value="submit" />
			<input type="button" value="cancel" />
		</div>
	</form>

	<script>
	function add_another(){
		jQuery("#addanother").parent().before('<br/><input type="file" name="upload_file[]" />');
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