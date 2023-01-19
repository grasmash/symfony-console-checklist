# Symfony Console Spinner

This utility provides two tools for use with Symfony Console:
1. An animated spinner class. This is a wrapper around Symfony's built-in Progress Bar which will show colored, animated spinner. It requires advance() to be called in order for the spinner to spin.
2. A checklist class. This is a wrapper around the Spinner. It allows you to emit a checklist item, display a spinner next to it to indicate that it is in progress, and write a "message detail" under the item.

## Usage

### Simple Spinner
```php
$output = new \Symfony\Component\Console\Output\ConsoleOutput();
$spinner = new Spinner($output);
$spinner->setMessage('Fetching a really big file from far away');
$spinner->start();
while (getting_the_file()) {
    $spinner->advance();
}
$spinner->finish()
```

### Simple Checklist
```php
$output = new \Symfony\Component\Console\Output\ConsoleOutput();
$checklist = new Checklist($output);
$checklist->addItem('Fetching a really big file from far away');
while (getting_the_file()) {
    $checklist->updateProgressBar('Still getting the file');
}
$checklist->completePreviousItem();

$checklist->addItem('Doing the next thing');
```

### Advanced Checklist Example

```php
  use Symfony\Component\Process\Process;
  use Symfony\Component\Console\Output\ConsoleOutput;
  
  public function runMyCommand() {
    $output = new ConsoleOutput();
    $checklist = new Checklist($output);
    $checklist->addItem('Running a command with lots of output');

    $process = new Process([
      'composer',
      'run-script',
      'my-script',
      '--no-interaction',
    ]);
    $process->start();
    $process->wait(function ($type, $buffer) use ($checklist, $output) {
      if (!$output->isVerbose() && $checklist->getItems()) {
        $checklist->updateProgressBar($buffer);
      }
      $output->writeln($buffer, OutputInterface::VERBOSITY_VERY_VERBOSE);
    });
    if (!$process->isSuccessful()) {
      throw new \Exception('Something went wrong! {message}' . $process->getErrorOutput());
    }

    $checklist->completePreviousItem();
  }
```