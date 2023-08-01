<?php

namespace App\Service;

use App\Repository\ServiceHttpLogRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class LogService
{
    public function __construct(
        private readonly ServiceHttpLogRepository $serviceHttpLogRepository
    )
    {

    }

    /**
     * Get the count of rows that match the filter criteria.
     *
     * @param array $data An array containing the filter criteria.
     *                  Possible keys: 'serviceNames', 'statusCode', 'startDate', 'endDate'.
     *
     * @return int The count of rows that match the filter criteria.
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getCount(array $data): int
    {
        // Create a query builder for the ServiceHttpLog entity
        $queryBuilder = $this->serviceHttpLogRepository->createQueryBuilder('shl');

        // Add a condition to filter by service names if 'serviceNames' is provided
        if (!empty($data['serviceNames'])) {
            $queryBuilder->andWhere('shl.name IN (:serviceNames)')
                ->setParameter('serviceNames', $data['serviceNames']);
        }

        // Add a condition to filter by status code if 'statusCode' is provided
        if ($data['statusCode'] !== null) {
            $queryBuilder->andWhere('shl.statusCode = :statusCode')
                ->setParameter('statusCode', $data['statusCode']);
        }

        // Add a condition to filter by start date if 'startDate' is provided
        if ($data['startDate'] !== null) {
            $queryBuilder->andWhere('shl.date >= :startDate')
                ->setParameter('startDate', new \DateTime($data['startDate']));
        }

        // Add a condition to filter by end date if 'endDate' is provided
        if ($data['endDate'] !== null) {
            $queryBuilder->andWhere('shl.date <= :endDate')
                ->setParameter('endDate', new \DateTime($data['endDate']));
        }

        // Build the query and get the count of rows that match the filter criteria
        return $queryBuilder->select('COUNT(shl.id)')->getQuery()->getSingleScalarResult();
    }

}