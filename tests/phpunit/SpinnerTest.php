<?php

namespace Grasmash\Tests;

use Grasmash\SymfonyConsoleSpinner\Spinner;
use PHPUnit\Framework\TestCase;

final class SpinnerTest extends TestCase
{

    public function testSpinner(): void
    {
        $output = new BufferedConsoleOutput();
        $output->setOutputStreamFilename(__DIR__ . '/../../build/test_stream');
        $spinner = new Spinner($output);

        $spinner->start();
        $this->assertStringContainsString('⌛', $output->getConsoleSectionVisibleOutput());

        // .05 seconds.
        usleep(50000);
        $spinner->advance();
        $display = $output->getConsoleSectionVisibleOutput();
        // Validate that a spinner character has replaced the initial hourglass.
        $this->assertStringNotContainsString('⌛', $display);
        $character = mb_substr($display, 11, 1);
        $this->assertContains($character, Spinner::CHARS);
    }
}

