<?php

namespace Grav\Plugin;

use Composer\Autoload\ClassLoader;
use Grav\Common\Grav;
use Grav\Common\Page\Page;
use Grav\Common\Plugin;
use Grav\Common\Twig\Twig;
use Grav\Plugin\Photoswipe\Twig\PhotoswipeExtension;
use RocketTheme\Toolbox\Event\Event;

/**
 * Class PhotoswipePlugin
 *
 * @package Grav\Plugin
 */
class PhotoswipePlugin extends Plugin
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
    public static function getSubscribedEvents(): array
    {
        return [
            'onPluginsInitialized' => [
                ['onPluginsInitialized', 0],
            ],
        ];
    }

    /**
     * Composer autoload.
     *is
     *
     * @return ClassLoader
     */
    public function autoload(): ClassLoader
    {
        return require __DIR__ . '/vendor/autoload.php';
    }

    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized(): void
    {
        if ($this->isAdmin()) {
            $this->enable(
                [
                    'onGetPageTemplates'  => ['onGetPageTemplates', 0],
                ]
            );

            return;
        }

        $this->enable(
            [
                'onTwigTemplatePaths' => ['onTwigTemplatePaths', 0],
                'onTwigExtensions'    => ['onTwigExtensions', 0],
                'onOutputGenerated'   => ['onOutputGenerated', 0],
            ]
        );
    }

    /**
     * Add blueprint directory to page templates.
     */
    public function onGetPageTemplates(Event $event)
    {
        $locator = Grav::instance()['locator'];
        $event->types->scanTemplates($locator->findResource('plugin://' . $this->name . '/templates'));
    }

    /**
     * Register templates
     *
     * @return void
     */
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    /**
     * @return void
     */
    public function onTwigExtensions()
    {
        $this->grav['twig']->twig->addExtension(new PhotoswipeExtension());
    }

    public function onOutputGenerated()
    {
        /** @var Page $page */
        $page = $this->grav->get('page');

        if (!$page->getContentMeta('photoswipe_holder_needed')) {
            return;
        }

        /** @var Twig $twig */
        $twig   = $this->grav->get('twig');
        $holder = $twig->processTemplate('partials/photoswipe/holder.html.twig');

        if (!$holder) {
            return;
        }

        $doc = new \DOMDocument();

        libxml_use_internal_errors(true);
        $doc->loadHTML($this->grav->output);
        libxml_use_internal_errors(false);

        $body = $doc->getElementsByTagName('body')->item(0);

        if (!$body) {
            return;
        }

        $template = $doc->createDocumentFragment();
        $template->appendXML("\n" . $holder . "\n");

        $children = $body->childNodes;

        if (!$children->length) {
            $body->appendChild($template);
        } else {
            $node = null;

            for ($i = $children->length - 1; $i >= 0; $i--) {
                $child = $children->item($i);

                if (!$child instanceof \DOMElement) {
                    continue;
                }

                /** @var \DOMElement $child */
                $node_name = $child->nodeName;

                if ($node_name === 'script') {
                    continue;
                }

                $node = $child;

                break;
            }

            if (!$node) {
                $body->appendChild($template);
            } else {
                $node->parentNode->insertBefore($template, $node->nextSibling);
            }
        }

        $this->grav->output = $doc->saveHTML();
    }
}
