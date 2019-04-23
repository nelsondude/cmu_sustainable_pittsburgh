<?php
// no direct access
defined('_JEXEC') or die;
$year = gwcHelper::getCycle();
$user = JFactory::getUser();
//die('<pre>'.print_r($this->actions,1));
//die('<pre>'.print_r($this,1));

//Group by the item category
$number_planned = count($this->planned);
$num_points = 0;
$grouped = array();
foreach ($this->planned as $i => $item) {
    $grouped[$item->category][] = $item;
    $num_points = (int)$item->points;
}

?>

<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">

<h1><?php echo $this->info->name;?></h1>
<h3>Points Awarded: <?php echo intval($this->info->points) + intval($this->info->legacy_points);?></h3>
<hr>
<?php foreach($this->actions as $label=>$actions) :?>
<h3>
	<?php echo ucwords($label);?> Actions
</h3>
<ul class="zebra">
<?php if (count($actions)) :?>
	<li class="uk-clearfix row-even">
		<span style="width:5%;float:left;">id</span>
		<span class="midcol" style="width:65%;float:left;">Action</span>
		<span class="midcol" style="width:20%;float:left;">Files</span>
		<span style="width:10%;float:right;text-align:center;">Points</span>
	</li>
	<?php foreach($actions as $i=>$action) : ?>
		<?php $check_files =  array_slice(scandir('/usr/home/sustainablepgh/public_html/spchallenge_submission/'.$year.'/'.$this->info->id.'/'.$action->action_id),2);?>
		<li class="uk-clearfix row-<?php echo ($i % 2 ? 'even' : 'odd');?>">
			<span style="width:5%;float:left;"><?php echo $action->action_number;?></span>
			<span class="midcol" style="width:65%;float:left;">
				<!--<a href="?option=com_gwc&view=submissions&layout=edit&id=<?php echo $action->id;?>">-->
					<?php echo $action->action_name;?>
				<!--</a>-->
				<p class="comments">
					<?php echo $action->comments;?>
				</p>
				<?php if ($label == 'pending'):?>
					<div class="comments" style="display:none;">
						<textarea id="comment_<?php echo $action->id;?>" name="comments"><?php echo $action->comments;?></textarea>				
					</div>
					<a id="edit_<?php echo $action->id;?>" class="uk-icon-edit"><?php echo strlen($action->comments) ? 'Edit' : 'Add';?> Comments</a>
				<?php elseif($label == 'disapproved'):?>
					<div class="admin_comments">
						Administrator Comments: <span><?php echo $action->admin_comments;?></span>
					</div>
				<?php endif;?>
			</span>
			
			<span class="midcol" style="width:20%;float:left;">
				<?php if ($label == 'pending'):?>
					<a class="modal" rel="{handler:'clone', size:{x:540}}" href="#uploads_<?php echo $action->id;?>">Upload Additional Documents</a> 
					(<?php echo count($check_files); ?>)
				
					<div id="uploads_<?php echo $action->id;?>">
						<h3>Files</h3>
						<ul class="uploads">
						
						<?php if($check_files):?>
							<?php foreach($check_files as $file):?>
								<li>
									<?php echo $file;?>
									<a class="uk-button uk-button-danger"  onclick="docRemove(this,<?php echo $action->id.',\''.$file.'\'';?>)">Remove</a>
									<!--<a class="uk-button uk-button-warning" onclick="docReplace(<?php echo $action->id.',\''.$file.'\'';?>)">Replace</a>-->
								
									<?php echo '<a class="uk-button uk-button-primary" target="_blank" href="/index.php?option=com_gwc&task=submissions.download&user_id='.$user->id.'&company='.$this->info->id.'&action='.$action->action_id.'&file='.$file.'">Review</a>';
									?>
								</li>
							<?php endforeach;?>
						<?php endif;?>
						</ul>
						<form method="post" class="uploads_form_<?php echo $action->id;?>" enctype="multipart/form-data" action="">
							<input type="hidden" name="option" value="com_gwc" />
							<input type="hidden" name="task" value="companies.newDoc" />
							<input type="hidden" name="submission" value="<?php echo $action->id;?>" />
							<input type="file" name="upload_file[]"/>
							<button type="button"  class="uk-button uk-button-primary add_another_<?php echo $action->id;?>" onclick="add_another(<?php echo $action->id;?>);">Add another file</button>
							<br/>
						</form>
						<a class="upload-new" onclick="docUpload(this,<?php echo $action->id;?>)">Upload New</a>
					</div>
				<?php else:?>
					<form method="post" class="uploads_form_<?php echo $action->id;?>" enctype="multipart/form-data" action="">
						<input type="hidden" name="option" value="com_gwc" />
						<input type="hidden" name="task" value="companies.newDoc" />
						<input type="hidden" name="submission" value="<?php echo $action->id;?>" />
						<input type="file" name="upload_file[]"/>
						<button type="button"  class="uk-button uk-button-primary add_another_<?php echo $action->id;?>" onclick="add_another(<?php echo $action->id;?>);">Add another file</button>
						<br/>
					</form>
					<!--<a class="upload-new" onclick="docUpload(this,<?php echo $action->id;?>)">Upload New</a>-->
				<?php endif;?>
			</span>
			<span style="width:10%;float:right;text-align:center;"><?php echo ($action->final_points ? $action->final_points : "*");?></span>
		</li>
	<?php endforeach; ?>
	<?php if($label=='pending'):?>
		<div style="clear:both;">
			*<em style="color:#c00;line-height:4em;font-size:12px;"> - The points for this action vary and will be assigned upon approval</em>
		</div>
	<?php endif; ?>
