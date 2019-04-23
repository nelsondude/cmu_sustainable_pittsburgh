<?php
// no direct access
defined('_JEXEC') or die;
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));

?>

<form action="<?php echo JRoute::_('index.php?option=com_gwc'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="filters">
		<tr>
			<?php
			// Search tools bar
				echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
			?>
		</tr>
	</table>
	<?php if (empty($this->items)) : ?>
		<div class="alert alert-no-items">
			<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
		</div>
	<?php else : ?>

		<table class="table table-striped">
			<thead>
				<tr>
					<th width="1%" class="hidden-phone">
						<?php echo JHtml::_('grid.checkall'); ?>
					</th>
					<th width="5%"><?php echo JHtml::_('searchtools.sort', '#', 'a.action_number', $listDirn, $listOrder); ?></th>
					<th width="70%"><?php echo JHtml::_('searchtools.sort', 'Name', 'a.name', $listDirn, $listOrder); ?></th>
					<th width="15%"><?php echo JHtml::_('searchtools.sort', 'Category', 'ac.name', $listDirn, $listOrder); ?></th>
					<th width="10%" class="nowrap text-right"><?php echo JHtml::_('searchtools.sort', 'Default Point Value', 'a.points', $listDirn, $listOrder); ?></th>
				</tr>
			<thead>
			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->category; ?>">
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td><?php echo $item->action_number;?></td>
					<td><a href="?option=com_gwc&view=actions&layout=edit&id=<?php echo $item->id;?>"><?php echo $item->name;?></a></td>
					<td><?php echo $item->category;?></td>
					<td class="nowrap text-right"><?php echo ($item->points ? $item->points : '<em>Points Vary</em>');?></td>
					
				</tr>
				<?php endforeach;?>
			</tbody>
			<tfoot>
			<td colspan="5"><?php echo $this->pagination->getListFooter(); ?></td>
			</tfoot>
		</table>
	<?php endif; ?>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="actions" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->sortColumn; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->sortDirection; ?>" />	
	<?php echo JHtml::_('form.token'); ?>		
</form>