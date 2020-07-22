<?php declare(strict_types = 1);

namespace pvc\parser\dom;

use DOMDocument;
use finfo;
use pvc\err\throwable\exception\pvc_exceptions\InvalidArrayValueException;
use pvc\err\throwable\exception\stock_rebrands\InvalidArgumentException;
use pvc\intl\Charset;
use pvc\parser\dom\err\InvalidMarkupLanguageException;
use pvc\parser\dom\err\UnknownMarkupLanguageMsg;
use pvc\parser\Parser;
use pvc\parser\ParserInterface;
use pvc\regex\err\RegexBadPatternException;
use pvc\regex\err\RegexInvalidMatchIndexException;
use pvc\regex\err\RegexPatternUnsetException;
use pvc\xml\LibXmlWrapper\LibXmlErrorHandler;
use pvc\xml\LibXmlWrapper\LibXmlExecutionEnvironment;

/*
 * ParserDom parses text into a DOMDocument object.
 *
 * ParserDOM leverages the base capabilities in the php built in class DOMDocument and adds a few additional
 * methods to make parsing easier to control and error handling smoother.
 *
 * 'Parsing' means simply being able to 'load' a string into PHP's DOMDocument object.
 * It will not validate the document against a DTD or schema - that is the job of the class ValidatorDOM.
 *
 * In the spirit of interpreting html pages in a forgiving manner, the LibXML parser can "succeed" in parsing
 * a document even when there are errors.  LibXML has three error levels: fatal, recoverable and warning.
 * The parser will fail if it hits a fatal error, but succeed with recoverable errors and warnings.
 *
 * This class gives a bit more control by allowing you to set which error level constitutes a failure.
 * You can set the failure level to LibXmlErrorHandler::REPORT_RECOVERABLE_ERRORS and you will succeed if your
 * document has only warnings and no errors (fatal or recoverable).  Alternatively, you can set the failure
 * threshhold to LibXmlErrorHandler::REPORT_WARNINGS and your document will have to parse perfectly cleanly
 * in order to succeed (no errors or warnings).  Of course, a fatal error will always fail no matter what
 * threshold you have set.
 *
 * Class ParserDOM
 */

class ParserDOM extends Parser implements ParserInterface
{
    /**
     * @var string|null
     */
    protected ? string $markupLanguage;

    /**
     * @var string
     */
    protected string $mediaType;

    /**
     * @var Charset|null
     */
    protected ? Charset $charset;

    /**
     * @var array|string[]
     */
    protected array $validMarkupLanguages;

    /*
     * @var $domDoc DOMDocument
     * variable holding the DOMDocument instance used throughout the class
     */
    private DOMDocument $domDoc;

    /**
     * @var LibXmlErrorHandler
     */
    private LibXmlErrorHandler $errorHandler;


    /**
     * ParserDOM constructor.
     * @throws InvalidArgumentException
     */
    public function __construct()
    {
        $this->domDoc = new DOMDocument();
        $this->validMarkupLanguages = ['xml', 'html'];
        $this->errorHandler = new LibXmlErrorHandler();
        $this->errorHandler->setFailureThreshold(LIBXML_ERR_WARNING);
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
     * @function setMarkupLanguage
     * @param string $markupLanguage
     * @throws InvalidMarkupLanguageException
     */
    public function setMarkupLanguage(string $markupLanguage): void
    {
        $ml = strtolower($markupLanguage);
        if (!in_array($ml, $this->validMarkupLanguages)) {
            throw new InvalidMarkupLanguageException();
        }
        $this->markupLanguage = $markupLanguage;
    }

    /**
     * @function getMediaType
     * @return string
     */
    public function getMediaType(): string
    {
        return $this->mediaType;
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
     * @function setFailureThreshold
     * @param int $failureThreshold
     * @throws InvalidArgumentException
     */
    public function setFailureThreshold(int $failureThreshold): void
    {
        $this->errorHandler->setFailureThreshold($failureThreshold);
    }

    /**
     * @function getFailureThreshold
     * @return int
     */
    public function getFailureThreshold(): int
    {
        return $this->errorHandler->getFailureThreshold();
    }

    /**
     * @function getErrorHandler
     * @return LibXmlErrorHandler
     */
    public function getErrorHandler(): LibXmlErrorHandler
    {
        return $this->errorHandler;
    }

    /**
     * 'Parses' an HTML or an XML string into a DOMDocument.
     *
     * If the load method has already been set, the this function will not try and override it by inspecting
     * the document.  If the load method has not been set, then this function will try and discover the doctype
     * from the string and set the load method automatically before parsing.  If the load method is not set and the
     * function cannot determine the doctype we throw an exception.
     *
     * @function parse
     * @param string $fileContents
     * @return bool
     * @throws InvalidArrayValueException
     * @throws RegexBadPatternException
     * @throws RegexInvalidMatchIndexException
     * @throws RegexPatternUnsetException
     */
    public function parse(string $fileContents): bool
    {
        $ms = new MarkupSniffer();
        $ms->sniff($fileContents);

        if (!isset($this->markupLanguage)) {
            if (!$ms->getMarkupLanguage()) {
                $this->errmsg = new UnknownMarkupLanguageMsg();
                return false;
            } else {
                $this->markupLanguage = $ms->getMarkupLanguage();
            }
        }

        $env = new LibXmlExecutionEnvironment();
        $method = ($this->markupLanguage == 'xml' ? 'loadXML' : 'loadHTML');
        $params = [$fileContents];

        /*
         * the result of the load method(s) is true if the parser actually manages to
         * populate the DOMDocument, but "success" in this routine is dictated by the
         * failure threshold set in the error handler.  Regardless of whether the parse
         * "succeeds" or not, error messagesFilter from the errorHandler are use to populate
         * the error message attribute in this class.  Because of the complex nature of
         * success versus failure, the errmsg attribute is set to null so that even under a
         * "success" scenario, we can see if there are any errors / warnings coming up
         * from LibXML.*
         */

        /** @phpstan-ignore-next-line */
        $env->executeCallable([$this->domDoc, $method], $params);

        if (0 < count($errors = $env->getErrors())) {
            $this->errorHandler->setErrors($errors);
            $this->setErrmsg($this->errorHandler->getMsgCollection());

            $result = ($this->errorHandler->errorsExceedThreshold() ? false : true);
        } else {
            $this->setErrmsg(null);
            $result = true;
        }

        if ($result == true) {
            $this->setMediaTypeFromSource($fileContents);
            $this->charset = $ms->getCharset();
            $this->setParsedValue($this->domDoc);
        }
        return $result;
    }

    /**
     * @function setMediaTypeFromSource
     * @param string $source
     */
    protected function setMediaTypeFromSource(string $source): void
    {
        $finfo = new finfo(FILEINFO_MIME);
        if (false !== ($mediaType = $finfo->buffer($source))) {
            $this->mediaType = $mediaType;
        }
    }
}
