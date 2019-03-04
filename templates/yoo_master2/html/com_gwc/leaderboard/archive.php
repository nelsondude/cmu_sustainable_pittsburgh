<?php
// no direct access
defined('_JEXEC') or die;
?>
<h1>Past Competitions</h1>
<h3><a href="<?php echo JURI::root();?>index.php/overview/results-of-the-2011-2012-competition">Results of the 2011-2012 Competition</a></h3>
<?php
foreach($this->archives as $cycle){
	if($cycle=='2014') echo '<h3><a href="'.JRoute::_('index.php?option=com_gwc&view=leaderboard&layout=default&cycle='.$cycle.'&size=1&type=1').'">Results of the 2013-2014 Competition</a></h3>';
}
?>

