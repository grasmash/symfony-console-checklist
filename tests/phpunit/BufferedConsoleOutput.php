<?php

namespace Grasmash\Tests;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Symfony\Component\Filesystem\Filesystem;

/**
 * This is a combination of put and ConsoleOutput.
 *
 * It allows us to capture output in a buffer for PHPUnit assertions and
 * simultaneously print output to the console for debugging.
 *
 * @see \Symfony\Component\Console\Output\put
 * @see \Symfony\Component\Console\Output\ConsoleOutput
 */
class BufferedConsoleOutput extends ConsoleOutput {
    private array $consoleSectionOutputs = [];
    const ENV_VAR_KEY = 'phpunit_buffer_output';
    private string $outputStreamFilename;

    /**
     * Empties buffer and returns its content.
     *
     * @return string
     *   Contents of buffer
     */
    public function fetch() {
        $content = (string) getenv(self::ENV_VAR_KEY);
        putenv(self::ENV_VAR_KEY . '=');

        return $content;
    }

    /**
     * {@inheritdoc}
     */
    protected function doWrite($message, $newline) {
        $output = getenv(self::ENV_VAR_KEY);

        if ($newline) {
            putenv(self::ENV_VAR_KEY . '=' . $output . $message . PHP_EOL);
        }
        else {
            putenv(self::ENV_VAR_KEY . '=' . $output . $message);
        }

        parent::doWrite($message, $newline);
    }

    /**
     * Creates a new output section.
     */
    public function section(): ConsoleSectionOutput
    {
        $stream =  fopen($this->outputStreamFilename, 'w');
        return new ConsoleSectionOutput($stream, $this->consoleSectionOutputs, $this->getVerbosity(), $this->isDecorated(), $this->getFormatter());
    }

    public function getConsoleSectionVisibleOutput() {
        return $this->consoleSectionOutputs[0]->getVisibleContent();
    }

    public function setOutputStreamFilename(string $filename) {
        $this->outputStreamFilename = $filename;
    }


}