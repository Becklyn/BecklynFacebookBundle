<?php

namespace Becklyn\FacebookBundle\Service;

/**
 * Bundles generic and facebook app-unspecific logic
 *
 * @package Becklyn\FacebookBundle\Service
 */
class UtilitiesService
{
    /**
     * Returns the HTML for a like button
     *
     * @param string $url the url to like
     * @param array $options
     *
     * @return string
     */
    public function likeButton ($url, array $options = array())
    {
        $defaultOptions = array(
            "href"       => rawurlencode($url),
            "send"       => "false",
            "layout"     => "button_count",
            "width"      => 90,
            "show-faces" => "false"
        );
        $options = array_merge($defaultOptions, $options);

        $attributes = array();
        foreach ($options as $key => $value)
        {
            if (is_bool($value))
            {
                $sanitized = $value ? "true" : "false";
            }
            else
            {
                $sanitized = (string) $value;
            }

            $attributes[] = "data-{$key}=\"{$sanitized}\"";
        }

        return '<div class="fb-like" ' . implode(' ', $attributes) . '></div>';
    }



    /**
     * Returns the profile picture URL
     *
     * @param string $facebookId
     *
     * @return string
     */
    public function fbProfileImage ($facebookId)
    {
        return "https://graph.facebook.com/{$facebookId}/picture";
    }



    /**
     * Returns the facebook profile url
     *
     * @param $facebookId
     *
     * @return string
     */
    public function fbProfileUrl ($facebookId)
    {
        return "https://www.facebook.com/profile.php?id={$facebookId}";
    }



    /**
     * Truncates the like description text
     *
     * @param string $text
     * @param int $length
     *
     * @return string
     */
    public static function truncateLikeDescriptionText ($text, $length = 80)
    {
        $text = strip_tags($text);
        $text = html_entity_decode($text, null, "UTF-8");
        $truncated = mb_substr($text, 0, $length, "UTF-8");
        $truncated .= (mb_strlen($truncated, "UTF-8") < mb_strlen($text, "UTF-8")) ? "..." : "";

        return $truncated;
    }
}