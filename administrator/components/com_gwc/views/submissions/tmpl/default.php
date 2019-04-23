<?php
// no direct access
defined('_JEXEC') or die;
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
//die($listOrder);
?>

<form action="<?php echo JRoute::_('index.php?option=com_gwc&view=submissions'); ?>" method="post" name="adminForm" id="adminForm">
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
					<th width="5%"><?php echo JHtml::_('searchtools.sort', 'Approved', 's.approved', $listDirn, $listOrder); ?></th>
					<th width="35%"><?php echo JHtml::_('searchtools.sort', 'Action', 'a.name', $listDirn, $listOrder); ?></th>
					<th width="15%"><?php echo JHtml::_('searchtools.sort', 'Date', 's.created', $listDirn, $listOrder); ?></th>
					<th width="20%"><?php echo JHtml::_('searchtools.sort', 'Company', 'co.name', $listDirn, $listOrder); ?></th>
					<th width="20%"><?php echo JHtml::_('searchtools.sort', 'Category', 'ac.name', $listDirn, $listOrder); ?></th>
					<th width="5%"><?php echo JHtml::_('searchtools.sort', 'Points', 's.final_points', $listDirn, $listOrder); ?></th>
				</tr>
			<thead>
			<tbody>
				<?php foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->company; ?>">
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td><?php echo JHtml::_('jgrid.published', $item->approved, $i, '', 1, 'cb'); ?></td>
					<td><span style="display:inline-block;width:auto;margin-right:1em;"><?php echo $item->action_number;?>:</span> <a href="?option=com_gwc&view=submissions&layout=edit&id=<?php echo $item->id;?>"><?php echo $item->action;?></a></td>
					<td><?php echo date('M j, Y', strtotime($item->created));?></td>
					<td><?php echo $item->company;?></td>
					<td><?php echo $item->category;?></td>
					<td><?php echo $item->final_points;?></td>
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