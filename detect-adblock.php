<?php
/**
 * Allow to detect and manage AdBlock
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


use Grav\Common\Page\Interfaces\PageInterface;
use Grav\Common\Plugin;


/**
 * DetectAdblockPlugin.
 *
 * This plugin enables to use DetectAdblockPlugin inside a document
 * to be rendered by Grav.
 */
class DetectAdBlockPlugin extends Plugin
{

  /**
   * @var bool
   */
  protected $displayPopupMessage = false;

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

    if ($this->config->get('plugins.detect-adblock.enabled')) {
      // Non Admin operation
      if(!$this->isAdmin()) {
        $this->enable([
          'onPageInitialized' => ['onPageInitialized', -1],
          'onPageContentRaw' => ['onPageContentRaw', -1],
          'onPageContentProcessed' => ['onPageContentProcessed', 0]
        ]);
        // Admin operation
      } else {
        $this->enable([
          'onTwigSiteVariables' => ['onTwigSiteVariables', -10],
          'onAdminAfterSave' => ['onAdminAfterSave', 0],
        ]);
      }
    }
  }

  /**
   * Function called on Grav Page initialized
   */
  public function onPageInitialized()
  {
    // Add Detection JS
    $inlineJs = 'var abDetected = !(document.getElementById(\'DeTEctAdBloCK\')!==null);';

    // Add Analytics JS
    if ($this->config->get('plugins.detect-adblock.ganalytics')) {
      $inlineJs .= 'if(typeof ga !==\'undefined\'){ga(\'send\',\'event\',\'Blocking Ads\',(abDetected?\'Yes\':\'No\'),{\'nonInteraction\':1});}';
      $inlineJs .= 'else if(typeof _gaq !==\'undefined\'){_gaq.push([\'_trackEvent\',\'Blocking Ads\',(abDetected?\'Yes\':\'No\'),undefined,undefined,true]);}';
    }

    // Manage Popup Message
    if ($this->displayPopupMessage) {

      //Manage Page Filter
      $filter_items = $this->config->get('plugins.detect-adblock.popup.message.page_filter');
      $url = strtolower($this->cleanUrl($this->grav["uri"]->url()));

      $bDispMessage = false;
      if (is_array($filter_items) && (count($filter_items) > 0)) {
        if (!empty($url)) {
          //Look for url in filter list
          if (in_array($url, $filter_items)) $bDispMessage = true;
        }
      }

      if($bDispMessage) {

        $displayOnlyOneTimes = $this->config->get('plugins.detect-adblock.popup.message.displayone');
        $blockVisitEnabled = $this->config->get('plugins.detect-adblock.popup.blockvisit.enabled');
        $blockVisitId = $this->config->get('plugins.detect-adblock.popup.blockvisit.idtoremove');

        $inlineJs .= 'if(document.getElementById(\'detect-adblock-popup\')!==null){';

        //Manage display only one times
        if ($displayOnlyOneTimes && (!$blockVisitEnabled)) {
          $inlineJs .= 'if(abDetected && (dabGetCookie("detect-adblock")!="true")){document.getElementById(\'detect-adblock-popup\').style.display=\'block\';}';
        } else {
          $inlineJs .= 'if(abDetected){document.getElementById(\'detect-adblock-popup\').style.display=\'block\';}';
        }

        //Function to hide message
        if (!$blockVisitEnabled) {
          $inlineJs .= 'function dabHide(){document.getElementById(\'detect-adblock-popup\').style.display=\'none\';';
          if ($displayOnlyOneTimes) {
            $inlineJs .= 'dabSetCookie("detect-adblock","true",1)';
          }
          $inlineJs .= '}';
        } else {
          $inlineJs .= 'function dabHide(){}';
        }

        //Block Visit operation
        if ($blockVisitEnabled) {
          $inlineJs .= 'if((document.getElementById(\'' . $blockVisitId . '\')!==null) && abDetected){document.getElementById(\'' . $blockVisitId . '\').remove()}';
        }

        $inlineJs .= '}';

      }

    }

    // Manage Inside Message: Block Reading operation
    if ($this->config->get('plugins.detect-adblock.inside.blockreading.enabled')) {
      $inlineJs .= 'var dabContentBegin = document.getElementById("dab-content-begin");';
      $inlineJs .= 'if(abDetected && dabContentBegin) {';
      $inlineJs .= 'dabContentBegin.style.display=\'block\';';
      $inlineJs .= 'dabDeleteDomElement(dabContentBegin.nextElementSibling, \'dab-content-end\', false);';
      $inlineJs .= '}';

    }

    // Add CSS and JS
    $this->grav['assets']->addCss('plugin://detect-adblock/assets/css/detect-adblock.css');
    $this->grav['assets']->add('plugin://detect-adblock/assets/js/detect-adblock.js', null, true, null, 'bottom');
    $this->grav['assets']->addInlineJs($inlineJs, null, 'bottom');
  }

  /**
   * Add current directory to twig lookup paths.
   */
  public function onTwigTemplatePaths()
  {
    $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
  }

  /**
   * Add content after page content was read into the system.
   */
  public function onPageContentRaw()
  {

    // Manage Popup Message displaying
    $this->displayPopupMessage = false;
    if ($this->config->get('plugins.detect-adblock.popup.message.enabled') &&
      !$this->config->get('plugins.detect-adblock.inside.blockreading.enabled')) {
      $this->displayPopupMessage = true;
    }
    $this->grav['twig']->twig_vars['adblock_popup_displayed'] = $this->displayPopupMessage;

    // Read message from data caching
    $locator = $this->grav['locator'];
    $lang = $this->grav['language']->getLanguage();

    // Popup Message
    if($this->displayPopupMessage) {
      $message = null;
      $path = $locator->findResource('user://data/detect-adblock/popup-message-content.' . $lang . '.md');
      if(!$path) {
        $path = $locator->findResource('user://data/detect-adblock/popup-message-content.all.md');
      }
      if($path) {
        $message = file_get_contents($path);
      }
      if(!$message){
        $message = 'Bad configuration of Message to Display in Plugin parameters.';
      }

      $this->grav['twig']->twig_vars['adblock_popup_message_content'] = $message;

    } else {
      // Inside Message
      $message = null;
      $path = $locator->findResource('user://data/detect-adblock/inside-message-content.' . $lang . '.md');
      if(!$path) {
        $path = $locator->findResource('user://data/detect-adblock/inside-message-content.all.md');
      }
      if($path) {
        $message = file_get_contents($path);
      }
      if(!$message){
        $message = 'Bad configuration of Message to Display in Plugin parameters.';
      }

      $this->grav['twig']->twig_vars['adblock_inside_message_content'] = $message;
    }
  }

  /**
   * Search for DAB tags in page content and replace it by message.
   */
  public function onPageContentProcessed(){
    if ($this->config->get('plugins.detect-adblock.inside.blockreading.enabled')) {
      // Get content of template
      $pageContent = $this->grav['twig']->processTemplate('partials/detect-adblock-inside.html.twig');

      // Search for tags and replace it
      $content = $this->grav['page']->getRawContent();
      $content = preg_replace("#<([a-z]{1,5})>---dab---</([a-z]{1,5})>#i", '<div id="dab-content-begin" style="display:none;">' . $pageContent . '</div>', $content);
      $content = preg_replace("#<([a-z]{1,5})>---/dab---</([a-z]{1,5})>#i", '<div id="dab-content-end"></div>', $content);
      $this->grav['page']->setRawContent($content);
    }
  }

  /**
   * Manage Admin operation: Add DAB button in admin editor
   */
  public function onTwigSiteVariables(){
    // Add parameter to add button
    if($this->config->get('plugins.detect-adblock.inside.blockreading.add_editor_button')) {
      $this->grav['assets']->add('plugin://detect-adblock/admin/editor-button/js/button.js');
    }
  }

  /**
   * Save caching data files
   * @param $event \RocketTheme\Toolbox\Event\Event
   */
  public function onAdminAfterSave($event){
    /** @var PageInterface $obj */
    $obj = $event->offsetGet('object');

    // Save data caching only on detect AdBlock plugin saving event
    if(($obj !== null) && ($obj->file()->basename() == "detect-adblock")){

      // Create caching files directory if not created
      $locator = $this->grav['locator'];
      if(!($basePath = $locator->findResource('user://data/detect-adblock'))){
        $basePath = $locator->findResource('user://data') . DS . 'detect-adblock';
        mkdir($basePath, 0775, true);
      }

      /**
       * Save POPUP message content in data files
       */
      $message_popup_raw = $obj->file()->content()['popup']['message']['content'];

      //Extract message according current language
      $message_popup_array_raw = preg_split("/(.*)---([a-zA-Z]{2,3})---(.*)/i", $message_popup_raw, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

      $key='all';
      $message_popup_array = array();
      foreach($message_popup_array_raw as $value){
        $nbChar = strlen($value);
        if (($nbChar > 1) && ($nbChar <=3 )){  // If number of chars > 1 and <= 3, considered as language key
          $key = $value;
        } elseif($nbChar > 3) {               // If number of chars > 3, considered as message content
          $message_popup_array[$key] = trim($value," \t\n\r\0\x0B");
        }
      }

      foreach($message_popup_array as $key => $value) {
        file_put_contents($basePath . DS . 'popup-message-content.' . $key . '.md', $value);
      }


      /**
       * Save INSIDE message content in data files
       */
      $message_inside_raw = $obj->file()->content()['inside']['blockreading']['message'];

      //Extract message according current language
      $message_inside_array_raw = preg_split("/(.*)---([a-zA-Z]{2,3})---(.*)/i", $message_inside_raw, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

      $key='all';
      $message_inside_array = array();
      foreach($message_inside_array_raw as $value){
        $nbChar = strlen($value);
        if (($nbChar > 1) && ($nbChar <=3 )){  // If number of chars > 1 and <= 3, considered as language key
          $key = $value;
        } elseif($nbChar > 3) {               // If number of chars > 3, considered as message content
          $message_inside_array[$key] = trim($value," \t\n\r\0\x0B");
        }
      }

      foreach($message_inside_array as $key => $value) {
        file_put_contents($basePath . DS . 'inside-message-content.' . $key . '.md', $value);
      }
    }
  }

  /**
   * Get only the first part of URL
   * @param $url
   * @return bool|string
   */
  private function cleanUrl($url)
  {
    // Remove / at begining
    $url = trim($url, "/ ");
    if(strpos($url, "/") > 0) {
      $url = strstr($url, "/", true);
    }
    return $url;
  }
}