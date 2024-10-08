<?php
namespace ShortPixel;
use ShortPixel\ShortpixelLogger\ShortPixelLogger as Log;

class Spai
{
		public function __construct()
		{
			 add_action('plugins_loaded', array($this, 'addHooks'));

		}

		public function addHooks()
		{
			  if (\wpSPIO()->env()->plugin_active('spai'))
				{
					 // Prevent SPAI doing its stuff to our JSON returns. 
					 $hook_upon = array('shortpixel_image_processing', 'shortpixel_ajaxRequest');
					 if (wp_doing_ajax() &&
					 		 isset($_REQUEST['action']) &&
							 in_array($_REQUEST['action'], $hook_upon)			 )
					 {
						 	$this->preventCache();
					 }
				}
		}

		public function preventCache()
		{
			  if (! defined('DONOTCDN'))
				{
					 define('DONOTCDN', true);
				}
		}
}

$s = new Spai();
