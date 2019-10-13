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
class DetectAdBlockPlugin extends Plugin
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
      'onPluginsInitialized' => ['onPluginsInitialized', 0],
      'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0]
    ];
  }

  /**
   * Initialize the plugin
   */
  public function onPluginsInitialized()
  {

    if (!$this->isAdmin() && $this->config->get('plugins.detect-adblock.enabled')) {
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
    $this->grav['assets']->add('plugin://detect-adblock/assets/js/ads.js', null, true, null, 'bottom');

    // Add Detection JS
    $inlineJs = 'var abDetected = (document.getElementById(\'AdBloCKcoNTRol\')!==null);';

    // Add Analytics JS
    if($this->config->get('plugins.detect-adblock.ganalytics')){
      $inlineJs .= 'if(typeof ga !==\'undefined\'){ga(\'send\',\'event\',\'Blocking Ads\',abDetected,{\'nonInteraction\':1});}';
      $inlineJs .= 'else if(typeof _gaq !==\'undefined\'){_gaq.push([\'_trackEvent\',\'Blocking Ads\',abDetected,undefined,undefined,true]);}';
    }

    // Add Message
    if($this->config->get('plugins.detect-adblock.message')){
      $inlineJs .= 'if(document.getElementById(\'detect-adblock\')!==null){document.getElementById(\'detect-adblock\').style.display=\'block\';}';
      $this->grav['assets']->addCss('plugin://detect-adblock/assets/css/detect-adblock.css');
    }

    $this->grav['assets']->addInlineJs($inlineJs, null, 'bottom');
  }

  /**
   * Add current directory to twig lookup paths.
   */
  public function onTwigTemplatePaths()
  {
    $this->grav['twig']->twig_paths[] = __DIR__.'/templates';
  }
}