<?php

namespace MichaelSpiss\Translator\Tests;

use MichaelSpiss\Translator\Translator;
use PHPUnit\Framework\TestCase;
use Webmozart\Assert\Assert;

class TranslatorTest extends TestCase {
    /** @var Translator $translator */
    private $translator;

    public function setUp() {
        $this->translator = new Translator('en',__DIR__.'/test_directory');
    }

    public function testIndexDirectoryGetsAllSubfoldersAndSetsThemAsSupportedLocals() {
        $this->assertTrue($this->translator->supports('en'));
    }

    public function testGetLocaleReturnsDefaultLocale() {
        $this->assertEquals('en', $this->translator->getLocale());
    }

    public function testSetLocaleSetsNewLocaleIfSupportedLocaleIsEntered() {
        $this->translator->setLocale('de');
        $this->assertEquals('de', $this->translator->getLocale());
    }

    public function testSetLocaleSetsFallbackIfUnsupportedLocaleIsEntered() {
        $this->translator->setLocale('fr');
        $this->assertEquals('en', $this->translator->getLocale());
    }
}
