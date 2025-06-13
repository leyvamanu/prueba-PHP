# Proyecto Symfony: Gestión de Asistencia de Empleados

## Descripción
Prueba técnica que simula un sistema de fichaje para empleados. Está desarrollada como una API REST en un proyecto Symfony.

En este documento podrá encontrar la documentación necesaria para instalar el proyecto en local. También está disponible en un servidor personal para que pueda probarse sin necesidad de instalación.

En la sección de Endpoints disponibles encontrará las URLs para probarlo de ambas maneras.

---

## Requisitos previos
- PHP 8.1+
- Composer
- MySQL o base de datos compatible configurada
- Symfony CLI
- Git

---

## Instalación y arranque del proyecto
### Clona el repositorio:
```bash
git clone https://github.com/leyvamanu/prueba-PHP.git
cd prueba-PHP
```

### Instala dependencias:
```bash
composer install
```

---

### Configura la base de datos
Edita el archivo .env para establecer la conexión a la base de datos (variable DATABASE_URL).

Ejemplo:
```
DATABASE_URL="mysql://usuario:password@127.0.0.1:3306/nombre_basedatos"
```

### Crea la base de datos:

```bash
php bin/console doctrine:database:create
```

### Generar tablas y datos de prueba (opción 1);
```bash
php bin/console doctrine:migrations:migrate
```
Carga datos de prueba (fixtures):
```bash
php bin/console doctrine:fixtures:load
```

### Generear tablas y datos de prueba (opción 2);
En la carpeta dump hay un script sql con las sentencias para generar las tablas y poblarlas con datos de prueba

### Instalar un certificado de autoridad (CA) en tu sistema operativo:
>**Nota**: Este paso no es necesario si ya tienes instalado el certificado de autoridad (CA) en tu sistema operativo.
```bash
symfony server:ca:install
```

### Arranca el servidor Symfony:
```bash
symfony server:start
```
Por defecto estará disponible en http://127.0.0.1:8000.

---

## Endpoints disponibles
### 1. Registrar fichaje - POST `/asistencia/fichar/{idEmployee}`
Registra la entrada o salida del empleado en el día actual.
- Si es la primera vez que ficha en el día, se registra la hora de entrada y el total de horas se pone a 0.
- Si es la segunda vez que ficha en el mismo día, se registra la hora de salida y se calcula el total de horas como la diferencia entre la entrada y la salida.
- Si ya tiene entrada y salida registradas, devuelve error.

**Parámetros:**
- `idEmployee` (int): ID del empleado.

**Ejemplo de uso:**
```bash
curl -k -X POST "https://127.0.0.1:8000/asistencia/fichar/1" -H "Content-Type: application/json" -d "{}"
```

```bash
curl -X POST "https://pruebatecnica.manuleyva.com/asistencia/fichar/1" -H "Content-Type: application/json" -d "{}"
```

**Respuesta JSON ejemplo:**
Al fichar la entrada:
```json
{
  "empleado": "Laura Martínez",
  "acción": "Entrada registrada"
}
```
O, al fichar la salida:
```json
{
  "empleado": "Laura Martínez",
  "acción": "Salida registrada"
}
```

**Errores:**
- Si el empleado no existe, devuelve 404.
- Si ya ha fichado entrada y salida, devuelve 400 con mensaje de error.

### 2. Consultar historial - GET `/asistencia/historial/{idEmployee}?from=YYYY-MM-DD&to=YYYY-MM-DD`
- Devuelve el historial de fichajes entre dos fechas opcionales.
- from y to son parámetros query opcionales con formato YYYY-MM-DD.
- Si no se indican, devuelve todo el historial.

**Parámetros:**
- `idEmployee` (int): ID del empleado.

**Query params opcionales:**
- from: fecha inicial YYYY-MM-DD (inclusiva).
- to: fecha final YYYY-MM-DD (inclusiva).

**Ejemplo de uso:**
```bash
curl -k "https://127.0.0.1:8000/asistencia/historial/1?from=2025-06-10&to=2025-06-11"
```

```bash
curl "https://pruebatecnica.manuleyva.com/asistencia/historial/1?from=2025-06-10&to=2025-06-11"
```

**Respuesta JSON ejemplo:**
```json
{
  "empleado": "Laura Martínez",
  "historial": [
    {
      "date": "2025-05-01",
      "startTime": "08:30:00",
      "endTime": "16:30:00",
      "totalHours": 8
    }
  ]
}
```

**Errores:**
- 400 si fechas inválidas o from > to.
- 404 si empleado no existe.

### 3. Resumen mensual - GET `/asistencia/resumen/{idEmployee}/{month}/{year}`
Obtiene el total de horas trabajadas por un empleado en un mes y año específicos.

**Parámetros:**
- `idEmployee` (int): ID del empleado.
- `month` (int): mes (1-12).
- `year` (int): año (4 dígitos).

**Ejemplo de uso:**
```bash
curl -k "https://127.0.0.1:8000/asistencia/resumen/1/6/2025"
```

```bash
curl "http://pruebatecnica.manuleyva.com/asistencia/resumen/1/6/2025"
```

**Respuesta JSON ejemplo:**
```json
{
  "empleado": "Laura Martínez",
  "fecha": "6-2025",
  "horas": 160.5
}
```

**Errores:**
- 400 si mes o año inválidos o fecha futura.
- 404 si empleado no existe.

## Resumen rápido
| Endpoint | Método | Descripción  |
|--------------|--------------|--------------|
| /asistencia/fichar/{idEmployee} | POST | Registrar entrada/salida |
| /asistencia/historial/{idEmployee} | GET | Historial entre fechas opcionales |
| /asistencia/resumen/{idEmployee}/{month}/{year} | GET | Resumen total horas mensual |

## Nota
También puede probar la API en el servidor público `https://pruebatecnica.manuleyva.com` sin necesidad de instalar nada localmente.
