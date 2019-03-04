<?php

// no direct access

defined('_JEXEC') or die;



?>

<div class="page-header">

	<h2 itemprop="name">Available Actions</h2>

	<form action="index.php?option=com_gwc&view=categories" method="post" name="filter_categories">

		<div class="filtering clearfix">

			<div class="clearfix">

				<input type="submit" value="Filter" />

				<a class="button clearfilter" href="index.php?option=com_gwc&view=categories">Clear</a>

			</div>

		<?php foreach($this->items as $i=>$item) :?>

			<div class="item">

				<?php echo JHtml::_('grid.id', $i, $item->cat_id); ?>

				<label><?php echo $item->category;?></label>

			</div>

		<?php endforeach ;?>

		</div>

	</form>

</div>

<div class="page-body">

	<?php foreach($this->filtered_items as $i=>$item) :?>

		<h3><?php echo $item->category;?></h3>

		<ul class="zebra">

		<?php foreach($item->actions as $j=>$action):?>
<?php if ((substr($action->action_number, 0, 3) != "K12" && $this->userinfo->type !=4) || (substr($action->action_number, 0, 3) == "K12" && $this->userinfo->type ==4)) { ?>

		<li class="row-<?php echo ($j % 2 ? 'even' : 'odd');?>"><a href="<?php echo JRoute::_('?option=com_gwc&view=actions&id='.$action->id);?>"><?php echo $action->name;?></a></li>
<?php } ?>

		<?php endforeach;?>

		</ul>

	<?php endforeach;?>

</div>