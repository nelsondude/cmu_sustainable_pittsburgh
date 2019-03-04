<?php
/**
* @package   yoo_master
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// get template configuration
include($this['path']->path('layouts:template.config.php'));
	
?>
<!DOCTYPE HTML>
<html lang="<?php echo $this['config']->get('language'); ?>" dir="<?php echo $this['config']->get('direction'); ?>">

<head>

<?php echo $this['template']->render('head'); ?>
  <link rel="stylesheet" href="/modules/mod_smartcountdown3/css/flipclock.css" type="text/css" />
  <link rel="stylesheet" href="/modules/mod_smartcountdown3/css/smart-flipclock.css" type="text/css" />
  <script src="https://use.typekit.net/xri4ugw.js"></script>
	<style>
	.tk-myriad-pro-condensed{font-family:"myriad-pro-condensed",sans-serif;}.tk-aktiv-grotesk{font-family:"aktiv-grotesk",sans-serif;}
	</style>

<link rel="stylesheet" href="https://use.typekit.net/c/838404/1w;aktiv-grotesk,2,gd7:W:n2,gd9:W:n3;myriad-pro-condensed,7cdcb44be4a7db8877ffa5c0007b8dd865b3bbc383831fe2ea177f62257a9191,ftw:W:n7/l?3bb2a6e53c9684ffdc9a98f7125b2a626768c5ac7182669e739eaa287069e0280f9778743f7f7e45b19d5029f275662d9f4d9a4fbb97a090412227d2bcc02e443ee03f55fc586f2d7a81fba99802fb19d31257342a8e44cbb83a233d3845fc4a717d104ab8e2a2a3e9bc72aec94f07425d42e16a4df9db99f0b352f706674b125b0cbd0eddd3e15ee307878fda507573d6def307fa1f9400783ca9d73bf5b242a8bcdf5fb9a341f2a251854f9468b1d6ea33a1b3efb98bf87cae50c0afd13a1e587a87ed8ce5f1d582d3c4d7b4151fc0c4b9f2ffa922ce0fa4e9" media="all">
<script type="text/javascript" src="templates/yoo_master/js/submission.js"></script>
<script src="//use.typekit.net/ayb6tno.js"></script>
<script>try{Typekit.load();}catch(e){}</script>
  <script src="/modules/mod_smartcountdown3/js/vendor/jquery-ui-easing.min.js" type="text/javascript"></script>
  <script src="/modules/mod_smartcountdown3/js/vendor/moment.min.js" type="text/javascript"></script>
  <script src="/modules/mod_smartcountdown3/js/vendor/moment-timezone.min.js" type="text/javascript"></script>
  <script src="/modules/mod_smartcountdown3/js/vendor/flipclock.min.js" type="text/javascript"></script>
  <script src="/modules/mod_smartcountdown3/helpers/plurals/plural.js" type="text/javascript"></script>
<?php JHTML::_('behavior.modal'); ?>
</head>

<body id="page" class="page <?php echo $this['config']->get('body_classes'); ?>" data-config='<?php echo $this['config']->get('body_config','{}'); ?>'>

	<?php if ($this['modules']->count('absolute')) : ?>
	<div id="absolute">
		<?php echo $this['modules']->render('absolute'); ?>
	</div>
	<?php endif; ?>
	


		<header id="header">
			


	
			
			<?php if ($this['modules']->count('logo + headerbar')) : ?>	
			<div id="headerbar" class="clearfix">
			
			<div class="wrapper clearfix">
			
			
			<?php if ($this['modules']->count('toolbar-l + toolbar-r') || $this['config']->get('date')) : ?>
			<div id="toolbar" class="clearfix">

				<?php if ($this['modules']->count('toolbar-l') || $this['config']->get('date')) : ?>
				<div class="float-left">
				
					<?php if ($this['config']->get('date')) : ?>
					<time datetime="<?php echo $this['config']->get('datetime'); ?>"><?php echo $this['config']->get('actual_date'); ?></time>
					<?php endif; ?>
				
					<?php echo $this['modules']->render('toolbar-l'); ?>
					
				</div>
				<?php endif; ?>
					
				<?php if ($this['modules']->count('toolbar-r')) : ?>
				<div class="float-right"><?php echo $this['modules']->render('toolbar-r'); ?></div>
				<?php endif; ?>
				
			</div>
			<?php endif; ?>			
			
			
				<?php if ($this['modules']->count('logo')) : ?>	
				<a id="logo" href="<?php echo $this['config']->get('site_url'); ?>"><?php echo $this['modules']->render('logo'); ?></a>
				<?php endif; ?>			
			
				<?php if ($this['modules']->count('headerbar-a')) : ?>	
				<span id="headerbar-a" class="clearfix">
					<?php echo $this['modules']->render('headerbar-a'); ?>
					
				</span>
				<?php endif; ?>					
			
				<?php if ($this['modules']->count('menu')) : ?>
				<nav id="menu"><?php echo $this['modules']->render('menu'); ?></nav>
				<?php endif; ?>

				
				<?php echo $this['modules']->render('headerbar'); ?>
			</div>	
			</div>
			<?php endif; ?>

			<?php if ($this['modules']->count('menu + search')) : ?>
			<div id="menubar" class="clearfix">
				


				<?php if ($this['modules']->count('search')) : ?>
				<div id="search"><?php echo $this['modules']->render('search'); ?></div>
				<?php endif; ?>
				
			</div>
			<?php endif; ?>
		
			<?php if ($this['modules']->count('banner')) : ?>
			<div id="banner"><?php echo $this['modules']->render('banner'); ?></div>
			<?php endif; ?>
		
			<?php if ($this['modules']->count('top-1')) : ?>
			<div class="wrapper clearfix">
				<div id="top-1"><?php echo $this['modules']->render('top-1'); ?></div>
			</div>
			<?php endif; ?>		
			
		</header>

	<div class="wrapper clearfix">	
		
		<?php if ($this['modules']->count('top-a')) : ?>
		<section id="top-a" class="grid-block"><?php echo $this['modules']->render('top-a', array('layout'=>$this['config']->get('top-a'))); ?></section>
		<?php endif; ?>
		
		<?php if ($this['modules']->count('top-b')) : ?>
		<section id="top-b" class="grid-block"><?php echo $this['modules']->render('top-b', array('layout'=>$this['config']->get('top-b'))); ?></section>
		<?php endif; ?>
		
		<?php if ($this['modules']->count('innertop + innerbottom + sidebar-a + sidebar-b') || $this['config']->get('system_output')) : ?>
		<div id="main" class="grid-block">


		
			<div id="maininner" class="grid-box">

				<?php if ($this['modules']->count('innertop')) : ?>
				<section id="innertop" class="grid-block"><?php echo $this['modules']->render('innertop', array('layout'=>$this['config']->get('innertop'))); ?></section>
				<?php endif; ?>

				<?php if ($this['modules']->count('videos')) : ?>
				<section id="videos" class="grid-block"><?php echo $this['modules']->render('videos', array('layout'=>$this['config']->get('videos'))); ?></section>
				<?php endif; ?>				
				
				<?php if ($this['modules']->count('breadcrumbs')) : ?>
				<section id="breadcrumbs"><?php echo $this['modules']->render('breadcrumbs'); ?></section>
				<?php endif; ?>

				<?php if ($this['config']->get('system_output')) : ?>
				<section id="content" class="grid-block"><?php echo $this['template']->render('content'); ?></section>
				<?php endif; ?>

				<?php if ($this['modules']->count('innerbottom')) : ?>
				<section id="innerbottom" class="grid-block"><?php echo $this['modules']->render('innerbottom', array('layout'=>$this['config']->get('innerbottom'))); ?></section>
				<?php endif; ?>

			</div>
			<!-- maininner end -->
			

						<?php if ($this['modules']->count('sidebar-a')) : ?>
			<aside id="sidebar-a" class="grid-box"><?php echo $this['modules']->render('sidebar-a', array('layout'=>'stack')); ?></aside>
			<?php endif; ?>
			<?php if ($this['modules']->count('sidebar-b')) : ?>
			<aside id="sidebar-b" class="grid-box"><?php echo $this['modules']->render('sidebar-b', array('layout'=>'stack')); ?></aside>
			<?php endif; ?>

		</div>
		<?php endif; ?>
		<!-- main end -->

		<?php if ($this['modules']->count('bottom-a')) : ?>
		<section id="bottom-a" class="grid-block"><?php echo $this['modules']->render('bottom-a', array('layout'=>$this['config']->get('bottom-a'))); ?></section>
		<?php endif; ?>
		

		
		<?php if ($this['modules']->count('footer + debug + copyright') || $this['config']->get('warp_branding') || $this['config']->get('totop_scroller')) : ?>
		<footer id="footer" class="clearfix">

			<?php if ($this['modules']->count('bottom-b')) : ?>
			<section id="bottom-b" class="grid-block"><?php echo $this['modules']->render('bottom-b', array('layout'=>$this['config']->get('bottom-b'))); ?></section>
			<?php endif; ?>		
		
			<?php if ($this['config']->get('totop_scroller')) : ?>
			<a id="totop-scroller" href="#page"></a>
			<?php endif; ?>

			<?php
				echo $this['modules']->render('footer');
				$this->output('warp_branding');
				echo $this['modules']->render('debug');
			?>

			<?php echo $this->render('footer'); ?>
			
			<?php echo $this['modules']->render('copyright'); ?>
			
		</footer>
		<?php endif; ?>

	</div>
	
	<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

	ga('create', 'UA-45165926-1', 'gwcpgh.org');
	ga('send', 'pageview');

	</script>
	
</body>
</html>