<?php

namespace App\DataFixtures;

use App\Entity\Employee;
use App\Entity\WorkLog;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private const EMPLOYEES_FIXTURES = [
        [
            'name' => 'Laura Martínez',
            'email' => 'laura.martinez@example.com',
            'position' => 'Desarrolladora Frontend',
        ],
        [
            'name' => 'Carlos Gómez',
            'email' => 'carlos.gomez@example.com',
            'position' => 'Analista de Datos',
        ],
        [
            'name' => 'Marta Ruiz',
            'email' => 'marta.ruiz@example.com',
            'position' => 'Diseñadora UX/UI',
        ],
        [
            'name' => 'José Sánchez',
            'email' => 'jose.sanchez@example.com',
            'position' => 'Administrador de Sistemas',
        ],
        [
            'name' => 'Ana López',
            'email' => 'ana.lopez@example.com',
            'position' => 'Desarrolladora Backend',
        ],
    ];

    /**
     * @throws \DateInvalidOperationException
     * @throws \Exception
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::EMPLOYEES_FIXTURES as $employeeFixture) {
            $employee = new Employee();
            $employee->setName($employeeFixture['name']);
            $employee->setEmail($employeeFixture['email']);
            $employee->setPosition($employeeFixture['position']);
            $manager->persist($employee);
        }

        $manager->flush();

        $employeeRepository = $manager->getRepository(Employee::class);
        $employees = $employeeRepository->findAll();
        foreach ($employees as $employee) {
            for ($x = 3; $x >= 1; $x--) {
                $date = new DateTime();
                $date->sub(new DateInterval('P' . $x . 'D'));
                $startTime = $this->randomTimeBetween('06:00', '09:00')->setDate(
                    (int) $date->format('Y'),
                    (int) $date->format('m'),
                    (int) $date->format('d')
                );
                $endTime = $this->randomTimeBetween('13:00', '17:00')->setDate(
                    (int) $date->format('Y'),
                    (int) $date->format('m'),
                    (int) $date->format('d')
                );
                $interval = $startTime->diff($endTime);
                $totalHours = $interval->h + ($interval->i / 60.0);
                $workLog = new WorkLog();
                $workLog->setEmployee($employee);
                $workLog->setDate(DateTimeImmutable::createFromMutable($date));
                $workLog->setStartTime(DateTimeImmutable::createFromMutable($startTime));
                $workLog->setEndTime(DateTimeImmutable::createFromMutable($endTime));
                $workLog->setTotalHours($totalHours);
                $manager->persist($workLog);
                echo "WorkLog creado para {$employee->getName()} el {$date->format('Y-m-d')} de {$startTime->format('H:i')} a {$endTime->format('H:i')} ({$totalHours}h)\n";
            }
        }

        $manager->flush();
    }

    private function randomTimeBetween(string $startTime, string $endTime): DateTime
    {
        $start = strtotime($startTime);
        $end = strtotime($endTime);
        $randomTimestamp = rand($start, $end);
        return (new DateTime())->setTimestamp($randomTimestamp);
    }
}
