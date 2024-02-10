<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace messages;

use PHPUnit\Framework\TestCase;

/**
 * Class ParserMessagesTest
 */
class ParserMessagesTest extends TestCase
{
    /**
     * testMessages
     * @covers \pvc\parser\messages\ParserMessages.en.php
     */
    public function testMessages(): void
    {
        $messages = include(__DIR__ . '\..\..\src\messages\ParserMessages.en.php');
        self::assertIsArray($messages);
    }

}