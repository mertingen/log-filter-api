<?php

namespace App\Tests;

use App\Command\LogParserCommand;
use App\Repository\ServiceHttpLogRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\MessageBusInterface;

class LogParserCommandTest extends WebTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testExecuteWithInvalidLogFile()
    {
        // Create a mock for the MessageBusInterface
        $messageBusMock = $this->getMockBuilder(MessageBusInterface::class)->getMock();

        $command = new LogParserCommand($messageBusMock);
        $commandTester = new CommandTester($command);

        // Run the command with an invalid log file path
        $exitCode = $commandTester->execute([
            'logFilePath' => '/path/to/nonexistent/services_test.log',
            '--daemon',
        ]);

        $this->assertEquals(Command::FAILURE, $exitCode);
        $this->assertStringContainsString('Log file not found!', $commandTester->getDisplay());
    }

    public function testCheckFormatValidLines()
    {
        // Create a mock for the MessageBusInterface
        $messageBusMock = $this->getMockBuilder(MessageBusInterface::class)->getMock();

        $command = new LogParserCommand($messageBusMock);

        $validLines = [
            'USER-SERVICE - - [18/Aug/2018:10:33:56 +0000] "POST /users HTTP/1.1" 201',
            'USER-SERVICE - - [18/Aug/2018:10:34:59 +0000] "POST /users HTTP/1.1" 201',
            'INVOICE-SERVICE - - [18/Aug/2018:11:27:53 +0000] "POST /invoices HTTP/1.1" 201',
            'INVOICE-SERVICE - - [18/Aug/2018:11:28:53 +0000] "POST /invoices HTTP/1.1" 201',
            'INVOICE-SERVICE - - [18/Aug/2018:11:39:53 +0000] "POST /invoices HTTP/1.1" 201',
        ];

        foreach ($validLines as $line) {
            $result = $command->checkFormat($line);
            $this->assertTrue($result, "Line '$line' should be considered valid.");
        }
    }

    public function testCheckFormatInvalidLines()
    {
        // Create a mock for the MessageBusInterface
        $messageBusMock = $this->getMockBuilder(MessageBusInterface::class)->getMock();

        $command = new LogParserCommand($messageBusMock);

        $invalidLines = [
            'test',
            '124',
            'INVOICE-SERVICE - - [18/Aug/2018:11:27:53 +0000] "POST',
            'INVOICE-SERVICE - - "POST /invoices HTTP/1.1" 201',
            '[18/Aug/2018:11:39:53 +0000] "POST /invoices HTTP/1.1" 201',
        ];

        foreach ($invalidLines as $line) {
            $result = $command->checkFormat($line);
            $this->assertFalse($result, "Line '$line' should be considered invalid.");
        }
    }

    public function testServiceHttpLogFixtures()
    {
        $container = self::getContainer();
        $serviceHttpLogRepository = $container->get(ServiceHttpLogRepository::class);

        $logs = $serviceHttpLogRepository->findAll();
        $this->assertCount(1, $logs);

    }

    public function testCountEndpoint()
    {

        $client = static::createClient();
        // Send a GET request to the /count endpoint
        $response = $client->request('GET', '/count');

        // Assert that the response is successful (HTTP status code 200)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Assert that the response is in JSON format
        $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));

        // Decode the JSON response
        $data = json_decode($client->getResponse()->getContent(), true);

        // Assert that the response contains the expected "counter" key
        $this->assertArrayHasKey('counter', $data);

        // Assert that the "counter" value is an integer
        $this->assertIsInt($data['counter']);
    }
}