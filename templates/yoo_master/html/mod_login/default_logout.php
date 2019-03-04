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

?>

<?php if ($type == 'logout') : ?>
	<div>
		<a href="<?php echo JRoute::_('index.php?option=com_users&view=profile');?>">Edit Profile</a>
	</div>	
	<?php
		define('DS', DIRECTORY_SEPARATOR);
		require_once( JPATH_ROOT . DS . 'components' . DS . 'com_gwc' . DS . 'helpers' . DS . 'gwc.php' );
		$cid = gwcHelper::getUserByCompany($user->id);
		if($cid){
			echo '<div><a href="'.JRoute::_('index.php?option=com_gwc&view=companies').'">View your submissions</a></div>';
		}
	?>
	<form class="short style" action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post">
	
		<?php if ($params->get('greeting')) : ?>
		<div class="greeting">
			<?php if ($params->get('name') == 0) : {
				echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('name')));
			} else : {
				echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('username')));
			} endif; ?>
		</div>
		<?php endif; ?>
	
		<div class="button">
			<button value="<?php echo JText::_('JLOGOUT'); ?>" name="Submit" type="submit"><?php echo JText::_('JLOGOUT'); ?></button>
		</div>
		
		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>	
	</form>
	
	<script>
		jQuery(function($){
			$('form.login input[placeholder]').placeholder();
		});
	</script>
	
<?php endif; ?>
