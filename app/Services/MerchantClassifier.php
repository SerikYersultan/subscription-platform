<?php

namespace App\Services;

class MerchantClassifier
{
    private const POSITIVE_KEYWORDS = [
        // Generic subscription signals
        'plus',
        'premium',
        'pro',
        'subscription',
        'subscribe',
        'member',
        'membership',
        'streaming',
        'cloud',
        'storage',
        // Apple
        'apple.com',
        'apple tv',
        'icloud',
        // Google
        'google.com',
        'google one',
        // Microsoft
        'microsoft',
        'microsoft.com',
        'office',
        'onedrive',
        // Music & video
        'music',
        'kinopoisk',
        'netflix',
        'spotify',
        'youtube',
        'hulu',
        'disney',
        'paramount',
        'peacock',
        'deezer',
        'tidal',
        // Software & productivity
        'adobe',
        'figma',
        'chatgpt',
        'canva',
        'dropbox',
        'duolingo',
        'grammarly',
        'notion',
        'slack',
        'zoom',
        'github',
        'jetbrains',
        // Russian streaming / subscription services (Cyrillic)
        'кинопоиск',
        'яндекс плюс',
        'яндекс музыка',
    ];

    private const NEGATIVE_KEYWORDS = [
        // Ride-hailing
        'go',
        'taxi',
        'ride',
        // Food & delivery
        'eats',
        'lavka',
        'delivery',
        'food',
        'burger',
        'pizza',
        'sushi',
        'kebab',
        'shaurma',
        'fastfood',
        'restaurant',
        'cafe',
        'coffee',
        // Retail & shopping
        'market',
        'grocery',
        'supermarket',
        'hypermarket',
        'магазин',        // shop (Russian)
        // Fuel & transport
        'fuel',
        'petrol',
        'gas station',
        'parking',
        'toll',
        // Financial transfers
        'transfer',
        'atm',
        'p2p',
        'cash',
        'withdraw',
        'deposit',
        'to card',
        'from card',
        'аппарат',        // self-service machine / ATM (Russian)
        'самообслуживания', // self-service (Russian)
        // Utilities & rentals
        'pharmacy',
        'drugstore',
        'powerbank',
        'rental',
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
        $text = mb_strtolower(trim($merchantName).' '.trim($description ?? ''));
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
            $cat = mb_strtolower($category);
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
        // /u enables Unicode-aware word boundaries so Cyrillic keywords work correctly.
        return (bool) preg_match('/\b'.preg_quote($keyword, '/').'\\b/iu', $text);
    }
}
