<?php

namespace Html;

use DOMDocument;

class Parser
{
    public static function ParseFromString(string $str)
    {

        libxml_use_internal_errors(true);


        $option = 0;
        if (LIBXML_VERSION >= 20621) {
            $option |=  LIBXML_COMPACT;
        }


        if (LIBXML_VERSION >= 20708) {
            $option |= LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD;
        }

        $doc = new DOMDocument();


        //$doc->loadHTML(mb_convert_encoding("<div>" . $str . "</div>", 'HTML-ENTITIES', 'UTF-8'), $option);
        $doc->loadHTML(mb_encode_numericentity($str, [0x80, 0x10FFFF, 0, ~0], 'UTF-8'), $option);

        return $doc;
    }
}
