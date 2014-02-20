<?php
/**
 * RESS Toolbox variables
 *
 * @author André Elvan
 */

namespace Craft;

class ResstoolboxVariable
{
  
  public function isDebugMode() {
    return craft()->resstoolbox->getSetting('resstoolboxDebugMode');
  }
  
  public function fallbackWidth() {
    return craft()->resstoolbox->getSetting('resstoolboxFallbackWidth');
  }
  
  public function fallbackDensity() {
    return craft()->resstoolbox->getSetting('resstoolboxFallbackDensity');
  }
  
  public function cookie() {
		if(!isset($_COOKIE['resolution'])) {
			return "<script>
				// Set a cookie to test if they're enabled
				document.cookie = 'testcookie=true';
				cookiesEnabled = (document.cookie.indexOf('testcookie')!=-1)? true : false;

				document.cookie='resolution='+Math.max(screen.width,screen.height)+('devicePixelRatio' in window ? ','+devicePixelRatio : ',1')+'; path=/';
				
				// Only reload if cookies are enabled
				if (cookiesEnabled)
				{
					date = new Date();
					date.setDate(date.getDate() -1);
					// Delete test cookie
					document.cookie = 'testcookie=;expires=' + date;
					location.reload(true);
				}
			</script>";
		} else {
      /* TBD: Should the cookie be updated on every request to cope for the user moving between screens? */ 
			//return "<script>document.cookie='resolution='+Math.max(screen.width,screen.height)+('devicePixelRatio' in window ? ','+devicePixelRatio : ',1')+'; path=/';</script>";
    }
	}
  
  public function calculate($params=array()) {
    return craft()->resstoolbox->calculate($params);
  }
  
  public function size() {
    return craft()->resstoolbox->getSize();
  }
  
  public function density() {
    return craft()->resstoolbox->getDensity();
  }
  
  
}