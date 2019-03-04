<?php
/**
 * @package Module Smart Countdown 3 for Joomla! 3.0
 * @version 3.2.6
 * @author Alex Polonski
 * @copyright (C) 2012-2015 - Alex Polonski
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
// no direct access
defined( '_JEXEC' ) or die();

require_once dirname( __FILE__ ) . '/helpers/helper.php';

JHtml::_( 'jquery.framework' );

$document = JFactory::getDocument();
$document->addScript( JURI::root( true ) . '/modules/mod_smartcountdown3/js/vendor/jquery-ui-easing.min.js' );
$document->addScript( JURI::root( true ) . '/modules/mod_smartcountdown3/js/vendor/moment.min.js' );
$document->addScript( JURI::root( true ) . '/modules/mod_smartcountdown3/js/vendor/moment-timezone.min.js' );
$document->addScript( JURI::root( true ) . '/modules/mod_smartcountdown3/js/vendor/flipclock.min.js' );

$lang = JFactory::getLanguage( 'site' );
$tag = strtolower( $lang->getTag() );

// Check if language-specific plural php file exists
$file_name = 'plural_' . $tag . '.php';
if ( file_exists( dirname( __FILE__ ) . '/helpers/plurals/' . $file_name ) ) {
	// use language-specific file
	include_once dirname( __FILE__ ) . '/helpers/plurals/' . $file_name;
	
	$js_file_name = 'plural_' . $tag . '.js';
} else {
	// use default file (suitable for most languages)
	include_once dirname( __FILE__ ) . '/helpers/plurals/plural.php';
	
	$js_file_name = 'plural.js';
}
$document->addStyleSheet( JURI::root( true ) . '/modules/mod_smartcountdown3/css/smart-flipclock.css' );
$document->addStyleSheet( JURI::root( true ) . '/modules/mod_smartcountdown3/css/flipclock.css' );

// Add language strings to options (do not depend on Joomla.Jtext._())
$js_label_strings = scdSetupTranslatedPlurals();

// Add redirect confirmation hint
JText::script('MOD_SMARTCOUNTDOWN3_REDIRECT_CONFIRM_HINT', true);

$params->set( 'module_id', $module->id );
$params->set( 'label_strings', $js_label_strings );

// parse and expand params
$params = modSmartCountdown3Helper::parseCounterOptions( $params );

if ( $params === false ) {
	JError::raiseError( 500, 'Invalid Smart Countdown 3 Configuration' );
}

// layout configuration - used in module template
$params->set( 'layout_config', modSmartCountdown3Helper::getCounterLayout( $params ) );

$moduleclass_sfx = htmlspecialchars( $params->get( 'moduleclass_sfx' ) );

$options = $params->toArray();
$module_id = $params->get( 'module_id', '' );

/*
 * We need the tricky code below to load smartcoutndown-related scripts and style on
 * cached pages and in a specific scenario (at least):
 * Conditions:
 * 	1. smartcountdown module is embedded into an article on front page
 * 	2. open another page, embedded countdown is not rendered (because the article isn't there),
 * 		but smartcountdown scripts are not loaded when the page is loaded from cache.
 * At the moment we have no explanation for this issue, might be desired behavior, but for this
 * application we have to load scripts on the fly if they are not found on the page.
 */
$document->addScriptDeclaration( '
	jQuery(document).ready(function() {
		
		// A weird bug: this code is multiply duplicated in caching mode!!!
		// this is a dirty workaround...
		if(window["scd_init_called_' . $module_id . '"] === true) {
			return;
		}
		window["scd_init_called_' . $module_id . '"] = true;
		
		// Another issue. See description above.
		if(typeof scds_container === "undefined") {
			// only load scripts once
			if(!window["scd_waiting_for_scripts"]) {
		
				//console.log("Loading scripts started");
		
				var script;
        script = document.createElement("script");
        script.setAttribute("src","' . JUri::root( true ) . '/modules/mod_smartcountdown3/js/vendor/jquery-ui-easing.min.js");
        script.setAttribute("async", false);
        document.head.appendChild(script);

        script = document.createElement("script");
        script.setAttribute("src","' . JUri::root( true ) . '/modules/mod_smartcountdown3/js/vendor/moment.min.js");
        script.setAttribute("async", false);
        document.head.appendChild(script);

        script = document.createElement("script");
        script.setAttribute("src","' . JUri::root( true ) . '/modules/mod_smartcountdown3/js/vendor/moment-timezone.min.js");
        script.setAttribute("async", false);
        document.head.appendChild(script);
			
        script = document.createElement("script");
        script.setAttribute("src","' . JUri::root( true ) . '/modules/mod_smartcountdown3/js/vendor/flipclock.min.js");
        script.setAttribute("async", false);
        document.head.appendChild(script);
			
				script = document.createElement("script");
				script.setAttribute("src","' . JUri::root( true ) . '/modules/mod_smartcountdown3/helpers/plurals/' . $js_file_name . '");
				script.setAttribute("async", false);
				document.head.appendChild(script);
				
				jQuery("<link>")
	  				.appendTo("head")
	  				.attr({type : "text/css", rel : "stylesheet"})
	  				.attr("href", "' . JUri::root( true ) . '/modules/mod_smartcountdown3/css/smart-flipclock.css");

        jQuery("<link>")
            .appendTo("head")
            .attr({type : "text/css", rel : "stylesheet"})
            .attr("href", "' . JUri::root( true ) . '/modules/mod_smartcountdown3/css/flipclock.css");

		
				window["scd_waiting_for_scripts"] = true;
			} /*else {
				console.log("Already loading scripts");
			} */
			// each module instance must wait until the scripts are loaded before it can call
			// scds_container.add(). Although we explicitely set synced load and most likely
			// the scripts will be already loaded when we get to this point, we still use
			// timer loop here. Should work with async script loading also.
			window["scd_waiting_for_scripts_' . $module_id . '"] = window.setInterval(function() {
				if(typeof scds_container !== "undefined") {
					window.clearInterval(window["scd_waiting_for_scripts_' . $module_id . '"]);
					delete(window["scd_waiting_for_scripts_' . $module_id . '"]);
					scds_add_instance_' . $module_id . '();
					delete(window["scd_waiting_for_scripts"]);

					//console.log("Scripts loaded. Reported by ID " + ' . $module_id . ');
				} /*else {
					console.log("Waiting for scripts from ID " + ' . $module_id . ');
				} */
			}, 100);
		} else {
			scds_add_instance_' . $module_id . '();
		}
		// use this helper function to inject json-encoded options only once on the page.
		function scds_add_instance_' . $module_id . '() {
			scds_container.add(' . json_encode( $options ) . ');
		}
	});
' );

$head_scripts = $options['head_scripts'];
$document->addCustomTag($head_scripts);

if ( !empty( $is_admin_preview ) ) {
	// at the moment we only support default layout in preview
	require JPATH_SITE . '/modules/mod_smartcountdown3/tmpl/default.php';
} else {
	require JModuleHelper::getLayoutPath( 'mod_smartcountdown3', $params->get( 'layout', 'default' ) );
}


