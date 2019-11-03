# Detect AdBlock Grav Plugin

The **Detect AdBlock** plugin is for [Grav CMS](http://github.com/getgrav/grav).

## Description

This plugin allow you to:
- detect ad blocker
- send blocking user status to Google Analytics
- manage user message

This plugin works as an anti-adblock or just for statistics.

## Pre-requisites

If you want to manage Google Analytics, you shall include the snippets in your site:
  - Using your own integration (in theme or other)
  - Using Grav plugin [Google Analytics](https://github.com/escopecz/grav-ganalytics) by John Linhart.
  
## Installation

Installing the `Detect AdBlock` plugin can be done in one of two way: GPM (Grav Package Manager) installation method enables you to quickly and easily install the plugin with a simple terminal command, while the manual method enables you to do so via a zip file or GIT.

### GPM Installation (Preferred)

The simplest way to install this plugin is via the [Grav Package Manager [GPM](http://learn.getgrav.org/advanced/grav-gpm) through your system's Terminal (also called the command line). From the root of your Grav install type:

    $ bin/gpm install detect-adblock

This will install the `Detect AdBlock` plugin into your /user/plugins directory within Grav. Its files can be found under `user/plugins/detect-adblock`.

### Manual Installation (Download)

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `detect-adblock`. You can find these files on [GitHub](https://github.com/clemdesign/grav-plugin-detect-adblock).

You should now have all the plugin files under

    /your/site/grav/user/plugins/detect-adblock
	
> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) to operate.

## Configuration

### 1. Code configuration

In your [theme](https://learn.getgrav.org/16/themes/theme-basics), add the following snippet:

```twig
{% include 'partials/detect-adblock.html.twig' %}
```

> Add this snippet just after `<body>` integration, inside the tag.  
> `<body>` tag is generally defined in the `base.twig.html` template.


### 2. Parameters configuration

This plugin can be configured entirely by [Grav Admin plugin](https://github.com/getgrav/grav-plugin-admin).

Otherwise, you have the following configuration parameters:

```yaml
enabled: true           # Set to true to enable the plugin
ganalytics: true        # Set to true to manage Google Analytics tracking
message:
  enabled: true         # Set to true to display message
  displayone: true      # Set to true to display only 1 times
  page_filter: ''       # List of pages where message shall be displayed.
  content:              # Your message content in markdown format
blockvisit:
  enabled: false        # Set to true to block visit on website if AdBlock is enabled
  idtoremove: 'body'    # DOM Id to remove when visit is blocked.
```

### 3. Overwriting the message style

#### 3.1. Overwriting the template

The message displayed to user is managed by the template `partials/detect-adblock.html.twig`.  
You can overwrite this template in your theme, but be careful to:

- define the ` id="detect-adblock"` for box wrapper of your message.
- add a close button with this action ` onclick="dabHide()"`.
- add the script to include `/user/plugins/detect-adblock/assets/js/ads.js`

#### 3.2. Overwriting the css style

You have possibility to overwrite the style of messsage.
For that, you have the following CSS tag:

```css
.detect-adblock {
    /* The global message wrapper, to manage background of message. */
}
.detect-adblock .dab-message {
    /* The message container, to manage main message style. */
 }
.detect-adblock .dab-message .dab-buttons {
    /* The message buttons container. */
}
.detect-adblock .dab-message .dab-content {
    /* The message content container. */
}
```

### 4. Multi-language of message

Plugin allow you to define a multi-language message in plugin configuration page.

The displayed message use the parameter `message.content`.

To define translation of your message, split the content with the tag `---{language key}---`.

The first block is considered as default language.

For instance:

```md
Default language of your message
---fr---
Message in French
---es---
Message in Spanish
---de---
Message in German
```

### 5. Google Analytics

This plugin doesn't embed the Google Analytics library/code. You shall do it in your theme or using a plugin (See chapter Pre-requisites).  

Notify to Google Analytics the blocking user status works if:
- the parameter `ganalytics` is enabled.
- `ga` object is defined OR
- `_gap` is defined

To get statistics in Google Analytics, refer to this page:  
[https://support.google.com/analytics/answer/1033068?hl=en](https://support.google.com/analytics/answer/1033068?hl=en)

### 6. Block visit to user

This plugin allow you to force user to disable ad blocker.  
It is an intrusive feature.

In this way, to block user to read content by *Code Inspector* developer tool of the navigator, this plugin allow you to delete content if ad blocker is detected.  
You shall enable the parameter `blockvisit.enabled` and define the `blockvisit.idtoremove` for that.

# Contributing

If you think any implementation are just not the best, feel free to submit ideas and pull requests. All your comments and suggestion are welcome.

# Credits

This plugin is based on [Detect AdBlock](https://www.detectadblock.com/) solution.

# License

Copyright (c) 2019 [clemdesign](https://github.com/clemdesign).

For use under the terms of the [MIT](https://opensource.org/licenses/mit-license.php) license.