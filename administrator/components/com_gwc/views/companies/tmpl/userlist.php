<?php
	echo '<ul style="margin:0;list-style:none;">';
	foreach($this->users as $user){
		echo '<li class="item" style="padding:0.5em;color:#08c;cursor:pointer;border-bottom:1px #ddd solid;" id=user_'.$user->id.'>'.$user->name.' <span style="display:inline-block;width:50%;float:right;">'.$user->email.'</span></li>';
	}
	echo '</ul>';
?>
<style>
.item:hover{background:rgba(200,200,200,0.4);}
</style>
<script>
(function($){
	function add_user(userid){
		$.post( "index.php?option=com_gwc&view=companies&task=addUser", { userid: userid, companyid: "<?php echo $this->item->id;?>" } )
			.done(function(r){
				var json = $.parseJSON(r);
				//window.parent.SqueezeBox.close();
				window.parent.location.reload();
			});
	}
	
	$(document).ready(function(){
		$("li.item").click(function(){
			var uid = $(this).attr("id").replace("user_","");
			add_user(uid);
		});
	});
})(jQuery);
</script>


