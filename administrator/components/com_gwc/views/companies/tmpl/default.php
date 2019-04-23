<?php
// no direct access
defined('_JEXEC') or die;
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<form action="<?php echo JRoute::_('index.php?option=com_gwc&view=companies'); ?>" method="post" name="adminForm" id="adminForm">
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
					<th  width="4%"><?php echo JHtml::_('searchtools.sort', 'id', 'c.id', $listDirn, $listOrder); ?></th>
					<th width="5%"><?php echo JHtml::_('searchtools.sort', 'Active', 'c.active', $listDirn, $listOrder); ?></th>
					<th width="45%"><?php echo JHtml::_('searchtools.sort', 'Name', 'c.name', $listDirn, $listOrder); ?></th>
					<th width="31%"><?php echo JHtml::_('searchtools.sort', 'Users', 'users', $listDirn, $listOrder); ?></th>
					<th  width="5%"><?php echo JHtml::_('searchtools.sort', 'Type', 'ct.name', $listDirn, $listOrder); ?></th>
					<th  width="5%"><?php echo JHtml::_('searchtools.sort', 'Size', 'cs.name', $listDirn, $listOrder); ?></th>
					<th  width="5%"><?php echo JHtml::_('searchtools.sort', 'Points', 'c.points', $listDirn, $listOrder); ?></th>
					<th  width="5%"><?php echo JHtml::_('searchtools.sort', 'Legacy Points', 'c.legacy_points', $listDirn, $listOrder); ?></th>
				</tr>
			<thead>
			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->name; ?>">
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td><?php echo $item->id;?></td>
					<td><?php echo JHtml::_('jgrid.published', $item->active, $i, '', 1, 'cb'); ?></td>
					<td><a href="?option=com_gwc&view=companies&layout=edit&id=<?php echo $item->id;?>"><?php echo $item->name;?></a></td>
					<td><?php echo $item->users;?></td>
					<td><?php echo $item->type;?></td>
					<td><?php echo $item->size;?></td>
					<td><?php echo $item->points;?></td>
					<td><?php echo $item->legacy_points;?></td>
				</tr>
				<?php endforeach;?>
			</tbody>
			<tfoot>
			<td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
			</tfoot>
		</table>
	<?php endif; ?>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>		
</form>