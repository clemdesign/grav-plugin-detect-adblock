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
  - Using Grav plugin [Google Naalytics](https://github.com/escopecz/grav-ganalytics) by John Linhart.
  
## Installation

For now, installation is possible only manually.

To install this plugin, just download the zip version of this repository and unzip it under `/your/site/grav/user/plugins`. Then, rename the folder to `detect-adblock`. You can find these files on [GitHub](https://github.com/clemdesign/grav-plugin-detect-adblock).

You should now have all the plugin files under

    /your/site/grav/user/plugins/detect-adblock
	
> NOTE: This plugin is a modular component for Grav which requires [Grav](http://github.com/getgrav/grav) to operate.

## Configuration

### 1. Code configuration

In your [theme](https://learn.getgrav.org/16/themes/theme-basics), you shall integrate the following snippet:

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
blockvisit:
  enabled: false        # Set to true to block visit on website if AdBlock is enabled
  idtoremove: 'body'    # DOM Id to remove when visit is blocked.
```

### 3. Overwriting the displayed message

The message displayed to user is managed by the template `partials/detect-adblock.html.twig`.  
You can overwrite this template in your theme, but be careful to:

- define the ` id="detect-adblock"` for box wrapper of your message.
- add a close button with this action ` onclick="dabHide()"`.

### 4. Google Analytics

This plugin doesn't manage the Google Analytics integration. You shall do it in your theme or using a plugin (See chapter Pre-requisites).  

Notify to Google Analytics the blocking user status work if:
- `ga` object is defined OR
- `_gap` is defined

### 5. Block visit to user

This plugin allow you to force User to disable ad blocker.  
It is an intrusive feature.

In this way, to block user to read content by Code Inspector of the navigator, this plugin allow you to delete content if ad blocker is detected.


# Contributing

If you think any implementation are just not the best, feel free to submit ideas and pull requests. All your comments and suggestion are welcome.

# Credits

This plugin is based on [Detect AdBlock](https://www.detectadblock.com/) solution.

# Licenses

Copyright (c) 2019 [clemdesign](https://github.com/clemdesign).

For use under the terms of the [MIT](https://opensource.org/licenses/mit-license.php) license.