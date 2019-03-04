<?php
/**
 * @package Module Smart Countdown 3 for Joomla! 3.0
 * @version 3.2.4
 * @author Alex Polonski
 * @copyright (C) 2012-2015 - Alex Polonski
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 **/
// no direct access
defined( '_JEXEC' ) or die();

define( 'SCD_BASE_FONT_SIZE', 12 );
abstract class modSmartCountdown3Helper {
	public static $assets = array (
			'years',
			'months',
			'weeks',
			'days',
			'hours',
			'minutes',
			'seconds' 
	);
	
	// Responsive Classes actions
	private static $layout_tpls = array (
			'labels_pos' => array (
					'row' => array (
							array (
									'selector' => '.scd-label',
									'remove' => 'scd-label-col scd-label-row',
									'add' => 'scd-label-row' 
							),
							array (
									'selector' => '.scd-digits',
									'remove' => 'scd-digits-col scd-digits-row',
									'add' => 'scd-digits-row' 
							) 
					),
					'col' => array (
							array (
									'selector' => '.scd-label',
									'remove' => 'scd-label-col scd-label-row',
									'add' => 'scd-label-col' 
							),
							array (
									'selector' => '.scd-digits',
									'remove' => 'scd-digits-col scd-digits-row',
									'add' => 'scd-digits-col' 
							) 
					) 
			),
			'layout' => array (
					'vert' => array (
							'selector' => '.scd-unit',
							'remove' => 'scd-unit-vert scd-unit-horz',
							'add' => 'scd-unit-vert clearfix' 
					),
					'horz' => array (
							'selector' => '.scd-unit',
							'remove' => 'scd-unit-vert scd-unit-horz clearfix',
							'add' => 'scd-unit-horz' 
					) 
			),
			'event_text_pos' => array (
					'vert' => array (
							array (
									'selector' => '.scd-title',
									'remove' => 'scd-title-col scd-title-row clearfix',
									'add' => 'scd-title-col clearfix' 
							),
							array (
									'selector' => '.scd-counter',
									'remove' => 'scd-counter-col scd-counter-row clearfix',
									'add' => 'scd-counter-col clearfix' 
							) 
					),
					'horz' => array (
							array (
									'selector' => '.scd-title',
									'remove' => 'scd-title-col scd-title-row clearfix',
									'add' => 'scd-title-row' 
							),
							array (
									'selector' => '.scd-counter',
									'remove' => 'scd-counter-col scd-counter-row clearfix',
									'add' => 'scd-counter-row' 
							) 
					) 
			) 
	);
	public static function parseCounterOptions( $params ) {
		// read layout options
		$file_name = JPATH_SITE . '/modules/mod_smartcountdown3/layouts/' . $params->get( 'layout_preset', 'auto.xml' );
		if ( file_exists( $file_name ) ) {
			$xml = file_get_contents( $file_name );
		}
		if ( empty( $xml ) ) {
			// usually means a missing layout preset file
			// this is a fatal error, module will raise 500 error
			return false;
		}
		
		// now XML document should be valid
		libxml_use_internal_errors( true );
		
		$xml = simplexml_load_string( $xml );
		
		foreach ( libxml_get_errors() as $error ) {
			// log errors here...
		}
		
		// counter units padding settings
		$paddings = array ();
		foreach ( $xml->paddings->children() as $padding ) {
			$padding = $padding->getName();
			$paddings[$padding] = ( int ) $xml->paddings->$padding;
		}
		$params->set( 'paddings', $paddings );
		
		// process overridable layout settings
		if( $params->get( 'counter_layout' ) == '' ) {
			$params->set( 'counter_layout', ( string ) $xml->layout );
		}
		if( $params->get( 'event_text_pos' ) == '' ) {
			$params->set( 'event_text_pos', ( string ) $xml->event_text_pos );
		}
		if( $params->get( 'labels_pos' ) == '' ) {
			$params->set( 'labels_pos', ( string ) $xml->labels_pos );
		}
		if( $params->get( 'labels_vert_align' ) == '' ) {
			$params->set( 'labels_vert_align', ( string ) $xml->labels_vert_align );
		}
		if( $params->get( 'hide_highest_zeros' ) == '' ) {
			$params->set( 'hide_highest_zeros', ( string ) $xml->hide_highest_zeros );
		}
		if( $params->get( 'allow_all_zeros' ) == '' ) {
			$params->set( 'allow_all_zeros', ( string ) $xml->allow_all_zeros );
		}
		
		$responsive = array ();
		$is_responsive = $xml->responsive->attributes();
		
		// check if responsive  behavior was disabled in layout overrides
		if( $params->get( 'disable_responsive' ) == 1 ) {
			$is_responsive = false;
		} else {
			$is_responsive = ( int ) $is_responsive['value'];
		}
		
		if ( $is_responsive ) {
			// screen sizes
			foreach ( $xml->responsive->children() as $scale ) {
				
				$attrs = array ();
				foreach ( $scale->attributes() as $k => $v ) {
					$attrs[$k] = ( string ) $v;
				}
				
				$classes = array ();
				foreach ( $scale->children() as $layout ) {
					$name = $layout->getName();
					$value = ( string ) $layout;
					
					if ( isset( self::$layout_tpls[$name] ) && isset( self::$layout_tpls[$name][$value] ) ) {
						$classes[] = self::$layout_tpls[$name][$value];
					}
				}
				
				$responsive[] = array (
						'scale' => $attrs['value'],
						'alt_classes' => $classes 
				);
			}
			
			// add default scale 1.0 setting
			$classes = array ();
			$classes[] = self::$layout_tpls['layout'][$params->get( 'counter_layout', 'horz' )];
			$labels_pos = $params->get( 'labels_pos', 'right' );
			$labels_pos = $labels_pos == 'right' || $labels_pos == 'left' ? 'row' : 'col';
			$classes[] = self::$layout_tpls['labels_pos'][$labels_pos];
			$classes[] = self::$layout_tpls['event_text_pos'][$params->get( 'event_text_pos', 'vert' )];
			
			$responsive[] = array (
					'scale' => 1.0,
					'alt_classes' => $classes 
			);
		}
		
		$params->set( 'responsive', $responsive );
		$params->set( 'base_font_size', SCD_BASE_FONT_SIZE );
		
		// configure displayed units
		$all_units = array (
				'years' => 0,
				'months' => 0,
				'weeks' => 0,
				'days' => 0,
				'hours' => 0,
				'minutes' => 0,
				'seconds' => 0 
		);
		$units = $params->get( 'units', array () );
		foreach ( $units as $unit ) {
			$all_units[$unit] = 1;
		}
		
		$hide_lower_units = array ();
		if ( $params->get( 'allow_all_zeros', 0 ) == 0 ) {
			foreach ( array_reverse( $all_units, true ) as $asset => $display ) {
				if ( $display == 0 ) {
					$hide_lower_units[] = $asset;
					$all_units[$asset] = 1;
				} else {
					// first unit set as displayed, break the loop
					break;
				}
			}
		}
		$params->set( 'hide_lower_units', $hide_lower_units );
		$params->set( 'units', $all_units );
		
		$counter_modes = $params->get( 'counter_modes', '-1:-1' );
		list ( $countdown_limit, $countup_limit ) = explode( ':', $counter_modes );
		
		$params->set( 'countdown_limit', $countdown_limit );
		$params->set( 'countup_limit', $countup_limit );
		
		$event_goto_url = $params->get( 'event_goto_url', '' );
		$event_goto_menu = empty( $event_goto_url ) ? $params->get( 'event_goto_menu', '' ) : '';
		
		$event_goto_link = self::getRedirectURL($event_goto_url, $event_goto_menu);
		
		$params->set( 'redirect_url', $event_goto_link );
		
		if ( $params->get( 'counter_clickable', 0 ) > 0 ) {
			$params->set( 'click_url', $event_goto_link );
		} else {
			$params->set( 'click_url', '' );
		}
		
		$params->set( 'id', 'smartcountdown-' . $params->get( 'module_id', '' ) );
		$params->set( 'ajaxurl', JURI::root( true ) . '/index.php' );
		
		// read animations options
		
		$animations = self::getAnimations( $params );
		if ( $animations === false ) {
			return false;
		}
		
		$params->set( 'animations', $animations );
		
		return $params;
	}
	public static function getRedirectURL($event_goto_url, $event_goto_menu) {
		if ( empty( $event_goto_url ) && empty( $event_goto_menu ) ) {
			$event_goto_link = '';
		} elseif ( !empty( $event_goto_url ) ) {
			// workaround for duplicated 'http://' prefix prepended to url
			// for relative urls (e.g. index.php)
			if ( strripos( $event_goto_url, 'http' ) !== 0 ) {
				$event_goto_url = substr( $event_goto_url, stripos( $event_goto_url, '://' ) + 3 );
			}
			$event_goto_link = $event_goto_url;
				
			// ignore empty url
			if ( substr( $event_goto_link, -3 ) == '://' ) {
				$event_goto_link = '';
			}
		} else {
			$menu = JFactory::getApplication()->getMenu();
			$item = $menu->getItem( $event_goto_menu );
				
			// we have to check if we have a valid item here: missing item
			// can be caused by menu item unpublished state
			if ( !empty( $item ) ) {
				$router = JSite::getRouter();
				if ( $router->getMode() == JROUTER_MODE_SEF ) {
					$event_goto_link = 'index.php?Itemid=' . $event_goto_menu;
				} else {
					$event_goto_link = $item->link . '&Itemid=' . $event_goto_menu;
				}
				$event_goto_link .= '&lang=' . substr( $item->language, 0, 2 );
				if ( strcasecmp( substr( $event_goto_link, 0, 4 ), 'http' ) && ( strpos( $event_goto_link, 'index.php?' ) !== false ) ) {
					$event_goto_link = JRoute::_( $event_goto_link, true, $item->params->get( 'secure' ) );
				} else {
					$event_goto_link = JRoute::_( $event_goto_link );
				}
				$event_goto_link = JURI::getInstance()->toString( array (
						'scheme',
						'host'
				) ) . $event_goto_link;
			} else {
				$event_goto_link = '';
			}
		}
		return $event_goto_link;
	}
	public static function getCounterLayout( $params ) {
		$layout = array ();
		
		$counter_layout = $params->get( 'counter_layout' );
		
		$title_before_size = $params->get( 'title_before_size', 16 ) / SCD_BASE_FONT_SIZE;
		$title_after_size = $params->get( 'title_after_size', 14 ) / SCD_BASE_FONT_SIZE;
		$labels_size = $params->get( 'labels_size', 10 ) / SCD_BASE_FONT_SIZE;
		
		$layout['event_text_pos'] = $params->get( 'event_text_pos' );
		$layout['labels_pos'] = $params->get( 'labels_pos' );
		
		$layout['title_before_style'] = 'font-size:' . $title_before_size . 'em;' . $params->get( 'title_before_style', '' );
		$layout['title_after_style'] = 'font-size:' . $title_after_size . 'em;' . $params->get( 'title_after_style', '' );
		
		$layout['digits_style'] = $params->get( 'digits_style', '' );
		$layout['labels_style'] = 'font-size:' . $labels_size . 'em;' . $params->get( 'labels_style', '' );
		
		$layout['title_before_style'] = empty( $layout['title_before_style'] ) ? '' : ' style="' . $layout['title_before_style'] . '"';
		$layout['title_after_style'] = empty( $layout['title_after_style'] ) ? '' : ' style="' . $layout['title_after_style'] . '"';
		
		$layout['digits_style'] = empty( $layout['digits_style'] ) ? '' : ' style="' . $layout['digits_style'] . '"';
		$layout['labels_style'] = empty( $layout['labels_style'] ) ? '' : ' style="' . $layout['labels_style'] . '"';
		
		$layout['module_style'] = $params->get( 'module_style', '' );
		if ( $params->get( 'horizontally_center', 1 ) == 1 ) {
			// if auto-center is set in options we add the rule to module container
			$layout['module_style'] .= 'text-align:center;';
		}
		$layout['module_style'] = empty( $layout['module_style'] ) ? '' : ' style="' . $layout['module_style'] . '"';
		
		switch ( $layout['labels_pos'] ) {
			case 'left' :
			case 'right' :
				$layout['labels_class'] = 'scd-label scd-label-row';
				$layout['digits_class'] = 'scd-digits scd-digits-row';
				break;
			case 'top' :
			case 'bottom' :
			default :
				$layout['labels_class'] = 'scd-label scd-label-col';
				$layout['digits_class'] = 'scd-digits scd-digits-col';
				break;
		}
		switch ( $layout['event_text_pos'] ) {
			case 'horz' :
				$layout['text_class'] = 'scd-title scd-title-row';
				$layout['counter_class'] = 'scd-counter scd-counter-row scd-counter-' . $counter_layout;
				break;
			case 'vert' :
			default :
				$layout['text_class'] = 'scd-title scd-title-col clearfix';
				$layout['counter_class'] = 'scd-counter scd-counter-col clearfix';
		}
		
		$layout['units_class'] = 'scd-unit scd-unit-' . $counter_layout;
		if ( $counter_layout == 'vert' ) {
			$layout['units_class'] .= ' clearfix';
		}
		
		return $layout;
	}
	public static function getAnimations( $params ) {
		$file_name = JPATH_SITE . '/modules/mod_smartcountdown3/fx/' . $params->get( 'fx_preset', 'No_FX_animation.xml' );
		
		if ( !file_exists( $file_name ) ) {
			return false;
		}
		$xml = file_get_contents( $file_name );
		
		libxml_use_internal_errors( true );
		
		$xml = simplexml_load_string( $xml );
		
		foreach ( libxml_get_errors() as $error ) {
			// log errors here...
		}
		if ( empty( $xml ) ) {
			return false;
		}
		
		$digitsConfig = array ();
		
		// global settings
		$digitsConfig['name'] = $xml['name'] ? ( string ) $xml['name'] : 'Custom';
		$digitsConfig['description'] = $xml['description'] ? ( string ) $xml['description'] : '';
		$images_folder = $xml['images_folder'] ? ( string ) $xml['images_folder'] : '';
		// compatibility with profiles for smart countdown 2.5
		if ( strpos( $images_folder, 'modules/mod_smartcountdown/images/' ) === 0 ) {
			$images_folder = substr( $images_folder, strlen( 'modules/mod_smartcountdown/images/' ) );
		}
		$digitsConfig['images_folder'] = JUri::root( true ) . '/modules/mod_smartcountdown3/images/' . $images_folder;
		
		$digitsConfig['uses_margin_values'] = false;
		
		// *** TEST ONLY - for debugging to see previous values for all digits on init
		// $digitsConfig['uses_margin_values'] = true;
		
		// get all digit scopes configurations
		foreach ( $xml->digit as $digit ) {
			
			// scope attribute may contain more than one value (comma-separated list)
			$scopes = explode( ',', ( string ) $digit['scope'] );
			
			foreach ( $scopes as $scope ) {
				// init config for all scopes in list
				$digitsConfig['digits'][$scope] = array ();
			}
			
			// Calculate digits scale. We look for height and font-size scalable styles and calculate the
			// effective scaling (basing on SCD_BASE_FONT_SIZE)
			$scale = 1; // prepare for a fallback if no scalable relevant style is found
			
			$digits_size = $params->get( 'digits_size', 24 );
			
			foreach ( $digit->styles->style as $value ) {
				$attrs = array ();
				foreach ( $value->attributes() as $k => $v ) {
					$attrs[$k] = ( string ) $v;
				}
				/*
				 * $$$ CHECK LOGIC with text based animations - EM setting and line-height:1em; (causes extra margin!)
				 */
				if ( ( $attrs['name'] == 'height' || $attrs['name'] == 'font-size' ) && !empty( $attrs['scalable'] ) ) {
					if ( $attrs['unit'] == 'px' ) {
						$scale = $digits_size / $attrs['value'];
					} elseif ( $attrs['unit'] == 'em' ) {
						$scale = ( $digits_size / SCD_BASE_FONT_SIZE ) / $attrs['value'];
					}
				}
			}
			
			// construct digit style
			$styles = array ();
			
			foreach ( $digit->styles->style as $value ) {
				$attrs = array ();
				foreach ( $value->attributes() as $k => $v ) {
					$attrs[$k] = ( string ) $v;
				}
				
				// If attribute unit is "px" we translate it to "em" using global base font size
				// setting
				if ( $attrs['unit'] == 'px' ) {
					$attrs['unit'] = 'em';
					$attrs['value'] = $attrs['value'] / SCD_BASE_FONT_SIZE;
				}
				// Scale the value if it has 'scalable' attribute set
				$result = ( !empty( $attrs['scalable'] ) ? $scale * $attrs['value'] : $attrs['value'] ) . ( !empty( $attrs['unit'] ) ? $attrs['unit'] : '' );
				
				$result = preg_replace( '#url\((\S+)\)#', 'url(' . $digitsConfig['images_folder'] . '$1)', $result );
				
				// We save styles as array, must be joined by ";" before applying directly to style attribute!
				$styles[$attrs['name']] = $result;
			}
			
			// *** old version: styles as a string
			// for digit style - if background set, prepend images_folder
			// $styles = preg_replace('#url\((\S+)\)#', 'url('.$digitsConfig['images_folder'].'$1)', $styles);
			
			foreach ( $scopes as $scope ) {
				// set styles for all scopes in list
				$digitsConfig['digits'][$scope]['style'] = $styles;
			}
			
			// get modes (down and up)
			foreach ( $digit->modes->mode as $groups ) {
				
				$attrs = $groups->attributes();
				$mode = ( string ) $attrs['name'];
				
				foreach ( $groups as $group ) {
					
					$grConfig = array ();
					
					$grAttrs = $group->attributes();
					foreach ( $grAttrs as $k => $v ) {
						$grConfig[$k] = ( string ) $v;
						if ( $k == 'transition' ) {
							$grConfig[$k] = self::translateTransitions( $grConfig[$k] );
						}
					}
					
					$grConfig['elements'] = array ();
					
					// get all elements for the group
					foreach ( $group as $element ) {
						// default values to use if attribute is missing
						$elConfig = array (
								'filename_base' => '',
								'filename_ext' => '',
								'value_type' => '' 
						);
						
						$elAttrs = $element->attributes();
						foreach ( $elAttrs as $k => $v ) {
							$elConfig[$k] = ( string ) $v;
						}
						
						if ( $elConfig['value_type'] == 'pre-prev' || $elConfig['value_type'] == 'post-next' ) {
							// working with pre-prev and post-next requires significant
							// calculation in client script, so for performance sake we set the
							// flag here, so that this calculation is performed only if needed
							$digitsConfig['uses_margin_values'] = true;
						}
						
						$elConfig['styles'] = self::getElementStyles( $element->styles, $digitsConfig['images_folder'] );
						$elConfig['tweens'] = self::getElementTweens( $element->tweens, empty( $grConfig['unit'] ) ? '%' : $grConfig['unit'] );
						
						// if a style is missing in tweens['from'] we must add it here
						$elConfig['tweens']['from'] = array_merge( $elConfig['styles'], $elConfig['tweens']['from'] );
						
						// if a tweens rule (CSS property) is missing in element's styles, existing animations profiles
						// get broken. At the moment we implement this workaround - explicitly add a style if a "tween.from"
						// property is missing. Later we can check if this can be done in client script and/or if there are
						// clear guidelines for correcting existing animation profiles
						foreach ( $elConfig['tweens']['from'] as $style => $value ) {
							if ( !isset( $elConfig['styles'][$style] ) ) {
								$elConfig['styles'][$style] = $value;
							}
						}
						$grConfig['elements'][] = $elConfig;
					}
					
					foreach ( $scopes as $scope ) {
						// set fx configuration for all scopes in list
						$digitsConfig['digits'][$scope][$mode][] = $grConfig;
					}
				}
			}
		}
		
		$digitsConfig['digits'] = self::translateScopes( $digitsConfig['digits'] );
		return $digitsConfig;
	}
	
