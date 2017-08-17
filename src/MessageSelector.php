<?php

namespace MichaelSpiss\Translation;

/**
 * Class MessageSelector
 * @package MichaelSpiss\Translator
 */
class MessageSelector {
    /**
     * @var int RULE_EXPRESSION indicates that the {0} x | [12,3] y | ... rule
     * is being used
     * @see MessageSelector::choose()            which returns this const
     * @see MessageSelector::expressionMatches() should be invoked
     */
    const RULE_EXPRESSION = 0;
    /**
     * @var int RULE_SINGULAR_PLURAL indicates that the singular|plural rule
     * is being used
     * @see MessageSelector::choose()         which returns this const
     * @see MessageSelector::getPluralIndex() should be invoked
     */
    const RULE_SINGULAR_PLURAL = 1;
    /**
     * @var int RULE_BROKEN indicates that the data does not match any rule.
     * @see MessageSelector::choose() which returns this const
     * Suggests to just return the key and don't perform any more operations.
     */
    const RULE_BROKEN = 2;

    /**
     * Splits the string into its parts. Sorts them into 'expression' and
     * 'non-expression' - short 'none'.
     * @param string $translation the whole string
     * @return array
     */
    public static function getSortedChoices(string $translation): array {
        $parts = explode('|', $translation);
        $list = [];
        // find expressions and sort
        foreach($parts as $part) {
            $regex = '/(\{\s*\d+(?>\.\d+)?\s*\}|(\]|\[)\s*(\d+(?>\.\d+)?)\s*,\s*(\d+(?>\.\d+)?|\*)\s*(\]|\[))/';
            $expression = [];
            preg_match($regex, $part, $expression);
            $part = preg_replace($regex, '', $part);
            $part = trim($part, ' ');
            if(empty($expression)) {
                $list['none'][] = $part;
            } else {
                $list['expression'][$expression[0]] = $part;
            }
        }
        return $list;
    }

    /**
     * Chooses the rule which is being used to select the right part
     * of the string.
     * @see MessageSelector::getSortedChoices() to understand how the
     * sorted array is created
     * @param array $list a sorted array from getSortedChoices()
     * @return int
     */
    public static function choose(array $list): int {
        // use rule "{1} x | [12,3] y | ..."
        if(empty($list['none'])) {
            return MessageSelector::RULE_EXPRESSION;
        }
        // use rule "singular | plural"
        if(empty($list['expression'])) {
            return MessageSelector::RULE_SINGULAR_PLURAL;
        }
        // both arrays are populated -> mixed. (unsupported)
        return MessageSelector::RULE_BROKEN;
    }

