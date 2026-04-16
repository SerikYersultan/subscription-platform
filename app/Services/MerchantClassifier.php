<?php

namespace App\Services;

class MerchantClassifier
{
    private const POSITIVE_KEYWORDS = [
        'plus',
        'premium',
        'pro',
        'subscription',
        'subscribe',
        'member',
        'membership',
        'music',
        'kinopoisk',
        'cloud',
        'storage',
        'netflix',
        'spotify',
        'adobe',
        'figma',
        'chatgpt',
        'canva',
        'dropbox',
        'icloud',
        'youtube',
        'hulu',
        'disney',
        'paramount',
        'peacock',
        'deezer',
        'tidal',
        'duolingo',
        'grammarly',
        'notion',
        'slack',
        'zoom',
        'github',
        'jetbrains',
        'office',
        'microsoft',
        'apple tv',
        'google one',
        'onedrive',
        'streaming',
    ];

    private const NEGATIVE_KEYWORDS = [
        'go',
        'taxi',
        'ride',
        'eats',
        'lavka',
        'market',
        'grocery',
        'supermarket',
        'hypermarket',
        'fuel',
        'petrol',
        'gas station',
        'restaurant',
        'cafe',
        'coffee',
        'delivery',
        'pharmacy',
        'drugstore',
        'transfer',
        'atm',
        'p2p',
        'cash',
        'withdraw',
        'parking',
        'toll',
        'food',
        'burger',
        'pizza',
        'sushi',
        'kebab',
        'shaurma',
        'fastfood',
    ];

    private const POSITIVE_CATEGORIES = [
        'subscription',
        'digital_service',
        'software',
        'streaming',
        'membership',
        'saas',
    ];

    private const NEGATIVE_CATEGORIES = [
        'taxi',
        'food_delivery',
        'grocery',
        'retail',
        'fuel',
        'transfer',
        'transport',
        'restaurant',
        'cafe',
        'pharmacy',
        'atm',
        'p2p',
        'fast_food',
    ];

    /**
     * Returns a score modifier in the range roughly -75 to +50.
     * Positive = looks like a subscription, negative = looks like routine spending.
     */
    public function classify(string $merchantName, ?string $description = null, ?string $category = null): int
    {
        $text = strtolower(trim($merchantName).' '.trim($description ?? ''));
        $score = 0;

        foreach (self::POSITIVE_KEYWORDS as $keyword) {
            if ($this->matches($text, $keyword)) {
                $score += 35;
                break;
            }
        }

        foreach (self::NEGATIVE_KEYWORDS as $keyword) {
            if ($this->matches($text, $keyword)) {
                $score -= 60;
                break;
            }
        }

        if ($category !== null) {
            $cat = strtolower($category);
            foreach (self::POSITIVE_CATEGORIES as $c) {
                if (str_contains($cat, $c)) {
                    $score += 15;
                    break;
                }
            }
            foreach (self::NEGATIVE_CATEGORIES as $c) {
                if (str_contains($cat, $c)) {
                    $score -= 25;
                    break;
                }
            }
        }

        return $score;
    }

    private function matches(string $text, string $keyword): bool
    {
        return (bool) preg_match('/\b'.preg_quote($keyword, '/').'\\b/i', $text);
    }
}
