<?php

/**
 * Created by Raymond Chong
 * Clean html content
 *  
 */

namespace Html;

use DOMDocument;
use DOMElement;
use DOMXPath;

class Cleaner
{

    private static function RemoveElement(string $element, DOMDocument &$document)
    {
        $converter = new \Symfony\Component\CssSelector\CssSelectorConverter();
        $expression = $converter->toXPath($element);
        $xpath = new DOMXPath($document);
        foreach ($xpath->query($expression) as $node) {
            $node->parentNode->removeChild($node);
        }
    }

    //remove tag and keep the content
    private static function RemoveTag(string $tag, DOMDocument &$document)
    {
        $converter = new \Symfony\Component\CssSelector\CssSelectorConverter();
        $expression = $converter->toXPath($tag);
        $xpath = new DOMXPath($document);
        foreach ($xpath->query($expression) as $node) {

            $node->parentNode->replaceChild($node->ownerDocument->createTextNode($node->textContent), $node);
        }
    }

    private static function ReplaceElementAppendSpace(string $element, DOMDocument &$document)
    {
        $converter = new \Symfony\Component\CssSelector\CssSelectorConverter();
        $expression = $converter->toXPath($element);
        $xpath = new DOMXPath($document);
        foreach ($xpath->query($expression) as $node) {
            $text = trim($node->textContent);
            $node->parentNode->replaceChild($document->createTextNode(" " . $text . " "), $node);
        }
    }

    private static function GroupingUl(DOMDocument &$document)
    {
        $converter = new \Symfony\Component\CssSelector\CssSelectorConverter();
        $expression = $converter->toXPath("ul");

        $xpath = new DOMXPath($document);
        foreach ($xpath->query($expression) as $ul) {

            //find all li
            $lis = $ul->getElementsByTagName('li');

            //get all text
            $li_texts = [];

            foreach ($lis as $li) {
                $li_texts[] = trim($li->textContent);
            }

            $text = implode(" | ", $li_texts);



            //new div
            $ul->parentNode->replaceChild($ul->ownerDocument->createElement('div', $text), $ul);
        }
    }

    private static function RemoveAllAttributes(DOMDocument &$document)
    {
        $converter = new \Symfony\Component\CssSelector\CssSelectorConverter();
        $expression = $converter->toXPath("*");
        $xpath = new DOMXPath($document);
        foreach ($xpath->query($expression) as $node) {
            if ($node->hasAttributes()) {
                $attributes = iterator_to_array($node->attributes);
                foreach ($attributes as $attribute) {
                    $node->removeAttribute($attribute->name);
                }
            }
        }
    }

    private static function RemoveComment(DOMDocument &$document)
    {
        $converter = new \Symfony\Component\CssSelector\CssSelectorConverter();
        $expression = $converter->toXPath("comment()");
        $xpath = new DOMXPath($document);
        foreach ($xpath->query($expression) as $node) {
            $node->parentNode->removeChild($node);
        }
    }

    private static function RemoveNodesWithEmptyText(DOMDocument &$document)
    {
        $converter = new \Symfony\Component\CssSelector\CssSelectorConverter();
        $expression = $converter->toXPath("*");
        $xpath = new DOMXPath($document);
        foreach ($xpath->query($expression) as $node) {
            if (trim($node->textContent) === "") {
                $node->parentNode->removeChild($node);
            }
        }
    }

