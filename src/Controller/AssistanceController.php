<?php

namespace App\Controller;

use App\Service\AssistanceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AssistanceController extends AbstractController
{
    public function __construct(
        readonly private AssistanceService $assistanceService,
    )
    {
    }

    #[Route('/asistencia/fichar/{idEmployee}', name: 'app_register', methods: 'POST')]
    public function register(int $idEmployee): JsonResponse
    {
        return $this->assistanceService->register($idEmployee);
    }

    /**
     * @throws \Exception
     */
    #[Route('/asistencia/historial/{idEmployee}', name: 'app_history')]
    public function history(int $idEmployee, Request $request): JsonResponse
    {
        return $this->assistanceService->getHistory($idEmployee, $request);
    }

    /**
     * @throws \Exception
     */
    #[Route('/asistencia/resumen/{idEmployee}/{month}/{year}', name: 'app_summary', requirements: [
        'idEmployee' => '\d+',
        'month' => '0?[1-9]|1[0-2]',
        'year' => '\d{4}'
    ])]
    public function summary(int $idEmployee, int $month, int $year): JsonResponse
    {
        return $this->assistanceService->getSummary($idEmployee, $month, $year);
    }
}