	/**
	 * Translate old mootools easing directives to jQuery UI easing standards.
	 * When using native jQuery
	 * easing or unknown, returns $transition param without changes
	 *
	 * @param string $transition
	 *        	- sourse easing directive
	 * @return string - jQuery UI standard easing directive
	 */
	private static function translateTransitions( $transition ) {
		$parts = explode( ':', $transition );
		if ( count( $parts ) == 2 ) {
			return 'ease' . ucfirst( $parts[1] ) . ucfirst( $parts[0] );
		} else {
			return $transition;
		}
	}
	/**
	 * Support smart countdown 2 animation profiles digit scopes in smart countdown 3
	 * - scope 6 has a special meaning - lower digit of "days", "weeks", "months" and "years" units
	 *
	 * @param array $scopes
	 *        	- animation data array, possibly containig smartcountdown-2 keys (0, 1, 2, etc...)
	 * @return array of scopes with smartcountdown3 keys notation (seconds0, seconds1, minutes0, etc...)
	 */
	private static function translateScopes( $scopes ) {
		$dict = array (
				'0' => 'seconds0',
				'1' => 'seconds1',
				'2' => 'minutes0',
				'3' => 'minutes1',
				'4' => 'hours0',
				'5' => 'hours1',
				'6' => 'days0' 
		);
		
		$translated = array ();
		
		foreach ( $scopes as $scope => $data ) {
			if ( isset( $dict[$scope] ) ) {
				$translated[$dict[$scope]] = $data;
				if ( $scope == 6 ) {
					$translated['weeks0'] = $translated['months0'] = $translated['years0'] = $data;
				}
			} else {
				$translated[$scope] = $data;
			}
		}
		return $translated;
	}
	private static function getElementStyles( $styles, $images_folder ) {
		$result = array ();
		
		if ( empty( $styles ) ) {
			return $result;
		}
		
		$styles = $styles->children();
		for ( $i = 0; $count = count( $styles ), $i < $count; $i++ ) {
			$result[$styles[$i]->getName()] = trim( preg_replace( '#url\((\S+)\)#', 'url(' . $images_folder . '$1)', ( string ) $styles[$i] ) );
		}
		
		return $result;
	}
	
	/*
	 * Split tweens to "from" and "to" CSS rules. Must-have for jQuery animation
	 */
	private static function getElementTweens( $tweens, $unit ) {
		$result = array (
				'from' => array (),
				'to' => array () 
		);
		if ( empty( $tweens ) ) {
			return $result;
		}
		
		$tweens = $tweens->children();
		
		for ( $i = 0; $count = count( $tweens ), $i < $count; $i++ ) {
			$name = $tweens[$i]->getName();
			if ( !in_array( $name, array (
					'top',
					'bottom',
					'left',
					'right',
					'height',
					'width',
					'font-size' 
			) ) ) {
				// discard unit for css rules that do not accept units
				$unit = '';
			}
			$values = explode( ',', ( string ) $tweens[$i] );
			$result['from'][$name] = trim( $values[0] . $unit );
			$result['to'][$name] = trim( $values[1] . $unit );
		}
		
		return $result;
	}
}