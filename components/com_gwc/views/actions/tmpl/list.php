<?php

// no direct access

defined('_JEXEC') or die;

$cat = '';

$ongoing = gwcHelper::ongoing();

usort($this->items,function($a,$b){
	if(preg_match('/([A-Z]+)(\d+)/',$a->action_number,$matches)) list(,$keya,$numa) = $matches;
	if(preg_match('/([A-Z]+)(\d+)/',$b->action_number,$matches)) list(,$keyb,$numb) = $matches;
	if(!$keya) return 1;
	if($keya==$keyb) {
		return (0+$numa < 0+$numb) ? -1 : 1;
	}
	else return strcmp($keya,$keyb);
});


?>
<h1>TEST</h1>
<ul class="actionlist">

<?php foreach ($this->items as $i => $item) : ?>
<?php //if ((substr($item->action_number, 0, 3) != "K12" && $this->userinfo->type !=4) || (substr($item->action_number, 0, 3) == "K12" && $this->userinfo->type ==4)) { ?>
<?php if (in_array($this->userinfo->type, explode(",",$item->type_ids))) { ?>

	<?php if($cat != $item->category):?>

		<h3><?php echo $item->category;?></h3>

	<?php endif;?>
	<li class="clearfix row<?php echo $i%2;?>"><strong><?php echo $item->action_number;?></strong> 
		<?php if($ongoing):?>

		<a class="" href="index.php?option=com_gwc&view=actions&layout=default&id=<?php echo $item->id;?>">

			<?php echo $item->name;?>

		</a>

		<?php else :?>

			<?php echo $item->name;?>

		<?php endif;?>

	</li>

	<?php $cat = $item->category;?>
<?php } ?>
<?php endforeach;?>

</ul>
