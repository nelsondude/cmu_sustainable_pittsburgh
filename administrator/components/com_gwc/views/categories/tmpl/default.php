<?php
// no direct access
defined('_JEXEC') or die;
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo JRoute::_('index.php?option=com_gwc&view=categories'); ?>" method="post" name="adminForm" id="adminForm">
	<?php
	// Search tools bar
	echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
	?>
	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>
		<table class="table table-striped" id="articleList">
			<thead>
				<tr>
					<th width="1%" class="hidden-phone">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="4%"><?php echo JHtml::_('searchtools.sort', 'id', 'id', $listDirn, $listOrder); ?></th>
					<th width="2%">Color</th>
					<th width="93%"><?php echo JHtml::_('searchtools.sort', 'Name', 'name', $listDirn, $listOrder); ?></th>
				</tr>
			<thead>
			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->name; ?>">
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td><?php echo $item->id;?></td>
					<td><div style="width:1em;height:1em;background-color:#<?php echo $item->color;?>"> </div>
					<td><a href="?option=com_gwc&view=categories&layout=edit&id=<?php echo $item->id;?>"><?php echo $item->name;?></a></td>
				</tr>
				<?php endforeach;?>
			</tbody>
			<tfoot>
			<td colspan="4"><?php echo $this->pagination->getListFooter(); ?></td>
			</tfoot>			
		</table>
	<?php endif; ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>		
</form>