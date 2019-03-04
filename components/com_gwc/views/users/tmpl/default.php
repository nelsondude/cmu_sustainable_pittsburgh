<?php

?>
<form method="POST" action="index.php">
	<h3>Please confirm your participation in the 2015 competition</h3>
	<input type="submit" class="btn btn-primary" value="Confirm"/>
	<a class="btn btn-danger" href="<?php echo JURI::root();?>">Cancel</a>
	<input type="hidden" name="option" value="com_gwc"/>
	<input type="hidden" name="view" value="users"/>
	<input type="hidden" name="task" value="users.activateuser"/>
	<input type="hidden" name="uid" value="<?php echo $this->user;?>"/>
</form>