<?php

namespace timezone;

/**
 * Class HtmlTemplate
 * @package timezone
 */
class HtmlTemplate {

    /**
     * @param string $templateName
     * @param array $parameters
     * @return string
     */
    public static function getTemplate($templateName, $parameters)
    {
        $html = file_get_contents('html/' . $templateName . '.html');

        $replace    = array_values($parameters);
        $search     = array_keys($parameters);
        foreach ($search as &$key) {
            $key = '{{' . $key . '}}';
        }

        return str_replace($search, $replace, $html);
    }
}