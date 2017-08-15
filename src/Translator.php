<?php

namespace MichaelSpiss\Translator;

/**
 * Class Translator
 * @package MichaelSpiss\Translator
 */
class Translator {
    /** @var string $locale */
    protected $locale;

    /** @var string $resource_dir */
    protected $resource_dir;

    /** @var array $loaders */
    protected $loaders = [];

    /** @var string $fallback_locale */
    protected $fallback_locale;

    /** @var MessageSelector $selector */
    protected $selector;

    /** @var array $supported_locales */
    protected $supported_locales = [];

    /** @var array $cache */
    protected $cache = [];
}
