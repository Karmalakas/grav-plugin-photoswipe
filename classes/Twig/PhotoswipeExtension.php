<?php

namespace Grav\Plugin\Photoswipe\Twig;

use Grav\Common\Config\Config;
use \Grav\Common\Grav;
use Grav\Common\Page\Page;
use Grav\Common\Twig\Twig;
use Twig_SimpleFunction;

class PhotoswipeExtension extends \Twig_Extension
{
    /**
     * @var Grav
     */
    protected $grav;

    /**
     * @var Config
     */
    protected $config;

    /**
     * SwiperJSExtension constructor
     */
    public function __construct()
    {
        $this->grav   = Grav::instance();
        $this->config = $this->grav['config'];
    }

    /**
     * Returns extension name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'PhotoswipeExtension';
    }

    /**
     * @return Twig_SimpleFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new Twig_SimpleFunction('photoswipe', [$this, 'photoswipe']),
        ];
    }

    /**
     * @param string     $id
     * @param string     $slides_selector
     * @param array|null $options
     *
     * @return void
     */
    public function photoswipe(string $id, string $slides_selector = 'figure', ?array $options = null)
    {
        $defaults = $this->config->get('plugins.photoswipe', []);

        if ($defaults['enabled'] === false) {
            return;
        }

        /** @var Page $page */
        $page    = $this->grav->get('page');
        $options = array_merge($defaults['options'], $options ?? []);

        $options['errorMsg'] = sprintf(
            '<div class="pswp__error-msg">%s</div>',
            str_replace(
                ['[', ']'],
                ['<a href="%url%" target="_blank">', '</a>'],
                $this->grav['language']->translate('PLUGIN_PHOTOSWIPE.LOADING_ERROR')
            )
        );

        $page->addContentMeta('photoswipe_holder_needed', true);
        $this->addAssets($id, $slides_selector, $options);
    }

    /**
     * @param string $id
     * @param string $slides_selector
     * @param array  $options
     *
     * @return $this
     */
    protected function addAssets(string $id, string $slides_selector, array $options): PhotoswipeExtension
    {
        if ($this->config->get('plugins.photoswipe.built_in_photoswipe_assets')) {
            $assets_path = 'plugins://photoswipe/node_modules/photoswipe/dist/';

            $this->grav['assets']->add(
                [
                    sprintf('%s%s', $assets_path, 'photoswipe.css'),
                    sprintf('%s%s', $assets_path, 'default-skin/default-skin.css'),
                    sprintf('%s%s', $assets_path, 'photoswipe.min.js'),
                    sprintf('%s%s', $assets_path, 'photoswipe-ui-default.min.js'),
                ],
                ['priority' => 50]
            );
        }

        if ($this->config->get('plugins.photoswipe.built_in_css')) {
            $this->grav['assets']->addCss('plugin://photoswipe/assets/css/photoswipe.css');
        }

        if ($this->config->get('plugins.photoswipe.built_in_js')) {
            $this->grav['assets']->addJs('plugin://photoswipe/assets/js/photoswipe.min.js');
        }

        $this->grav['assets']->addInlineJs(
            sprintf('pluginPhotoswipe().init("%s", "%s", %s);', $id, $slides_selector, json_encode($options)),
            ['group' => 'bottom', 'priority' => 35]
        );

        return $this;
    }
}
