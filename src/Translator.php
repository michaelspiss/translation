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

    /**
     * Translator constructor.
     *
     * @param string $fallback_locale also used as default
     * @param string $resource_dir directory where every subfolder
     * represents a supported language
     */
    public function __construct($fallback_locale, $resource_dir) {
        $this->fallback_locale = $fallback_locale;
        $this->locale = $fallback_locale;
        $this->index_directory($resource_dir);
    }

    /**
     * Indexes the given directory, Adds found subdirs to supported languages
     * @param string $directory
     */
    protected function index_directory($directory) {
        $subdirs = [];
        foreach(glob($directory.'/*', GLOB_ONLYDIR) as $dir) {
            $subdirs[] = basename($dir);
        }
        $this->supported_locales = array_merge($this->supported_locales, $subdirs);
    }

    /**
     * Checks if the entered local is supported
     * @param string $local
     * @return bool
     */
    public function supports(string $local) : bool {
        return in_array($local, $this->supported_locales);
    }
}
