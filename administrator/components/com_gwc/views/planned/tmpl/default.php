<?php
// no direct access
defined('_JEXEC') or die;
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
//die($listOrder);
?>

<form action="<?php echo JRoute::_('index.php?option=com_gwc&view=planned'); ?>" method="post" name="adminForm" id="adminForm">
    <?php
    // Search tools bar
//    echo JLayoutHelper::render('joomla.searchtools.default', array('view' => $this));
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

                </th>
                <th width="50%"><?php echo JHtml::_('searchtools.sort', 'Action', 'action', $listDirn, $listOrder); ?></th>
                <th width="35%"><?php echo JHtml::_('searchtools.sort', 'Company', 'co.name', $listDirn, $listOrder); ?></th>
                <th width="15%"><?php echo JHtml::_('searchtools.sort', 'Deadline', 'p.deadline', $listDirn, $listOrder); ?></th>
            </tr>
            <thead>
            <tbody>
            <?php foreach ($this->items as $i => $item) : ?>
                <tr class="row">
                    <td><?php echo $item->action;?></td>
                    <td><?php echo $item->company;?></td>
                    <td><?php echo $item->deadline;?></td>
                </tr>
            <?php endforeach;?>
            </tbody>
            <tfoot>
            <td colspan="7"><?php echo $this->pagination->getListFooter(); ?></td>
            </tfoot>
        </table>
    <?php endif; ?>

</form>
