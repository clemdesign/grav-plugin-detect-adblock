<?php
/**
 * Allow to control adblock
 * Date: 12/10/2019
 * Time: 11:34
 *
 * @doc         https://www.detectadblock.com/
 *
 * @author      clemdesign <contact@clemdesign.fr>
 * @copyright   2019, clemdesign
 * @license     http://opensource.org/licenses/MIT
 */


namespace Grav\Plugin;


use Grav\Common\Plugin;
use Grav\Common\Page\Page;
use Grav\Common\Twig\Twig;
use RocketTheme\Toolbox\Event\Event;


/**
 * AdblockControlPlugin.
 *
 * This plugin enables to use AdblockControlPlugin inside a document
 * to be rendered by Grav.
 */
class AdblockControlPlugin extends Plugin
{

  /**
   * @return array
   *
   * The getSubscribedEvents() gives the core a list of events
   *     that the plugin wants to listen to. The key of each
   *     array section is the event that the plugin listens to
   *     and the value (in the form of an array) contains the
   *     callable (or function) as well as the priority. The
   *     higher the number the higher the priority.
   */
  public static function getSubscribedEvents()
  {

    return [
      'onPluginsInitialized' => ['onPluginsInitialized', 0]
    ];
  }

  /**
   * Initialize the plugin
   */
  public function onPluginsInitialized()
  {

    if (!$this->isAdmin() && $this->config->get('plugins.adblock-control.enabled')) {
      $this->enable([
        'onPageInitialized'     => ['onPageInitialized', 0]
      ]);
    }
  }

  /**
   * Function called on Grav Page initialized
   */
  public function onPageInitialized(Event $e)
  {
    $this->grav['assets']->add('plugin://adblock-control/assets/js/ads.js', null, true, null, 'bottom');
    $this->grav['assets']->addInlineJs('
if(document.getElementById(\'AdBloCKcoNTRol\')){
  alert(\'Blocking Ads: No\');
} else {
  alert(\'Blocking Ads: Yes\');
}
', null, 'bottom');
  }
}