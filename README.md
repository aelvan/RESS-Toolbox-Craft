Introduction
---
RESS Toolbox for [Craft](http://buildwithcraft.com/) is a plugin for implementing enabling RESS (Responsive design + Server Side components) in Craft templates.

The term RESS was [originally coined by Luke Wroblewski](http://www.lukew.com/ff/entry.asp?1392). It brings together server-side solutions and responsive design, 
with the goal of making solutions with better performance. This implementation only detects the users maximum device width and pixel density, and provides you with 
some tools to optimize your layout and assets based on this. It doesn't use any device detection libraries to provide more information about the users device, but 
this may be added in the future.
 
Screen resolution detection is done with Javascript, and on a users first request the browser will make an extra page request. Depending on your project, 
this may not be a desirable behavior. Also, if you use any kind of caching, make sure you read and understand the "A word of caution regarding caching" section. 

For support, please file a bug report or feature request in the repository on Github:    
https://github.com/aelvan/RESS-Toolbox-Craft/issues


Changelog
---
### Version 0.0.1
 - Initial Public Release. Ported from ExpressionEngine and added some additional functionality.


Installation
---
1. Download and extract the contents of the zip. Copy the /resstoolbox folder to your Craft plugin folder. 
2. Enable the RESS Toolbox plugin in Craft (Settings > Plugins).
3. Click on the RESS Toolbox plugin to configure the plugin settings, or configure it via the general config file (see "Configuration" below).
4. Add the RESS cookie to the beginning of your pages head (see "Example Usage" below). 
5. You're ready to RESS.


Configuration
---
RESS Toolbox can be configured either through the plugins settings in the control panel, or 
by adding the settings to the general config file (usually found in /craft/config/general.php). 

####Example

    'resstoolboxDebugMode' => false,
    'resstoolboxFallbackWidth' => 960,
    'resstoolboxFallbackDensity' => 1

Turning on debug mode makes RESS Toolbox use the fallback width and density instead of the values from the device.

*If for some reason the device size cannot be retrieved, either because the cookie hasn't been set or because the browser doesn't support cookies, the fallback size will be used as fallback.*


Example Usage
---
First thing you need to do is add the RESS cookie to your pages head:

####Add cookie

    <!doctype html>
    <head>
        {{ craft.resstoolbox.cookie() | raw }}

This outputs a script tag that tests for cookie support, and sets a cookie with the users device width/height and pixel density. 
The first time the cookie is created, the page is reloaded so Craft can return the most optimal version of the page to the user.
It's recommended to put the cookie at the top of your head to minimize the amount of data the browser requests on the first request.

You now have access to the following template variables:

####craft.resstoolbox.size
Returns the devices detected size (width or height, depending on which is the biggest one).

This examples includes some HTML into the template only if the size is bigger than 700px:

    {% if craft.resstoolbox.size>700 %}
        <div class="desktop-side-column">
             ... content that you only want to show on larger screen sizes ...
        </div>
    {% endif %}

####craft.resstoolbox.density
Returns the deviced detected pixel density (ie. "1" for an old-school screen, "2" for Apple iPhone retina)

####craft.resstoolbox.calculate({ ... })
The main utility function for calculating optimal sizes (normally for images) depending on detected size. It takes one parameter, an object with the following keys:

**maxSize:** The absolute maximum size the function should return. Can be a number of pixels, or a percentage of the detected size.   
**retina:** Whether or not pixel density should be taken into account.  
**subtract:** Number of pixels to subtract from the device size. Useful for instance if a layout always has a minimum margin.  
**steps:** Specifies the increments in which the size should be returned. Useful to reduce the number of generated assets on server. Always rounds up.  
**modifier:** The modifier is multiplied with the calculated size. Useful for calculating a size relative to another, for instance when calculating the height of an image when you want a specific aspect ratio.  

If you have a layout with a full-width image, and want to create an optimal image for any size, you could do:
 
    {% set myImage = entry.myImage.first() %}
    {% set imgWidth = craft.resstoolbox.calculate() %}

    {% set myImageTransform = {
        width: imgWidth
    } %}
    
    <img src="{{ myImage.getUrl(myImageTransform) }}"> 

This would return an image with width 2560px on my big, external screen, and 568px on my iPhone 5.

If you make the assumption that noone with as large a screen as mine has the browser in fullscreen, or just wants to keep images within a sensible size, you could do:
 
    {% set imgWidth = craft.resstoolbox.calculate({ maxSize: 1800 }) %}

This would return 1800px on my big, external screen, and 568px on my iPhone 5.

If you want to enable retina support:

    {% set imgWidth = craft.resstoolbox.calculate({ maxSize: 1800, retina: true }) %}

This would return 1800px on my big, external, non-retina screen, and 1136px on my iPhone 5. If my screen was retina, it'd return 3600px on it. 

*More examples below.*

####craft.resstoolbox.isDebugMode
Returns the configuration setting with the same name (boolean).

####craft.resstoolbox.fallbackWidth
Returns the configuration setting with the same name.

####craft.resstoolbox.fallbackDensity
Returns the configuration setting with the same name.


More examples
---
I want an image that's a maximum of 1600px wide, and I want it to always be in 16:9 aspect ratio:
 
    {% set myImage = entry.myImage.first() %}
    {% set imgWidth = craft.resstoolbox.calculate({ maxSize: 1600 }) %}
    {% set imgHeight = craft.resstoolbox.calculate({ maxSize: 1600, modifier: (9/16) }) %}

    {% set myImageTransform = {
        width: imgWidth,
        width: imgHeight,
        mode: 'crop',
        position: 'center-center'
    } %}
    
    <img src="{{ myImage.getUrl(myImageTransform) }}"> 

I want an image with a maximum width of 50% of the devices width, always have at lease 40px margins, and I want it to always be twice as tall as the width:
 
    {% set myImage = entry.myImage.first() %}
    {% set imgWidth = craft.resstoolbox.calculate({ maxSize: '50%', subtract: 40 }) %}
    {% set imgHeight = craft.resstoolbox.calculate({ maxSize: '50%', subtract: 40, modifier: 2 }) %}

    {% set myImageTransform = {
        width: imgWidth,
        width: imgHeight,
        mode: 'crop',
        position: 'center-center'
    } %}
    
    <img src="{{ myImage.getUrl(myImageTransform) }}"> 

By default, the value returned by the calculate function will be exact. This could result in alot of created images on the server, one version of 
each image/transform for each different device size. To reduce the amount, you can use the steps parameter. 

    {% set imgWidth = craft.resstoolbox.calculate({ maxSize: 1200 }) %}

This would return 1200px on my big, external screen, 568px on my iPhone 5 and 480px on an iPhone 4.

    {% set imgWidth = craft.resstoolbox.calculate({ maxSize: 1200, steps: 200 }) %}

This would return 1200px on my big, external screen, 600px on my iPhone 5 and 600px on an iPhone 4.


A word of caution regarding caching
---
RESS is a rather complex thing to get right. Pair it with caching, and there are landmines to be stepped on. The most important thing is to make sure the cookie 
tag is never, ever cached. Since it does a Javascript reload, caching this could result in an infinite loop which could take down a server completely 
(I've done it, so I know :)). This is hard to detect in development because it only occurs if the cache is invalid on the request where the cookie is set. So, just 
make sure you don't cache the cookie.
 
Also, if you cache parts of the page that contains a tranformed asset, or a section of HTML that is included only on some device-sizes, make sure the cached content 
is only served to the appropriate sizes. If not, you'd risk that the cache was created by a user on a mobile device, and then served to desktop devices.

