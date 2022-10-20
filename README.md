# Photoswipe Plugin

The **Photoswipe** Plugin is an extension for [Grav CMS](http://github.com/getgrav/grav). Add Photoswipe gallery to your pages

## Configuration

Before configuring this plugin, you should copy the `user/plugins/photoswipe/photoswipe.yaml` to `user/config/plugins/photoswipe.yaml` and only edit that copy.

Here is the default configuration of available options:

```yaml
enabled: true
built_in_photoswipe_assets: true
built_in_css: true
built_in_js: true
options:

  # UI
  closeEl: true
  captionEl: true
  fullscreenEl: true
  zoomEl: true
  shareEl: true
  counterEl: true
  arrowEl: true
  preloaderEl: true
  tapToClose: false
  tapToToggleControls: true
  clickToCloseNonZoomable: true
  indexIndicatorSep: ' / '

  # Core
  showHideOpacity: false
  bgOpacity: 1
  spacing: 0.12
  allowPanToNext: true
  maxSpreadZoom: 2
  loop: true
  pinchToClose: true
  closeOnScroll: true
  closeOnVerticalDrag: true
  escKey: true
  arrowKeys: true
  history: true

  # Advanced
  centerThumbPosition: false
```

All options are the defaults of original [Photoswipe gallery](https://github.com/dimsemenov/PhotoSwipe) (which descriptions you can [find here](https://photoswipe.com/documentation/options.html)) except one:
- `centerThumbPosition` - if before animation starts or after it ends you see thumbnail position not matching the image, or your thumbnail doesn't match full image ratio, you might want to try enabling this option. It vertically centers animated thumbnail according to image holder element. If you check [Getting started section](https://photoswipe.com/documentation/getting-started.html) example of `getThumbBoundsFn` option, you will see how `y` is calculated. Enabling `centerThumbPosition` in the plugin does more calculations depending on holder and image height - not only thumb height and scroll offset.

Note that if you use the Admin Plugin, a file with your configuration named _photoswipe.yaml_ will be saved in the `user/config/plugins/` - folder once the configuration is saved in the Admin.

## Usage

In your template you need to have some HTML with at least main holder ID and plugin call via Twig function. Eg.:
```twig
{{ photoswipe('photoswipe-gallery-id', 'a') }}

<div id="photoswipe-gallery-id">
    {% for item in page.media.images %}
        <a href="{{ item.url|e }}" class="gallery-item" data-size="{{ item.width ~ 'x' ~ item.height }}">
            <img src="{{ item.cropResize(500, 250).url|e }}" alt=""/>
        </a>
    {% endfor %}
</div>
```

> **NOTE:** Every item, defined by item selector, must have a `href` and `data-size` attributes. `href` must hold a link to full size image and `data-size` is used by Photoswipe to know full size image dimensions

Every item also may have `data-caption` attribute or, if you use `<figure></figure>` markdown, you can have `<figcaption>` in it. It doesn't matter where you include `{{ photoswipe('id', 'a') }}` as everything is rendered at the bottom of the page source, but you should have one function call per gallery.

Every page can override config options. You can pass theme as a third parameter to Twig function, eg.:
```twig
{{ photoswipe('id', 'a', page.header.photoswipe.options) }}
```

## Installation

Installing the Photoswipe plugin can be done in one of three ways: The GPM (Grav Package Manager) installation method lets you quickly install the plugin with a simple terminal command, the manual method lets you do so via a zip file, and the admin method lets you do so via the Admin Plugin.

> **NB:** Currently, only manual installation available. Maybe plugin will be added to GPM in the future, but not yet.

### GPM Installation (Preferred)

To install the plugin via the [GPM](http://learn.getgrav.org/advanced/grav-gpm), through your system's terminal (also called the command line), navigate to the root of your Grav-installation, and enter:

    bin/gpm install photoswipe

This will install the Photoswipe plugin into your `/user/plugins`-directory within Grav. Its files can be found under `/your/site/grav/user/plugins/photoswipe`.

### Manual Installation

To install the plugin manually, download the zip-version of this repository and unzip it under `/your/site/grav/user/plugins`. Then rename the folder to `photoswipe`. You can find these files on [GitHub](https://github.com/karmalakas/grav-plugin-photoswipe) or via [GetGrav.org](http://getgrav.org/downloads/plugins#extras).

You should now have all the plugin files under

    /your/site/grav/user/plugins/photoswipe

### Admin Plugin

If you use the Admin Plugin, you can install the plugin directly by browsing the `Plugins`-menu and clicking on the `Add` button.

## Credits

This plugin uses original [Photoswipe gallery](https://github.com/dimsemenov/PhotoSwipe) by @dimsemenov
