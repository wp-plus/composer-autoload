<?php

/**
 * TODO: Move this to a standalone library.
 */

namespace WpPlus\ComposerAutoload\Wp;

class Config
{
    public function __construct(
        public string $ABSPATH = './',
        public ?string $WP_CONTENT_DIR = null,
        public ?string $WP_PLUGIN_DIR = null,
        public ?string $WPMU_PLUGIN_DIR = null,
    ) {
        if (empty($this->WP_CONTENT_DIR)) {
            $this->WP_CONTENT_DIR = $this->ABSPATH . 'wp-content';
        }
        if (empty($this->WP_PLUGIN_DIR)) {
            $this->WP_PLUGIN_DIR = $this->WP_CONTENT_DIR . '/plugins';
        }
        if (empty($this->WPMU_PLUGIN_DIR)) {
            $this->WPMU_PLUGIN_DIR = $this->WP_CONTENT_DIR . '/mu-plugins';
        }
    }

    public static function createFromPluginConfig($config): static
    {
        return new static(
            ABSPATH: $config['ABSPATH'] ?? './',
            WP_CONTENT_DIR: $config['WP_CONTENT_DIR'] ?? null,
            WP_PLUGIN_DIR: $config['WP_PLUGIN_DIR'] ?? null,
            WPMU_PLUGIN_DIR: $config['WPMU_PLUGIN_DIR'] ?? null,
        );
    }
}
