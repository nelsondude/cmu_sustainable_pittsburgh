<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_gwc' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'gwc.php');
$id = gwcHelper::getCompanyByUser($user->id);

$prop = "";

?>

<?php if ($type == 'logout') : ?>

	<form class="uk-form" action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post">

		<?php if ($params->get('greeting')) : ?>
		<div class="uk-form-row">

			<?php
            if ($params->get('name') == 0) : {
			    $prop = 'name';
			} else : {
			    $prop = 'username';
			} endif; ?>
            <a href="<?php echo JRoute::_('index.php?option=com_gwc&view=companies&id=' . $id); ?>" id="profile-link"><?php echo htmlspecialchars($user->get($prop));?></a>
		</div>
		<?php endif; ?>

		<div class="uk-form-row">
			<button class="btn btn-primary" value="<?php echo JText::_('JLOGOUT'); ?>" name="Submit" type="submit"><?php echo JText::_('JLOGOUT'); ?></button>
		</div>

		<input type="hidden" name="option" value="com_users">
		<input type="hidden" name="task" value="user.logout">
		<input type="hidden" name="return" value="<?php echo $return; ?>">
		<?php echo JHtml::_('form.token'); ?>
	</form>

<?php endif; ?>