<?php else :?>
	<li style="margin:1em 0 2em;"><em>No <?php echo $label;?> actions.</em></li>
<?php endif; ?>
</ul>
<?php endforeach; ?>
<hr>

<h3 id="planner-title">Planned Actions (<?php echo $number_planned?>)</h3>
<h4>Total Points: <?php echo $num_points ?></h4>

<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

<div id="accordion_search_bar_container">
    <input type="search"
           id="accordion_search_bar"
           placeholder="Search"/>
    <button class="btn btn-success" id="expandall">Expand All</button>
    <button class="btn btn-success" id="collapseall">Collapse All</button>
</div>

<div class="panel-group"
     id="accordion"
     role="tablist"
     aria-multiselectable="true">
    <?php foreach ($grouped as $category => $items) :?>
        <?php $stripped_category = str_replace(' ', '', $category) ?>
        <div class="panel panel-success"
             id="collapse<?php echo $stripped_category;?>_container">
            <div class="panel-heading"
                 role="tab"
                 id="headingOne">
                <h4 class="panel-title">
                    <a role="button"
                       data-toggle="collapse"
                       data-target='#collapse<?php echo $stripped_category?>'
                       href="javascript:void(0);"
                       aria-expanded="true"
                       aria-controls="collapse<?php echo $stripped_category;?>">
                        <?php echo $category ?>
                    </a>
                </h4>
            </div>
            <div id="collapse<?php echo $stripped_category;?>"
                 class="panel-collapse collapse in"
                 role="tabpanel"
                 aria-labelledby="headingOne">
                <div class="panel-body">
                    <table class="planning-table">
                        <tr>
                            <th>#</th>
                            <th>Action</th>
                            <th>Planned Deadline</th>
                            <th class="planning-points">Points</th>
                            <th class="planning-delete">Delete</th>
                        </tr>

                        <?php foreach ($items as $index => $item) : ?>
                            <tr id="tablerow<?php echo $item->action_id?>" class="plan-row">
                                <td><?php echo $item->action_number?></td>
                                <td><?php echo $item->action_name?></td>
                                <td><input class="form-control" type="date" value="<?php echo $item->deadline?>" data-id="<?php echo $item->action_id?>"></td>
                                <td class="planning-points"><?php echo $item->points?></td>
                                <td class="planning-delete"><i class="fa fa-trash delete-button" aria-hidden="true" data-id="<?php echo $item->action_id?>"></i></td>
                            </tr>
                        <?php endforeach;?>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach;?>
