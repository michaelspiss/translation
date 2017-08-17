<?php

namespace MichaelSpiss\Translation\Tests;

use MichaelSpiss\Translation\MessageSelector;
use MichaelSpiss\Translation\Translator;
use MichaelSpiss\Translation\ArrayLoader;
use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase {
    /** @var Translator $translator */
    private $translator;

    public function setUp() {
        $this->translator = new Translator('en',__DIR__.'/test_directory');
        $this->translator->addLoader('php', new ArrayLoader());
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
        $this->assertEquals('One', $this->translator->trans('message.one'));
    }

    public function testGetReplacesPlaceholdersCorrectly() {
        $this->assertEquals('With me!', $this->translator->trans('message.with_placeholder', ['placeholder' => 'me']));
    }

    public function testGetReplacesCurrentLocalCorrectly() {
        $this->assertEquals('Mit mir!', $this->translator->trans('message.with_placeholder', ['placeholder' => 'mir'], 'de'));
    }

    public function testGetResetsLocalAfterTemporaryChange() {
        $this->translator->trans('message.with_placeholder', ['placeholder' => 'mir'], 'de');
        $this->assertEquals('With me!', $this->translator->trans('message.with_placeholder', ['placeholder' => 'me']));
    }

    public function testGetReturnsKeyIfGroupHasNotMatchedAnything() {
        $this->assertEquals('unknown.group', $this->translator->trans('unknown.group'));
    }

    public function testGetReturnsKeyIfItHasNotMatchedAnything() {
        $this->assertEquals('message.unknown', $this->translator->trans('message.unknown'));
    }

    public function testGetReturnsKeyIfLayersGoTooDeep() {
        $this->assertEquals('message.one.too_deep', $this->translator->trans('message.one.too_deep'));
    }

    public function testGetReturnsCorrectValueWhenKeyHasMultipleLayers() {
        $this->assertEquals('Subkey Key', $this->translator->trans('message.key.subkey'));
    }

    public function testGetReturnsKeyWhenItMatchesASubkey() {
        $this->assertEquals('message.key', $this->translator->trans('message.key'));
    }

    public function testGetUsesCache() {
        $this->translator->trans('message.one');
        rename(__DIR__.'/test_directory/en/message.php', __DIR__.'/test_directory/en/locked.php');
        $this->assertEquals('One', $this->translator->trans('message.one'));
        rename(__DIR__.'/test_directory/en/locked.php', __DIR__.'/test_directory/en/message.php');
    }

    public function testReturnsPlaceholderIfNoReplacementIsGiven() {
        $this->assertEquals('With {placeholder}!', $this->translator->trans('message.with_placeholder'));
    }

    public function testHasReturnsTrueIfKeyExists() {
        $this->assertTrue($this->translator->has('message.one'));
    }

    public function testHasReturnsFalseIfKeyDoesNotExist() {
        $this->assertFalse($this->translator->has('message.unknown'));
    }

    public function testHasReturnsTrueIfKeyExistsWithLocaleChange() {
        $this->assertTrue($this->translator->has('message.one', 'de'));
    }

    public function testTransChoiceReturnsSingularStringWhenNumberIsOne() {
        $this->assertEquals('singular', $this->translator->transChoice('message.simple_choice', 1));
    }

    public function testTransChoiceReturnsPluralStringWhenNumberMoreThanOne() {
        $this->assertEquals('plural', $this->translator->transChoice('message.simple_choice', 2));
    }

    public function testTransChoiceReturnsCorrectStaticWhenUsingExpressions() {
        $this->assertEquals('None', $this->translator->transChoice('message.expression_choice', 0));
    }

    public function testTransChoiceReturnsCorrectSecondStaticWhenUsingExpressions() {
        $this->assertEquals('Some', $this->translator->transChoice('message.expression_choice', 1));
    }

    public function testTransChoiceWorksWithInclude() {
        $this->assertEquals('Include', $this->translator->transChoice('message.expression_exclude_include', 10));
    }

    public function testTransChoiceWorksWithExclude() {
        $this->assertEquals('message.expression_exclude_include', $this->translator->transChoice('message.expression_exclude_include', 11));
    }

    public function testTransChoiceReturnsKeyIfNoRuleCanBeApplied() {
        $this->assertEquals('message.invalid', $this->translator->transChoice('message.invalid', 15));
    }

    public function testMessageSelectorGetStringFromExpressionSkipsInvalidExpression() {
        $this->assertEquals('valid', MessageSelector::getStringFromExpression(['invalid' => 'string', '{10}' => 'valid'], 10));
    }

    public function testMultiplePlaceholdersAreReplacedCorrectly() {
        $this->assertEquals('Hello World!', $this->translator->trans('message.multiple_placeholders', ['placeholderOne' => 'Hello', 'placeholderTwo' => 'World']));
    }

}
