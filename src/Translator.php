<?php

namespace MichaelSpiss\Translator;

/**
 * Class Translator
 * @package MichaelSpiss\Translator
 */
class Translator {
    /** @var string $locale */
    private $locale;

    /** @var string $resource_dir */
    private $resource_dir;

    /** @var array $loaders */
    private $loaders = [];

    /** @var string $fallback_locale */
    private $fallback_locale;

    /** @var MessageSelector $selector */
    private $selector;

    /** @var array $locales */
    private $locales = [];

    /** @var array $cache */
    private $cache = [];
}
