<?php

/**
 * RESS Toolbox service
 *
 * @author André Elvan
 */
namespace Craft;


class ResstoolboxService extends BaseApplicationComponent
{
  
  var $settings = array();
  var $default_options = array(
    'maxSize' => 9999,
    'retina' => false,
    'subtract' => 0,
    'steps' => 0,
    'modifier' => 1
  );

  
	/**
	 * Gets RESS size
   * 
	 * @param $name Setting name
	 * @return mixed Setting value
	 * @author André Elvan
	*/
  public function getSize() {
    $this->settings = $this->_init_settings();
    
		$cookie_val = (!empty($_COOKIE['resolution']) ? $_COOKIE['resolution'] : null);
    if ($cookie_val!==null) {
      $t = explode(',', $cookie_val);
      $screensize = $t[0];
    } else {
      $screensize = $this->settings['resstoolboxFallbackWidth'];
    }

    return $this->settings['resstoolboxDebugMode'] ? $this->settings['resstoolboxFallbackWidth'] : $screensize;
  }
  

	/**
	 * Gets RESS density
   * 
	 * @param $name Setting name
	 * @return mixed Setting value
	 * @author André Elvan
	*/
  public function getDensity() {
    $this->settings = $this->_init_settings();
    
		$cookie_val = (!empty($_COOKIE['resolution']) ? $_COOKIE['resolution'] : null);
    if ($cookie_val!==null) {
      $t = explode(',', $cookie_val);
      $density = $t[1];
    } else {
      $density = $this->settings['resstoolboxFallbackDensity'];
    }

    return $this->settings['resstoolboxDebugMode'] ? $this->settings['resstoolboxFallbackDensity'] : $density;
  }
  

	/**
	 * Calculates sizes based on device size with modifiers
   * 
	 * @param $name Setting name
	 * @return mixed Setting value
	 * @author André Elvan
	*/
  public function calculate($params) {
    $this->settings = $this->_init_settings();
    
    $options = array_merge($this->default_options, $params);
    
		$cookie_val = (!empty($_COOKIE['resolution']) ? $_COOKIE['resolution'] : null);
    if ($cookie_val!==null) {
      $t = explode(',', $cookie_val);
      $screensize = $this->settings['resstoolboxDebugMode'] ? $this->settings['resstoolboxFallbackWidth'] : $t[0];
      $density = $this->settings['resstoolboxDebugMode'] ? $this->settings['resstoolboxFallbackDensity'] : $t[1];
    } else {
      $screensize = $this->settings['resstoolboxFallbackWidth'];;
      $density = $this->settings['resstoolboxFallbackDensity'];
    }
    
    if (is_string($options['maxSize'])) {
      if (substr($options['maxSize'], strlen($options['maxSize'])-1) == '%') {
        $maxSize = (int)$screensize * ( (int)substr($options['maxSize'], 0, strlen($options['maxSize'])-1) / 100);
      } else {
        $maxSize = (int)$options['maxSize'];
      }
    } else {
      $maxSize = (int)$options['maxSize'];
    }
    
		$out_size = min($maxSize, (int)$screensize-(int)$options['subtract']);

		if ($options['retina']==1) {
			$out_size = $out_size*(float)$density;
		}
    
    if ($options['steps']!=0) {
      $out_size = ceil($out_size/(int)$options['steps'])*(int)$options['steps'];
    }    
    
    return round($out_size * (float)$options['modifier']);
  }
  

	/**
	 * Gets a plugin setting
   * 
	 * @param $name Setting name
	 * @return mixed Setting value
	 * @author André Elvan
	*/
  public function getSetting($name) {
    $this->settings = $this->_init_settings();
    return $this->settings[$name];
  }
  

	/**
	 * Gets RESS Toolbox settings, either from saved settings or from config
   * 
	 * @return array Array containing all settings
	 * @author André Elvan
	*/
  private function _init_settings() {
    $plugin = craft()->plugins->getPlugin('resstoolbox');
    $plugin_settings = $plugin->getSettings();    
    
    $settings = array();
    $settings['resstoolboxDebugMode'] = craft()->config->get('resstoolboxDebugMode')!==null ? craft()->config->get('resstoolboxDebugMode') : $plugin_settings['resstoolboxDebugMode'];
    $settings['resstoolboxFallbackWidth'] = craft()->config->get('resstoolboxFallbackWidth')!==null ? craft()->config->get('resstoolboxFallbackWidth') : $plugin_settings['resstoolboxFallbackWidth'];
    $settings['resstoolboxFallbackDensity'] = craft()->config->get('resstoolboxFallbackDensity')!==null ? craft()->config->get('resstoolboxFallbackDensity') : $plugin_settings['resstoolboxFallbackDensity'];
    
    return $settings;
  }
  
  
}
