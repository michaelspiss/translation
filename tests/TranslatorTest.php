<?php

namespace MichaelSpiss\Translation\Tests;

use MichaelSpiss\Translation\Translator;
use PHPUnit\Framework\TestCase;

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

    public function testGetReturnsCorrectValueFromFile() {
        $this->translator->addLoader('php', new TestLoader());
        $this->assertEquals('One', $this->translator->trans('message.one'));
    }

    public function testGetReplacesPlaceholdersCorrectly() {
        $this->translator->addLoader('php', new TestLoader());
        $this->assertEquals('With me!', $this->translator->trans('message.with_placeholder', ['placeholder' => 'me']));
    }

    public function testGetReplacesCurrentLocalCorrectly() {
        $this->translator->addLoader('php', new TestLoader());
        $this->assertEquals('Mit mir!', $this->translator->trans('message.with_placeholder', ['placeholder' => 'mir'], 'de'));
    }

    public function testGetResetsLocalAfterTemporaryChange() {
        $this->translator->addLoader('php', new TestLoader());
        $this->translator->trans('message.with_placeholder', ['placeholder' => 'mir'], 'de');
        $this->assertEquals('With me!', $this->translator->trans('message.with_placeholder', ['placeholder' => 'me']));
    }

    public function testGetReturnsKeyIfGroupHasNotMatchedAnything() {
        $this->translator->addLoader('php', new TestLoader());
        $this->assertEquals('unknown.group', $this->translator->trans('unknown.group'));
    }

    public function testGetReturnsKeyIfItHasNotMatchedAnything() {
        $this->translator->addLoader('php', new TestLoader());
        $this->assertEquals('message.unknown', $this->translator->trans('message.unknown'));
    }

    public function testGetReturnsKeyIfLayersGoTooDeep() {
        $this->translator->addLoader('php', new TestLoader());
        $this->assertEquals('message.one.too_deep', $this->translator->trans('message.one.too_deep'));
    }

    public function testGetReturnsCorrectValueWhenKeyHasMultipleLayers() {
        $this->translator->addLoader('php', new TestLoader());
        $this->assertEquals('Subkey Key', $this->translator->trans('message.key.subkey'));
    }

    public function testGetReturnsKeyWhenItMatchesASubkey() {
        $this->translator->addLoader('php', new TestLoader());
        $this->assertEquals('message.key', $this->translator->trans('message.key'));
    }

    public function testGetUsesCache() {
        $this->translator->addLoader('php', new TestLoader());
        $this->translator->trans('message.one');
        rename(__DIR__.'/test_directory/en/message.php', __DIR__.'/test_directory/en/locked.php');
        $this->assertEquals('One', $this->translator->trans('message.one'));
        rename(__DIR__.'/test_directory/en/locked.php', __DIR__.'/test_directory/en/message.php');
    }

    public function testReturnsPlaceholderIfNoReplacementIsGiven() {
        $this->translator->addLoader('php', new TestLoader());
        $this->assertEquals('With {placeholder}!', $this->translator->trans('message.with_placeholder'));
    }
}
