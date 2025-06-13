<?php

namespace App\Repository;

use App\Entity\WorkLog;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<WorkLog>
 */
class WorkLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkLog::class);
    }

    /**
     * @throws \Exception
     */
    public function getHistory(int $idEmployee, ?string $from, ?string $to): array
    {
        $qb = $this->createQueryBuilder('wl')
            ->andWhere('wl.employee = :employee')
            ->setParameter('employee', $idEmployee);

        if ($from !== null) {
            $qb->andWhere('wl.date >= :fromDate')
                ->setParameter('fromDate', new DateTimeImmutable($from));
        }

        if ($to !== null) {
            $qb->andWhere('wl.date <= :toDate')
                ->setParameter('toDate', new DateTimeImmutable($to));
        }

        $worklogs = $qb->getQuery()->getResult();

        return array_map(fn($worklog) => [
            'id' => $worklog->getId(),
            'date' => $worklog->getDate()->format('Y-m-d'),
            'startTime' => $worklog->getStartTime()->format('H:i:s'),
            'endTime' => $worklog->getEndTime()->format('H:i:s'),
            'totalHours' => $worklog->getTotalHours(),
        ], $worklogs);
    }

    /**
     * @throws \Exception
     */
    public function getTotalHours(int $idEmployee, int $month, int $year): float
    {
        $startDate = new DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month));
        $endDate = $startDate->modify('first day of next month');

        $qb = $this->createQueryBuilder('wl')
            ->select('SUM(wl.totalHours) as totalHours')
            ->andWhere('wl.employee = :employeeId')
            ->andWhere('wl.date >= :startDate')
            ->andWhere('wl.date < :endDate')
            ->setParameter('employeeId', $idEmployee)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->getQuery();

        $result = $qb->getOneOrNullResult();

        return $result['totalHours'] ?? 0;
    }
}