    private static function TrimText(DOMDocument &$document)
    {
        /*for example 
        <div>
        <div> hello</div>
        </div> 
        -> 
        <div><div>hello</div></div>
        */

        $converter = new \Symfony\Component\CssSelector\CssSelectorConverter();
        $expression = $converter->toXPath("*");
        $xpath = new DOMXPath($document);
        foreach ($xpath->query($expression) as $node) {

            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_TEXT_NODE) {
                    $child->nodeValue = trim($child->nodeValue);
                }
            }
        }
    }

    private static function RemoveRepeatedNodes(DOMDocument &$document)
    {

        //for example <div><div>hello</div></div> -> <div>hello</div>

        $converter = new \Symfony\Component\CssSelector\CssSelectorConverter();
        $expression = $converter->toXPath("*");
        $xpath = new DOMXPath($document);

        do {
            $processed = false;
            foreach ($xpath->query($expression) as $node) {
                assert($node instanceof \DOMNode);
                if ($node->childNodes->length === 1) {
                    $child = $node->childNodes->item(0);

                    if ($child->nodeType === XML_ELEMENT_NODE && $child->tagName === $node->tagName) {
                        print_R($child);
                        die;

                        print_R($child);
                        die;

                        $node->parentNode->insertBefore($child, $node);
                        $node->parentNode->removeChild($node);

                        $processed = true;
                        continue;
                    }
                }
            }
        } while ($processed);

        /*         
        
    
        foreach ($nodes as $node) {

            if ($node->hasChildNodes()) {
                $ns = iterator_to_array($node->childNodes);

                self::RemoveRepeatedNodes($ns);

                $ns = iterator_to_array($node->childNodes);

                if (count($ns) === 1) {
                    // echo $node->tagName . " <-> " . $ns[0]->tagName . "\n";
                    if ($ns instanceof DOMElement) {
                        if ($node->tagName === $ns[0]->tagName) {
                            assert($node instanceof \DOMNode);

                            if ($node->parentNode) {
                                $node->parentNode->insertBefore($ns[0], $node);
                                $node->parentNode->removeChild($node);
                            }
                        }
                    }
                }
            }
        }
 */
    }
    private static function ReplaceElement(string $element, string $replace, DOMDocument &$document)
    {
        $converter = new \Symfony\Component\CssSelector\CssSelectorConverter();
        $expression = $converter->toXPath($element);
        $xpath = new DOMXPath($document);
        foreach ($xpath->query($expression) as $node) {
            $node->parentNode->replaceChild($node->ownerDocument->createTextNode($replace), $node);
        }
    }

    private static function ProcessP(DOMDocument &$document)
    {
        $converter = new \Symfony\Component\CssSelector\CssSelectorConverter();
        $expression = $converter->toXPath("p");
        $xpath = new DOMXPath($document);
        foreach ($xpath->query($expression) as $node) {
            $node->parentNode->replaceChild($document->createElement('p', $node->textContent), $node);
        }
    }

    private static function ProcessDiv(DOMDocument &$document)
    {
        //get all div, if the div without contains element, only contain Text Node, remove new line \r or \n and add \n to the beginning of the text
        $converter = new \Symfony\Component\CssSelector\CssSelectorConverter();
        $expression = $converter->toXPath("div");
        $xpath = new DOMXPath($document);
        foreach ($xpath->query($expression) as $node) {

            //check if the div contains element
            $hasElement = false;
            foreach ($node->childNodes as $child) {
                if ($child->nodeType === XML_ELEMENT_NODE) {
                    $hasElement = true;
                    break;
                }
            }

            if (!$hasElement) {
                $text = trim($node->textContent);
                //remove new line \r or \n
                $text = preg_replace('/\s+/', ' ', $text);

                // add to div
                $node->parentNode->replaceChild($document->createElement('div', $text), $node);
            }
        }
    }

    private static function ProcessTdTh(DOMDocument &$document)
    {
        $converter = new \Symfony\Component\CssSelector\CssSelectorConverter();
        $expression = $converter->toXPath("td");
        $xpath = new DOMXPath($document);
        foreach ($xpath->query($expression) as $node) {
            //replace
            $node->parentNode->replaceChild($document->createElement('td', $node->textContent), $node);
        }

        $expression = $converter->toXPath("th");
        $xpath = new DOMXPath($document);
        foreach ($xpath->query($expression) as $node) {
            //replace
            $node->parentNode->replaceChild($document->createElement('div', $node->textContent), $node);
        }
    }

    public static function Clean(string $html)
    {
        //filter out invalid characters
        //filter out utf-8 invalid characters
        $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $html);

        //remove script by using regex
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);

        //remove style by using regex
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);

        //remove comments
        $html = preg_replace('/<!--(.*?)-->/is', '', $html);

        //remove noscript
        $html = preg_replace('/<noscript\b[^>]*>(.*?)<\/noscript>/is', '', $html);

        if (!$html) {
            return "";
        }

        $doc = Parser::ParseFromString($html);

        self::RemoveElement('img', $doc);
        self::RemoveElement('form', $doc);
        self::RemoveElement('button', $doc);
        self::RemoveElement('input', $doc);
        self::RemoveElement('select', $doc);
        self::RemoveElement('textarea', $doc);
        self::RemoveElement('iframe', $doc);
        self::RemoveElement('video', $doc);
        self::RemoveElement('audio', $doc);
        self::RemoveElement('embed', $doc);
        self::RemoveElement('object', $doc);
        self::RemoveElement('applet', $doc);
        self::RemoveElement('area', $doc);
        self::RemoveElement('map', $doc);
        self::RemoveElement('canvas', $doc);
        self::RemoveElement('svg', $doc);
        self::RemoveElement('footer', $doc);
        self::RemoveAllAttributes($doc);

        self::ProcessP($doc);

        self::ReplaceElement("sup", " ", $doc);

        self::ProcessTdTh($doc);



        self::ReplaceElementAppendSpace("h1",  $doc);
        self::ReplaceElementAppendSpace("h2",  $doc);
        self::ReplaceElementAppendSpace("h3",  $doc);
        self::ReplaceElementAppendSpace("h4",  $doc);
        self::ReplaceElementAppendSpace("h5",  $doc);
        self::ReplaceElementAppendSpace("span",  $doc);

        self::RemoveTag("a", $doc);


        self::GroupingUl($doc);

        self::RemoveNodesWithEmptyText($doc);

        //normalize document
        $doc->normalizeDocument();

        self::TrimText($doc);











        return $doc->saveHTML($doc->documentElement);
    }
}
