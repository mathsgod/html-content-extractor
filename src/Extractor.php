<?php

namespace Html;

class Extractor
{
    public static function Extract(string $html)
    {

        $doc = Parser::ParseFromString(Cleaner::Clean($html));

        $xpath = new \DOMXPath($doc);
        $nodes = $xpath->query('//text()');
        $result = [];
        foreach ($nodes as $node) {

            $text = preg_replace('/\s+/', ' ', $node->nodeValue);

            $result[] = trim($text);
        }

        //remove empty
        $result = array_filter($result);

        //implode
        $result = implode("\n", $result);

        return $result;
    }
}