</div>

<script>
    var searchTerm, panelContainerId;
    var $ = jQuery;
    $.expr[':'].containsCaseInsensitive = function (n, i, m) {
        return jQuery(n).text().toUpperCase().indexOf(m[3].toUpperCase()) >= 0;
    };

    $('#accordion_search_bar').on('change keyup paste click', function () {
        const searchTerm = $(this).val();
        $('#accordion > .panel').each(function () {
            panelContainerId = '#' + $(this).attr('id');
            $(panelContainerId + ':not(:containsCaseInsensitive(' + searchTerm + '))').hide();
            $(panelContainerId + ':containsCaseInsensitive(' + searchTerm + ')').show();
        });
        $('.planning-table .plan-row:not(:containsCaseInsensitive(' + searchTerm + '))').css('display', 'none');
        $('.planning-table .plan-row:containsCaseInsensitive(' + searchTerm + ')').css('display', 'table-row');
    });
    $('#expandall').click(function() {
        $('#accordion .collapse').collapse('show');
    });
    $('#collapseall').click(function() {
        $('#accordion .collapse').collapse('hide');
    });
</script>


<script>
$ = jQuery;
function add_another(submission){
	$(".add_another_" + submission).before('<br/><input type="file" name="upload_file[]" />');
}
function docUpload(elem,submission){
	$(".uploads_form_" + submission).show();
	elem.innerHTML = "Submit New Files";
	$form = $(elem).prev();
	elem.addEventListener("click",function(){
		$form.submit();
	});
}
function docRemove(elem,submission,file){
	var del = window.confirm("Are you sure you want to delete " + file + "?");
	if(del == true){
		$.post('index.php?option=com_gwc&task=companies.removeDoc', {"submission":submission, "file": file} )
		 .done(function(data){
			$(elem).parent().remove();
		});
	}
}
$(document).ready(function(){
	$(".uk-icon-edit").click(function(){
		submission = $(this).attr("id").replace("edit_","");
		$(this).siblings(".comments").toggle();
		$(this).toggleClass("uk-icon-close uk-text-danger");
		if($(this).hasClass("uk-icon-close")){
			saveBtn = document.createElement("A");
			saveBtn.className = "uk-icon-check";
			saveBtn.innerHTML = 'Save changes';
			$(this).after(saveBtn);
			saveBtn.addEventListener("click", function(){
				orig = $(this).prev();
				$.post('index.php?option=com_gwc&task=companies.updateComment', {"submission":submission, "comments":$("#comment_"+submission).val()} )
				 .done(function(data){
					$(orig).siblings(".comments").toggle();
					$(orig).siblings("p.comments").text($("#comment_"+submission).val());
					$(orig).siblings("p.comments").append('<span class="uk-text-success">Saved!</span>');
					setTimeout(function(){
						$(orig).siblings("p.comments").find(".uk-text-success").fadeOut(300,function(){$(this).remove()});
					},2000);
					$(orig).toggleClass("uk-icon-close uk-text-danger");
					$(saveBtn).remove();
				});				
			});
		} else {
			$(this).next(".uk-icon-check").remove();
		}
	});

	$('input[type="date"]').change(function() {
	    var deadline = $(this).val();
	    var id = $(this).data("id");
	    $.post('index.php?option=com_gwc&task=companies.updatePlannedAction', {"action_id": id, "deadline": deadline})
            .done(function(data) {
                console.log('done');
            })
    });

	$('.delete-button').click(function() {
        var del = window.confirm("Are you sure you want to remove this action from your planner?");
        if (!del) return;

	    var id = $(this).data("id");
        $.post('index.php?option=com_gwc&task=companies.removePlannedAction', {"action_id": id} )
            .done(function(data){
                $('#tablerow' + id).remove();
            });
    })
});
</script>
