<?php

namespace Arcadia\Theme;

use OpeningHours;

class Shortcodes
{
    public static function init()
    {
        add_shortcode('email', [__CLASS__, 'email']);
        add_shortcode('phone', [__CLASS__, 'primaryPhone']);
        add_shortcode('all_phone', [__CLASS__, 'allPhoneNumbers']);
        add_shortcode('address', [__CLASS__, 'address']);
        add_shortcode('social', [__CLASS__, 'socialList']);
        add_shortcode('hours', [__CLASS__, 'storeHours']);
    }

    public static function storeHours($options)
    {
        if (is_string($options)) {
            $options = [];
        }

        $options['echo'] = false;

        return OpeningHours::render($options);
    }

    public static function address()
    {
        if (!get_field('locations', 'option')) {
            return '';
        }

        $content = '';

        foreach (get_field('locations', 'option') as $location) {
            $content .= '<p itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">';

            if ($location['address']) {
                $content .= '<span itemprop="streetAddress">' . $location['address'] . '</span><br> ';
            }

            if ($location['city']) {
                $content .= '<span itemprop="addressLocality">' . $location['city'] . '</span>, ';
            }

            if ($location['province']) {
                $content .= '<span itemprop="addressRegion">' . $location['province'] . '</span> ';
            }

            if ($location['postal_code']) {
                $content .= '<span itemprop="postalCode">' . $location['postal_code'] . '</span> ';
            }

            $content .= '</p>';
        }

        return $content;
    }

    public static function socialList()
    {
        if (!get_field('social_platforms', 'option')) {
            return '';
        }

        $content = '';

        foreach (get_field('social_platforms', 'option') as $option) {
            $content .= ' <a href="' . $option['url'] . '" target="_blank" rel="noopener" aria-label="' . $option['label'] . '"><em class="fab fa-' . $option['icon'] . '" aria-hidden="true"></em></a>';
        }

        return '<p class="social-list">' . $content . '</p>';
    }

    public static function email()
    {
        if (!get_field('email', 'option')) {
            return '';
        }

        return '<a href="mailto:' . get_field('email', 'option') . '" itemprop="email">' . get_field('email', 'option') . '</a>';
    }

    public static function primaryPhone()
    {
        if (!get_field('phone_numbers', 'option')) {
            return '';
        }

        $content = '';

        foreach (get_field('phone_numbers', 'option') as $key => $phone) {
            if (!$phone['default']) {
                continue;
            }

            $content .= '<a href="tel:' . convertPhoneNumber($phone['number']). '" itemprop="' . ($phone['type'] == 'faxNumber' ? $phone['type'] : 'telephone') . '">' . $phone['number'] . '</a>';

            break;
        }

        return $content;
    }

    public static function allPhoneNumbers()
    {
        if (!get_field('phone_numbers', 'option')) {
            return '';
        }

        $numbers = [];

        foreach (get_field('phone_numbers', 'option') as $key => $phone) {
            if (!$phone['default']) {
                continue;
            }

            $numbers[] = '<a href="tel:' . convertPhoneNumber($phone['number']). '" itemprop="' . ($phone['type'] == 'faxNumber' ? $phone['type'] : 'telephone') . '">' . $phone['number'] . '</a>';
        }

        return implode('<br>', $numbers);
    }
}