    /**
     * Get the index to use for pluralization.
     * @codeCoverageIgnore because this is a giant switch statement which really makes no
     * sense to cover
     *
     * Method mostly from the symfony/translation package, which is distributed under the
     * MIT license. Copyright (c) Fabien Potencier <fabien@symfony.com>
     * The plural rules are derived from code of the Zend Framework (2010-09-25), which
     * is subject to the new BSD license (http://framework.zend.com/license/new-bsd)
     * Copyright (c) 2005-2010 - Zend Technologies USA Inc. (http://www.zend.com)
     *
     * @param  string  $locale
     * @param  int  $number
     * @return int
     */
    public static function getPluralIndex(string $locale, $number): int
    {
        if ('pt_BR' === $locale) {
            // temporary set a locale for brazilian
            $locale = 'xbr';
        }
        if (strlen($locale) > 3) {
            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));
        }
        switch ($locale) {
            case 'az':
            case 'bo':
            case 'dz':
            case 'id':
            case 'ja':
            case 'jv':
            case 'ka':
            case 'km':
            case 'kn':
            case 'ko':
            case 'ms':
            case 'th':
            case 'tr':
            case 'vi':
            case 'zh':
                return 0;
                break;
            case 'af':
            case 'bn':
            case 'bg':
            case 'ca':
            case 'da':
            case 'de':
            case 'el':
            case 'en':
            case 'eo':
            case 'es':
            case 'et':
            case 'eu':
            case 'fa':
            case 'fi':
            case 'fo':
            case 'fur':
            case 'fy':
            case 'gl':
            case 'gu':
            case 'ha':
            case 'he':
            case 'hu':
            case 'is':
            case 'it':
            case 'ku':
            case 'lb':
            case 'ml':
            case 'mn':
            case 'mr':
            case 'nah':
            case 'nb':
            case 'ne':
            case 'nl':
            case 'nn':
            case 'no':
            case 'om':
            case 'or':
            case 'pa':
            case 'pap':
            case 'ps':
            case 'pt':
            case 'so':
            case 'sq':
            case 'sv':
            case 'sw':
            case 'ta':
            case 'te':
            case 'tk':
            case 'ur':
            case 'zu':
                return ($number == 1) ? 0 : 1;
            case 'am':
            case 'bh':
            case 'fil':
            case 'fr':
            case 'gun':
            case 'hi':
            case 'hy':
            case 'ln':
            case 'mg':
            case 'nso':
            case 'xbr':
            case 'ti':
            case 'wa':
                return (($number == 0) || ($number == 1)) ? 0 : 1;
            case 'be':
            case 'bs':
            case 'hr':
            case 'ru':
            case 'sr':
            case 'uk':
                return (($number % 10 == 1) && ($number % 100 != 11)) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2);
            case 'cs':
            case 'sk':
                return ($number == 1) ? 0 : ((($number >= 2) && ($number <= 4)) ? 1 : 2);
            case 'ga':
                return ($number == 1) ? 0 : (($number == 2) ? 1 : 2);
            case 'lt':
                return (($number % 10 == 1) && ($number % 100 != 11)) ? 0 : ((($number % 10 >= 2) && (($number % 100 < 10) || ($number % 100 >= 20))) ? 1 : 2);
            case 'sl':
                return ($number % 100 == 1) ? 0 : (($number % 100 == 2) ? 1 : ((($number % 100 == 3) || ($number % 100 == 4)) ? 2 : 3));
            case 'mk':
                return ($number % 10 == 1) ? 0 : 1;
            case 'mt':
                return ($number == 1) ? 0 : ((($number == 0) || (($number % 100 > 1) && ($number % 100 < 11))) ? 1 : ((($number % 100 > 10) && ($number % 100 < 20)) ? 2 : 3));
            case 'lv':
                return ($number == 0) ? 0 : ((($number % 10 == 1) && ($number % 100 != 11)) ? 1 : 2);
            case 'pl':
                return ($number == 1) ? 0 : ((($number % 10 >= 2) && ($number % 10 <= 4) && (($number % 100 < 12) || ($number % 100 > 14))) ? 1 : 2);
            case 'cy':
                return ($number == 1) ? 0 : (($number == 2) ? 1 : ((($number == 8) || ($number == 11)) ? 2 : 3));
            case 'ro':
                return ($number == 1) ? 0 : ((($number == 0) || (($number % 100 > 0) && ($number % 100 < 20))) ? 1 : 2);
            case 'ar':
                return ($number == 0) ? 0 : (($number == 1) ? 1 : (($number == 2) ? 2 : ((($number % 100 >= 3) && ($number % 100 <= 10)) ? 3 : ((($number % 100 >= 11) && ($number % 100 <= 99)) ? 4 : 5))));
            default:
                return 0;
        }
    }

    /**
     * Returns the string that matches the condition.
     * @param array $expressions array of expression => string pairs
     * @param int|float $number the condition the expression must fulfill
     * @return string|null
     */
    public static function getStringFromExpression(array $expressions, $number) {
        foreach($expressions as $expression => $string) {
            if(self::expressionMatches($expression, $number)) {
                return $string;
            }
        }
        return null;
    }

    /**
     * Finds out whether the expression {x} or [y,z] matches the condition (number) or not
     * @see MessageSelector::getStringFromExpression() where this method is being used
     * @param string $expression
     * @param int|float $number
     * @return bool
     */
    protected static function expressionMatches(string $expression, $number): bool {
        $expression = trim($expression, ' ');
        $expression = str_replace(' ', '', $expression);
        $static = '/\{\s*(\d+(?>\.\d+)?)\s*\}/';
        $range = '/(\]|\[)\s*(\d+(?>\.\d+)?)\s*,\s*(\d+(?>\.\d+)?|\*)\s*(\]|\[)/';
        $split = [];
        if(preg_match($static, $expression, $split)) {
            return $split[1] == $number;
        }
        $split = [];
        if(preg_match($range, $expression, $split)) {
            $first_bracket = $split[1];
            $first_digit = $split[2];
            $second_digit = $split[3];
            $second_bracket = $split[4];
            //                                       ?       [ includes                 ] excludes
            $first_condition = $first_bracket == '[' ? $number >= $first_digit : $number > $second_digit;
            $second_condition = $second_digit == '*' ? true : $second_bracket == ']' ? $number <= $second_digit : $number < $second_digit;
            return $first_condition && $second_condition;
        }
        return false;
    }
}
