<?php

namespace Grasmash\Tests;

use Grasmash\SymfonyConsoleSpinner\Checklist;
use PHPUnit\Framework\TestCase;

final class ChecklistTest extends TestCase
{

    public function testChecklist(): void
    {
        $output = new BufferedConsoleOutput();
        $output_stream_filename = __DIR__ . '/../../build/test_stream';
        $output->setOutputStreamFilename($output_stream_filename);
        $checklist = new Checklist($output);

        $message = 'First item!';
        $checklist->addItem($message);
        $items = $checklist->getItems();
        $this->assertEquals($items[0]['message'], $message);
        // Validate that checklist item text present and marked as in progress via an hour glass.
        $this->assertStringContainsString('    ⌛ First item!...', $output->getConsoleSectionVisibleOutput());

        $update_message = 'Still working on it';
        $checklist->updateProgressBar($update_message);
        // Validate that checklist item text is still present.
        $this->assertStringContainsString($message, $output->getConsoleSectionVisibleOutput());
        // Validate that checklist item detail is displayed.
        $this->assertStringContainsString($update_message, $output->getConsoleSectionVisibleOutput());

        $checklist->completePreviousItem();
        $output_stream_contents = file_get_contents($output_stream_filename);

        // Validate that the section detail was cleared.
        $this->assertEquals('', $output->getConsoleSectionVisibleOutput());
        // Validate that the section was marked as complete.
        $this->assertStringContainsString('✔', $output_stream_contents);
    }
}

