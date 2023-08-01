<?php

namespace App\MessageHandler;

use App\Entity\ServiceHttpLog;
use App\Repository\ServiceHttpLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class Log
{
    public function __construct(private readonly EntityManagerInterface $entityManager, private readonly ServiceHttpLogRepository $serviceHttpLogRepository)
    {
    }

    /**
     * Handles the log message.
     *
     * @param \App\Message\Log $log The log message
     * @return void
     */
    public function __invoke(\App\Message\Log $log): void
    {
        try {
            // Get the ServiceHttpLog entity from the log message
            $serviceHttpLog = $log->getServiceHttpLog();

            // Create a unique hash for the ServiceHttpLog entity based on its name, date, and status code
            $hash = md5($serviceHttpLog->getName() . $serviceHttpLog->getDate()->format("Y-m-d H:i:s") . $serviceHttpLog->getStatusCode());

            // Check if a ServiceHttpLog entity with the same hash already exists
            $isExist = $this->serviceHttpLogRepository->findOneBy(['hash' => $hash]);

            // If the entity does not exist, set the hash and persist it to the database
            if (!$isExist instanceof ServiceHttpLog) {
                $serviceHttpLog->setHash($hash);
                $this->entityManager->persist($log->getServiceHttpLog());
                $this->entityManager->flush();
            }
        } catch (\Exception $e) {
            dump("[ERROR]: " . $e->getMessage());
        }

    }
}