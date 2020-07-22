<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\parser\numeric\NumberParser\parsers;

use Mockery;
use NumberFormatter;
use PHPUnit\Framework\TestCase;
use pvc\intl\Locale;
use pvc\msg\UserMsg;
use pvc\parser\numeric\NumberParser\configurations\NumberParserConfiguration;
use pvc\parser\numeric\NumberParser\core\NumberFormatterConfiguration;
use pvc\parser\numeric\NumberParser\parsers\err\CalcNumDecimalPlacesException;
use pvc\parser\numeric\NumberParser\parsers\NumberParser;
use pvc\parser\numeric\NumberParser\precision\NumberParserPrecisionRangeNonNegative;
use pvc\testingTraits\PrivateMethodTestingTrait;

class NumberParserTest extends TestCase
{

    use PrivateMethodTestingTrait;

    protected Locale $locale;
    protected int $style;
    protected int $type;

    /** @phpstan-ignore-next-line */
    protected $config;

    /** @phpstan-ignore-next-line */
    protected $precisionRange;

    protected NumberFormatter $frmtr;
    protected NumberParser $parser;

    /**
     * This rather complicated setUp is designed so that the testing of child classes can simply extend this class
     * and override a couple of methods.  Specifically, the methods that are overridden in the child classes are
     * setFormatterStyle, setFormatterTYpe (setting the style of formatter and the data type of the return value
     * once parsed) and instantiateParser (which creates the actual instance of the parser to be tested).
     */

    public function setUp(): void
    {
        $this->locale = new Locale('de_DE');

        $this->style = NumberFormatter::DECIMAL;
        $this->type = NumberFormatter::TYPE_DOUBLE;

        $this->frmtr = new NumberFormatter($this->locale, $this->style);

        $nfc1 = Mockery::mock(NumberFormatterConfiguration::class);
        $nfc1->shouldReceive('configure')->with(Mockery::type('\NumberFormatter'));

        $nfc2 = Mockery::mock(NumberFormatterConfiguration::class);
        $nfc2->shouldReceive('configure')->with(Mockery::type('\NumberFormatter'));

        $this->config = Mockery::mock(NumberParserConfiguration::class);
        $this->config->shouldReceive('getNumberFormatter')->with($this->locale)->andReturn($this->frmtr);
        $this->config->shouldReceive('getFormatterType')->withNoArgs()->andReturn($this->type);
        $this->config->shouldReceive('getFormatterStyle')->withNoArgs()->andReturn($this->style);
        $this->config->shouldReceive('getNumberFormatterConfiguration')->withNoArgs()->andReturn([$nfc1, $nfc2]);

        $this->precisionRange = Mockery::mock(NumberParserPrecisionRangeNonNegative::class);
        // expectations for the precisionRange are set on a test by test basis

        $this->parser = new NumberParser($this->locale, $this->config, $this->precisionRange);
    }

    public function testConstructSetGetConfigurationLocalePrecisionRange() : void
    {
        self::assertSame($this->config, $this->parser->getConfiguration());
        self::assertEquals($this->locale, $this->parser->getLocale());
        self::assertEquals($this->precisionRange, $this->parser->getPrecisionRange());
    }

    public function testParseValueFail() : void
    {
        // this fails the main parse and precision is never tested, no expectations need to be set
        $value = '123ab';
        $result = $this->parser->parseValue($this->frmtr, $value);
        self::assertFalse($result);
    }

    /**
     * @function testCalcNumDecimalPlaces
     * @param string $input
     * @param int $expectedResult
     * @dataProvider numberProvider
     */
    public function testCalcNumDecimalPlaces(string $input, int $expectedResult) : void
    {
        $actualResult = $this->invokeMethod($this->parser, 'calcNumDecimalPlaces', ['.', $input]);
        self::assertEquals($expectedResult, $actualResult);
    }

    public function numberProvider() : array
    {
        return [
            '12345' => ['12345', -1],
            '0' => ['0', -1],
            '12345.' => ['12345.', 0],
            '12345.1' => ['12345.1', 1],
            '12345.123' => ['12345.123', 3],
        ];
    }

    public function testCalcNumDecimalPlacesException() : void
    {
        self::expectException(CalcNumDecimalPlacesException::class);
        $input = '123.45.678';
        $this->invokeMethod($this->parser, 'calcNumDecimalPlaces', ['.', $input]);
    }


    public function testParseValueSucceed() : void
    {
        // remember - this is Germany!  decimal separator is a comma.
        $value = '123,456';
        // precisionRange will return allow this one to go through
        $this->precisionRange->shouldReceive('containsValue')->with(3)->andReturn(true);

        $result = $this->parser->parseValue($this->frmtr, $value);
        self::assertTrue($result);
        self::assertEquals(123.456, $this->parser->getParsedValue());
    }

    public function testParseFail() : void
    {
        $value = '123ab';
        self::assertFalse($this->parser->parse($value));
        $msg = $this->parser->getErrmsg() ?: new UserMsg();
        $vars = $msg->getMsgVars();
        self::assertEquals($value, $vars[0]);
    }

    public function testParseSucceed() : void
    {
        $value = '123,456';
        $this->precisionRange->shouldReceive('containsValue')->with(3)->andReturn(true);
        self::assertTrue($this->parser->parse($value));
        self::assertEquals(123.456, $this->parser->getParsedValue());
        self::assertEquals(3, $this->parser->getNumDecimalPlaces());
    }
}
