<?php

namespace App\Utils;

final class AssistanceMessages
{
    public const REGISTER_START = 'Fichar entrada';
    public const REGISTER_END = 'Fichar salida';
    public const ERROR_ALREADY_REGISTERED = 'Hoy ya has fichado tanto a la entrada como a la salida';
    public const ERROR_INVALID_FROM_DATE = 'Fecha "desde" debe ser una fecha correcta con formato YYYY-MM-DD';
    public const ERROR_INVALID_TO_DATE = 'Fecha "hasta" debe ser una fecha correcta con formato YYYY-MM-DD';
    public const ERROR_FROM_AFTER_TO = 'La fecha "from" debe ser anterior o igual a la fecha "to".';
    public const ERROR_INVALID_MONTH = 'El mes debe estar entre 1 y 12.';
    public const ERROR_INVALID_YEAR = 'El año debe tener 4 cifras.';
    public const ERROR_FUTURE_DATE = 'No se pueden consultar horas de meses futuros.';
    public const ERROR_EMPLOYEE_NOT_FOUND = 'Empleado no encontrado';
}
