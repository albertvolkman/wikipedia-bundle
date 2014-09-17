<?php
/**
 * @file
 * Contains AlbertVolkman\Wikipedia\Helper\Parser.
 */

namespace AlbertVolkman\Wikipedia\Helper;

use AlbertVolkman\Wikipedia\Action\Parse;
use SimpleXMLElement;

class Parser {
    /**
     * Service that provides access to Wikipedia's parsing API.
     *
     * @var ParseAction
     */
    protected $parse;

    /**
     * Creates an instance of the Parse class.
     *
     * @param Parse $parse
     *   Service that provides access to Wikipedia's parsing API.
     */
    public function __construct(Parse $parse)
    {
        // Dependency injection.
        $this->parse = $parse;
    }

    public function infoBox($infobox)
    {
        $count = 0;
        $start = 0;
        $end = 0;
        $strlen = strlen($infobox);

        // Parse out data between opening and closing {{}}.
        for ($i = 0; $i <= $strlen; $i++) {
            $char = substr($infobox, $i, 1);
            if ($char == '{' && substr($infobox, $i+1, 1) == '{') {
                if ($count == 0) {
                    $start = $i+2;
                }
                $count++;
            } elseif ($char == '}' && substr($infobox, $i+1, 1) == '}') {
                $count--;
                if ($count == 0) {
                    $end = $i;
                    break;
                }
            }
        }

        // Reformat data into an associative array.
        $array = array();
        $infobox = explode("\n", substr($infobox, $start, $end-$start));
        foreach ($infobox as $data) {
            $item = explode('=', $data, 2);
            $item[0] = trim(ltrim($item[0], '|'));
            if (isset($item[1]) && !empty($item[1])) {
                $item[1] = trim($item[1]);
                if (!empty($item[1])) {
                    $text = $this->parse->text($item[1]);
                    if (preg_match('/\[\[File/', $item[1])) {
                        $text = $this->wikiImage($text['parse']['text']['*']);
                    } else {
                        $text = $this->stripTags($text['parse']['text']['*']);
                    }
                    $array[$item[0]] = $text;
                }
            }
        }

        return $array;
    }

    /**
     * Prepare the titles for query.
     */
    public function convertTitles($titles)
    {
        $titles = array_map(function ($n) {
            return rawurlencode($n);
        }, $titles);

        return implode('|', $titles);
    }

    public function stripTags($text)
    {
        return html_entity_decode(trim(strip_tags($text)));
    }

    public function wikiImage($text)
    {
        $dom = new SimpleXMLElement($text);

        return 'http:' . (string) $dom->xpath('//img/@src')[0]->src;
    }
}
