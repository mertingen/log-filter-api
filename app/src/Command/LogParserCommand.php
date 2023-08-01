<?php

namespace App\Command;

use App\Entity\ServiceHttpLog;
use App\Message\Log;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'app:log-stream',
    description: 'Stream log file and save data to MySQL database',
)]
class LogParserCommand extends Command
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Stream log file and save data to MySQL database')
            ->addArgument('logFilePath', InputArgument::REQUIRED, 'Path to the log file')
            ->addOption('daemon', null, InputOption::VALUE_NONE, 'Run the command as a daemon');
    }

    /**
     * Execute the log parsing command.
     *
     * @param InputInterface $input The input interface containing the command arguments.
     * @param OutputInterface $output The output interface for displaying messages.
     *
     * @return int The exit code to be returned after command execution.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // Create a SymfonyStyle object for user interaction
            $io = new SymfonyStyle($input, $output);

            // Get the log file path from the command argument
            $logFilePath = $input->getArgument('logFilePath');
            // Get the as daemon param  from the command argument
            $daemon = (int)$input->getOption('daemon');

            // Check if the log file exists
            if (!file_exists($logFilePath)) {
                // Display an error message if the log file is not found
                $io->error('Log file not found!');
                return Command::FAILURE;
            }

            // Create a SplFileObject to read the log file
            $file = fopen($logFilePath, 'r');

            // Define the buffer size (number of lines to read at once)
            $bufferSize = 5;

            // Initialize an empty array to store the lines in the buffer
            $buffer = [];

            // Infinite loop to continuously read and process log lines
            while (true) {
                // Read lines from the file and add them to the buffer
                while (count($buffer) < $bufferSize && !feof($file)) {
                    $buffer[] = fgets($file);
                }

                // Process the lines in the buffer if it is not empty
                if (!empty($buffer) && count($buffer) > 0) {
                    $this->processBuffer($buffer);
                    $buffer = [];

                    // Introduce a short delay using usleep to avoid busy-waiting
                    // while waiting for new log lines to be written to the file.
                    // Busy-waiting consumes CPU resources unnecessarily and is generally not an efficient way to wait for events like new log entries.
                    usleep(200000);
                }

                // Check if the end of the log file is reached
                if (feof($file)) {
                    if (!$daemon) {
                        // The command execution is successful
                        return Command::SUCCESS;
                    }

                    // Go to end of the file
                    fseek($file, 0, SEEK_END);

                    // Read and process any new log lines written to the file since the last read
                    while (true) {
                        // Read a line from the file
                        $line = stream_get_line($file, 4096, PHP_EOL);

                        // If the line is not empty, process it
                        if ($line) {
                            $this->processBuffer([$line]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return Command::FAILURE;
        }

    }

    /**
     * Process the log lines in the buffer.
     *
     * @param array $buffer An array containing log lines to be processed.
     * @return void
     */
    private function processBuffer(array $buffer): void
    {
        // Iterate over each log line in the buffer
        foreach ($buffer as $line) {
            // Check if the log line matches the expected format using regular expression
            if ($this->checkFormat($line)) {
                // Split the log line into individual components
                $explodedLine = explode(" ", $line);

                // Create a DateTime object from the log line's date and time information
                $dateTime = DateTime::createFromFormat("d/M/Y:H:i:s", str_replace(["[", "]"], '', $explodedLine[3]));

                // Create a new ServiceHttpLog entity and set its properties
                $serviceHttpLog = new ServiceHttpLog();
                $serviceHttpLog->setName($explodedLine[0]);
                $serviceHttpLog->setStatusCode($explodedLine[8]);
                $serviceHttpLog->setDate($dateTime);
                $serviceHttpLog->setCreatedAt(new \DateTimeImmutable());

                // Dispatch a new Log message with the ServiceHttpLog entity to the message bus
                $this->messageBus->dispatch(new Log($serviceHttpLog));

                // Unset the serviceHttpLog variable to free up memory
                unset($serviceHttpLog);
            } else {
                // Log line format is invalid, display an error message
                echo "Log line format is invalid." . PHP_EOL;
            }
        }

        // Unset the buffer variable to free up memory
        unset($buffer);
    }

    public function checkFormat(string $line): bool
    {
        // Remove any newlines or carriage returns from the log line
        $line = str_replace(array("\r", "\n"), '', $line);
        // Define the regular expression pattern to validate the log line format
        $regexPattern = '/^[A-Z\-]+?\s+-\s+-\s+\[\d{2}\/[A-Za-z]{3}\/\d{4}:\d{2}:\d{2}:\d{2}\s\+\d{4}\]\s+"[A-Z]+\s+\/\S+\s+HTTP\/\d\.\d"\s+\d+$/';
        return preg_match($regexPattern, $line);
    }
}
