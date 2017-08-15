<?php

namespace MichaelSpiss\Translation;

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
        $this->resource_dir = $resource_dir;
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

    /**
     * Returns the currently set locale
     * @return string
     */
    public function getLocale() : string {
        return $this->locale;
    }

    /**
     * Sets a new locale. If it isn't supported the fallback is set.
     * @param string $locale
     */
    public function setLocale(string $locale) {
        if($this->supports($locale)) {
            $this->locale = $locale;
        } else {
            $this->locale = $this->fallback_locale;
        }
    }

    /**
     * Adds a new loader for a specific file-type.
     * Only one loader per file-type is permitted.
     * @param string          $format (ex. php, yml, json, ...)
     * @param LoaderInterface $loader
     */
    public function addLoader(string $format, LoaderInterface $loader) {
        $this->loaders[$format] = $loader;
    }

    /**
     * Checks if a translation is available for the given key
     * @param string $key
     * @param string $locale
     * @return bool
     */
    public function has(string $key, string $locale = ''): bool {
        return $key !== $this->get($key, [], $locale);
    }

    /**
     * The main method to get from key to string
     * @param string $key the key to search by. Format: filename.key.subkey
     * @param array  $replace replacements for {placeholders}.
     * Must be in ['placeholder' => 'value'] format.
     * @param string $locale temporarily changes the locale for this request
     * @return string
     */
    protected function get(string $key, array $replace = [], string $locale = '') {
        // if $locale is unset, use default locale
        $locale = $locale == '' ? $this->locale : $locale;

        $key_parts = explode('.', $key);
        $group = array_shift($key_parts);

        // use cache if possible
        if(isset($this->cache[$locale][$group])) {
            $group_content = $this->cache[$locale][$group];
        } else {
            $group_content = $this->loadFromFile($locale, $group);
        }
        $base_value = $this->getValue($key_parts, $group_content) ?? $key;
        return $this->replace($base_value, $replace);
    }

    /**
     * Loads the group's translation data from the first file that
     * matches it's name
     * @param string $locale temporarily sets the locale for this request
     * @param string $group the filename to search for
     * @return array returns an empty array if no file with this name exists
     */
    private function loadFromFile(string $locale, string $group): array {
        $file = $this->findFile($locale, $group);
        if ($file) {
            $file_exploded = explode('.', $file);
            $extension = array_pop($file_exploded);
            if(isset($this->loaders[$extension])) {
                $content = $this->loaders[$extension]->getContent($file);
                $this->updateCache($locale, $group, $content);
                return $content;
            }
        }
        return [];
    }

    /**
     * Finds the file that holds the translation data for the specified
     * group
     * @param string $locale
     * @param string $group
     * @return bool|string the file path or, if not found, false
     */
    private function findFile(string $locale, string $group) {
        $files = glob($this->resource_dir.'/'.$locale.'/'.$group.'.*');
        foreach($files as $file) {
            if(is_file($file)) {
                return $file;
            }
        }
        return false;
    }

    /**
     * Returns the key's value. If not found, it returns null
     * @param array $key_parts
     * @param array $group_content
     * @return string|null
     */
    private function getValue(array $key_parts, array $group_content) {
        $iteration = $group_content;
        foreach ($key_parts as $part) {
            if(isset($iteration[$part])) {
                $iteration = $iteration[$part];
            } else {
                return null;
            }
        }
        if(is_string($iteration)) {
            return $iteration;
        }
        return null;
    }

    /**
     * Updates cache with the loaded translations
     * @param string $locale
     * @param string $group
     * @param array  $group_content
     */
    private function updateCache(string $locale, string $group, array $group_content) {
        $data_array = [ $locale => [ $group => $group_content ] ];
        $this->cache = array_merge($this->cache, $data_array);
    }

    /**
     * Replaces placeholders with their values. If no value is passed,
     * the placeholder stays.
     * @param string $base_value  the whole string
     * @param array  $replace     An array of ['placeholder' => 'value'] pairs
     * @return string
     */
    public function replace(string $base_value, array $replace): string {
        return preg_replace_callback('/\{[a-zA-Z]+\}/', function($match) use ($replace) {
            $name = trim($match[0], "{}");
            return $replace[$name] ?? $match[0];
        }, $base_value);
    }

    /**
     * The main method to get from key to string (Public)
     * @param string $key the key to search by. Format: filename.key.subkey
     * @param array  $replace replacements for {placeholders}.
     * Must be in ['placeholder' => 'value'] format.
     * @param string $locale temporarily changes the locale for this request
     * @return string
     */
    public function trans(string $key, array $replace = [], string $locale = '') {
        return $this->get($key, $replace, $locale);
    }
}
