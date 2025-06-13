<?php

namespace App\Service;

use App\Entity\Employee;
use App\Entity\WorkLog;
use App\Repository\EmployeeRepository;
use App\Repository\WorkLogRepository;
use App\Utils\AssistanceMessages;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

readonly class AssistanceService
{
    public function __construct(
        private EmployeeRepository     $employeeRepository,
        private WorkLogRepository      $workLogRepository,
        private EntityManagerInterface $manager,
    )
    {
    }

    public function register($idEmployee): JsonResponse
    {
        $employee = $this->checkIfEmployeeExist($idEmployee);

        $date = new DateTimeImmutable();
        $workLog = $this->workLogRepository->findOneBy([
            'employee' => $idEmployee,
            'date' => $date,
        ]);

        if (!$workLog) {
            $workLog = new WorkLog();
            $workLog->setEmployee($employee);
            $workLog->setDate($date);
            $workLog->setStartTime($date);
            $workLog->setTotalHours(0);
            $action = AssistanceMessages::REGISTER_START;
        } else {
            if ($workLog->getEndTime()) {
                throw new BadRequestHttpException(AssistanceMessages::ERROR_ALREADY_REGISTERED);
            }
            $workLog->setEndTime($date);
            $interval = $workLog->getStartTime()->diff($date);
            $totalHours = $interval->h + ($interval->i / 60.0);
            $workLog->setTotalHours($totalHours);
            $action = AssistanceMessages::REGISTER_END;
        }

        $this->manager->persist($workLog);
        $this->manager->flush();

        return new JsonResponse([
            'empleado' => $employee->getName(),
            'acción' => $action
        ]);
    }

    /**
     * @throws \Exception
     */
    public function getHistory(int $idEmployee, Request $request): JsonResponse
    {
        $employee = $this->checkIfEmployeeExist($idEmployee);

        $from = $request->query->get('from');
        $to = $request->query->get('to');

        $fromDate = $this->parseAndValidateDate($from, AssistanceMessages::ERROR_INVALID_FROM_DATE);
        $toDate = $this->parseAndValidateDate($to, AssistanceMessages::ERROR_INVALID_TO_DATE);

        if ($fromDate && $toDate && $fromDate > $toDate) {
            throw new BadRequestHttpException(AssistanceMessages::ERROR_FROM_AFTER_TO);
        }

        $history = $this->workLogRepository->getHistory($idEmployee, $from, $to);

        return new JsonResponse([
            'empleado' => $employee->getName(),
            'historial' => $history,
        ]);
    }

    /**
     * @throws \Exception
     */
    public function getSummary(int $idEmployee, int $month, int $year): JsonResponse
    {
        $employee = $this->checkIfEmployeeExist($idEmployee);

        if ($month < 1 || $month > 12) {
            throw new BadRequestHttpException(AssistanceMessages::ERROR_INVALID_MONTH);
        }

        if (!preg_match('/^\d{4}$/', (string)$year)) {
            throw new BadRequestHttpException(AssistanceMessages::ERROR_INVALID_YEAR);
        }

        $now = new DateTimeImmutable();

        $currentMonth = (int) $now->format('n');
        $currentYear = (int) $now->format('Y');

        if ($year > $currentYear || ($year === $currentYear && $month > $currentMonth)) {
            throw new BadRequestHttpException(AssistanceMessages::ERROR_FUTURE_DATE);
        }

        $hours = $this->workLogRepository->getTotalHours($idEmployee, $month, $year);

        return new JsonResponse([
            'empleado' => $employee->getName(),
            'fecha' => $month . '-' . $year,
            'horas' => $hours,
        ]);
    }

    /**
     * @param int $idEmployee
     * @return Employee
     */
    public function checkIfEmployeeExist(int $idEmployee): Employee
    {
        $employee = $this->employeeRepository->find($idEmployee);

        if (!$employee) {
            throw new NotFoundHttpException(AssistanceMessages::ERROR_EMPLOYEE_NOT_FOUND);
        }
        return $employee;
    }

    private function parseAndValidateDate(?string $value, string $errorMessage): ?DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        if (!$date || $date->format('Y-m-d') !== $value) {
            throw new BadRequestHttpException($errorMessage);
        }

        return $date;
    }
}
