<?php
/**
 * RESS Toolbox plugin
 * 
 * 
 * @author André Elvan
 */

namespace Craft;

class ResstoolboxPlugin extends BasePlugin
{
  public function getName()
  {
      return Craft::t('RESS Toolbox');
  }

  public function getVersion()
  {
      return '0.0.1';
  }

  public function getDeveloper()
  {
      return 'André Elvan';
  }

  public function getDeveloperUrl()
  {
      return 'http://vaersaagod.no';
  }

  public function hasCpSection()
  {
      return false;
  }


  protected function defineSettings()
  {
    return array(
         'resstoolboxDebugMode' => array(AttributeType::Bool, 'default' => false),
         'resstoolboxFallbackWidth' => array(AttributeType::String, 'default' => '960'),
         'resstoolboxFallbackDensity' => array(AttributeType::String, 'default' => '1'),
    );
  }
  
  public function getSettingsHtml()
  {
    $config_settings = array();
    $config_settings['resstoolboxDebugMode'] = craft()->config->get('resstoolboxDebugMode');
    $config_settings['resstoolboxFallbackWidth'] = craft()->config->get('resstoolboxFallbackWidth');
    $config_settings['resstoolboxFallbackDensity'] = craft()->config->get('resstoolboxFallbackDensity');
    
    return craft()->templates->render('resstoolbox/settings', array(
      'settings' => $this->getSettings(),
      'config_settings' => $config_settings
    ));
  }
  
  
  
}

