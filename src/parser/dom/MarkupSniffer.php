<?php declare(strict_types = 1);
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace pvc\parser\dom;

use pvc\intl\Charset;
use pvc\regex\err\RegexBadPatternException;
use pvc\regex\err\RegexInvalidMatchIndexException;
use pvc\regex\err\RegexPatternUnsetException;
use pvc\regex\Regex;

/**
 * MarkupSniffer yries to get a little bit of basic information about a markup document.
 *
 * The goal was to do this without having to invoke the overhead of the full DOM parser.  The search via
 * regex is admittedly quick and dirty.
 *
 * This class was designed to be able to generate a mime-type for sending a document off to an online validator
 * to be validated.  The php function finfo when used with the FILE_MIME_TYPE constant does not examine
 * declarations - it is actually sniffing each character in the file and trying to determine a code set
 * based on the values.  Moreover, if a DOM Parser fails badly on the text, the DOM Document could be
 * empty and so you get nothing anyway.  True that if the DOM parser fails that you do not have a valid
 * document, but the message output from the online validators is far superior to what you get out of LibXML.
 *
 * Class MarkupSniffer.
 */
class MarkupSniffer
{

    /**
     * @var Regex
     */
    private Regex $regex;

    /**
     * @var string
     */
    private string $markupLanguage;

    /**
     * @var Charset
     */
    private Charset $charset;

    /**
     * MarkupSniffer constructor.
     */
    public function __construct()
    {
        $this->regex = new Regex();
    }

    /**
     * @function getMarkupLanguage
     * @return string|null
     */
    public function getMarkupLanguage(): ?string
    {
        return $this->markupLanguage ?? null;
    }

    /**
     * @function getCharset
     * @return Charset|null
     */
    public function getCharset(): ?Charset
    {
        return $this->charset ?? null;
    }

    /**
     * @function setCharset
     * @param string $charset
     */
    protected function setCharset(string $charset): void
    {
        $cs = new Charset();
        if ($cs->validate($charset)) {
            $this->charset = $cs;
        }
    }

    /**
     * @function sniff
     * @param string $fileContents
     * @throws RegexBadPatternException
     * @throws RegexInvalidMatchIndexException
     * @throws RegexPatternUnsetException
     */
    public function sniff(string $fileContents): void
    {
        $fileContents = $this->stripComments($fileContents);
        $this->sniffMarkupLanguage($fileContents);
        $this->sniffCharsetDeclaration($fileContents);
    }

    /**
     * stripComments removes all comments from the markup document.
     *
     * The purpose is that we can search for text without being fooled by something that is commented out.
     *
     * Note that there are some edge cases in html that this method will strip when a regular parser would/should
     * not or would fail completely. The main case that comes to mind is when a comment would wrongly appear inside
     * another tag.  Here are two examples:  "<!DOCTYPE html <!--this comment is illegal --> >" and
     * "<script>function foo(a) { <!-- this comment is in middle of some javascript --> return a; }</script>"
     * For the purposes of this class, it should not matter.  Just don't use this method in another
     * context without being aware!
     *
     * note the "internal option setting" U which makes the regex ungreedy.
     * @function stripComments
     * @param string $string
     * @return string
     *
     */

    public function stripComments(string $string): string
    {
        $regex = "/<!--.*-->/U";
        $replacement = "";
        return preg_replace($regex, $replacement, $string) ?: '';
    }

    /**
     * @function sniffMarkupLanguage
     * @param string $fileContents
     * @throws RegexBadPatternException
     * @throws RegexInvalidMatchIndexException
     * @throws RegexPatternUnsetException
     */
    public function sniffMarkupLanguage(string $fileContents): void
    {
        // finds any tag that starts with "<!DOCTYPE", has trailing whitespace after DOCTYPE, and contains
        // either 'xml' or 'html' somewhere before the closing tag. Make the search case insensitive since HTML
        // is case insensitive.  Even if xml is case-sensitive, leave this search insensitive so there is some
        // leniency for a doctype declaration which is close but not quite right.  Similarly, the DOCTYPE
        // directive should be the first thing in the document but this search does not rely on that.

        $pattern = "/<!doctype\s.*(?<doctype>html|xml).*>/i";
        $this->regex->setPattern($pattern);
        if ($result = $this->regex->match($fileContents)) {
            /* phpstan sees getMatch returning false|array|string */
            /** @phpstan-ignore-next-line */
            $this->markupLanguage = strtolower($this->regex->getMatch('doctype'));
            return;
        }

        // no doctype declaration, try to discover based on opening tag
        $pattern = "/^\s*<(\?)?(?<doctype>xml|html)/";
        $this->regex->setPattern($pattern);
        if ($result = $this->regex->match($fileContents)) {
            /* phpstan sees getMatch returning false|array|string */
            /** @phpstan-ignore-next-line */
            $this->markupLanguage = strtolower($this->regex->getMatch('doctype'));
        }
    }

    /**
     * @function sniffCharsetDeclaration
     * @param string $fileContents
     * @throws RegexBadPatternException
     * @throws RegexInvalidMatchIndexException
     * @throws RegexPatternUnsetException
     */
    public function sniffCharsetDeclaration(string $fileContents): void
    {
        // there are a couple of different ways it can be formed

        // <meta charset="utf-8">
        // opening quote (single or double) is captured after the string 'charset'.
        //  The pattern ends when the parser hits the closing quote
        $patterns = [];
        $patterns[] = "/<meta.*charset=('|\")?(?<charset>.*)\g1/i";

        // <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        // pattern ends when it hits ', ", \s or >
        $patterns[] = "/<meta.*charset=(?<charset>.*)['\"\s>]/iU";

        // look for an opening xml tag with an encoding attribute.
        $patterns[] = "/<?xml.*encoding=('|\")(?<charset>.*)\g1/iU";

        foreach ($patterns as $pattern) {
            $this->regex->setPattern($pattern);
            if ($result = $this->regex->match($fileContents)) {
                /* phpstan sees getMatch returning false|array|string */
                /** @phpstan-ignore-next-line */
                $this->setCharset($this->regex->getMatch('charset'));
                return;
            }
        }
    }
}
