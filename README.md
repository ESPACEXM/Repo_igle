# 🎵 Sistema de Gestión del Ministerio de Alabanza

Sistema integral para la administración del ministerio de alabanza de iglesias, desarrollado con Laravel 12, Livewire 3 y Tailwind CSS. Permite gestionar eventos, ensayos, miembros, canciones, instrumentos y enviar recordatorios automáticos por WhatsApp.

![Laravel](https://img.shields.io/badge/Laravel-12.x-red.svg)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-blue.svg)
![Livewire](https://img.shields.io/badge/Livewire-3.x-pink.svg)
![Tailwind](https://img.shields.io/badge/Tailwind-4.x-cyan.svg)

---

## 📋 Tabla de Contenidos

- [Características](#-características)
- [Stack Tecnológico](#-stack-tecnológico)
- [Requisitos](#-requisitos)
- [Instalación](#-instalación)
- [Configuración de WhatsApp](#-configuración-de-whatsapp)
- [Configuración del Cron Job](#-configuración-del-cron-job)
- [Estructura de Directorios](#-estructura-de-directorios)
- [Comandos Útiles](#-comandos-útiles)
- [Funcionalidades del Sistema](#-funcionalidades-del-sistema)

---

## ✨ Características

- ✅ **Gestión de Miembros**: Registro con roles (líder/miembro), instrumentos asignados y datos de contacto
- ✅ **Gestión de Eventos**: Creación de cultos, reuniones con fecha, hora, ubicación y descripción
- ✅ **Ensayos Programados**: Vinculación de ensayos a eventos específicos
- ✅ **Cancionero Digital**: Base de datos de canciones con letra, acordes, notas y enlaces
- ✅ **Control de Instrumentos**: Inventario de instrumentos musicales
- ✅ **Asistencias**: Registro de asistencias a eventos y ensayos
- ✅ **Recordatorios WhatsApp**: Envío automático de recordatorios a las 9:00 AM (zona Guatemala)
- ✅ **Dashboard Visual**: Interfaz moderna con diseño glassmorphism
- ✅ **Sistema de Roles**: Middleware de autenticación y control de acceso

---

## 🛠 Stack Tecnológico

| Tecnología | Versión | Descripción |
|------------|---------|-------------|
| **Laravel** | 12.x | Framework PHP moderno y elegante |
| **PHP** | 8.2+ | Lenguaje de programación del servidor |
| **Livewire** | 3.x | Framework full-stack para componentes dinámicos |
| **Tailwind CSS** | 4.x | Framework CSS utilitario |
| **SQLite** | 3.x | Base de datos ligera (configurable a MySQL/PostgreSQL) |
| **Vite** | 6.x | Build tool moderno para assets |

---

## 📦 Requisitos

- PHP >= 8.2
- Composer
- Node.js >= 18
- NPM o Yarn
- Extensión PHP: `sqlite3`, `pdo_sqlite`, `mbstring`, `openssl`, `json`

---

## 🚀 Instalación

### 1. Clonar el Repositorio

```bash
git clone https://github.com/tu-usuario/iglesia-alabanza.git
cd iglesia-alabanza
```

### 2. Instalar Dependencias

```bash
# Instalar dependencias PHP
composer install

# Instalar dependencias Node.js
npm install
```

### 3. Configurar Entorno

```bash
# Copiar archivo de configuración
copy .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

### 4. Configurar Base de Datos

```bash
# Ejecutar migraciones
php artisan migrate

# (Opcional) Cargar datos de demostración
php artisan db:seed --class=DemoSeeder
```

### 5. Compilar Assets

```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### 6. Iniciar Servidor

```bash
php artisan serve
```

La aplicación estará disponible en: `http://localhost:8000`

---

## 📱 Configuración de WhatsApp

El sistema utiliza **CallMeBot API** para enviar mensajes de WhatsApp.

### Paso 1: Obtener API Key de CallMeBot

1. Abre WhatsApp en tu teléfono
2. Envía un mensaje con la palabra `start` a: **+34 644 52 59 89**
3. Recibirás un mensaje con tu API Key
4. Guarda esta API Key en tu archivo `.env`

### Paso 2: Configurar Variables de Entorno

```env
# WhatsApp (CallMeBot)
WHATSAPP_API_KEY=tu_api_key_aqui
WHATSAPP_ENABLED=true
```

### Paso 3: Formato de Números

Los números deben tener el formato internacional **sin el signo +**:
- ✅ Correcto: `50241234567` (Guatemala)
- ✅ Correcto: `5215512345678` (México)
- ❌ Incorrecto: `+50241234567`

### Paso 4: Probar Envío

```bash
# Ejecutar comando de recordatorios en modo simulación
php artisan events:send-reminders --dry-run

# Enviar mensaje de prueba manual
php artisan tinker
>>> $service = new \App\Services\WhatsAppService();
>>> $service->sendMessage('50241234567', '🎵 Prueba del sistema de alabanza');
```

---

## ⏰ Configuración del Cron Job

El sistema incluye recordatorios automáticos que se ejecutan diariamente a las **9:00 AM (hora Guatemala, UTC-6)**.

### Windows (Task Scheduler)

1. Abrir "Programador de Tareas" (Task Scheduler)
2. Crear nueva tarea básica:
   - **Nombre**: Laravel Schedule Runner
   - **Descripción**: Ejecutar schedule:run cada minuto
3. Trigger: "Al iniciar sesión" o "Diariamente" cada 1 minuto
4. Acción: "Iniciar un programa"
5. Configuración:
   - **Programa/script**: `php`
   - **Argumentos**: `artisan schedule:run`
   - **Iniciar en**: `C:\ruta\al\proyecto`

### Linux/Mac (Cron)

```bash
# Editar crontab
crontab -e

# Agregar línea (ejecutar cada minuto)
* * * * * cd /ruta/al/proyecto && php artisan schedule:run >> /dev/null 2>&1
```

### Verificar Scheduler

```bash
# Listar tareas programadas
php artisan schedule:list

# Ejecutar manualmente para pruebas
php artisan schedule:run
```

---

## 📁 Estructura de Directorios

```
iglesia-alabanza/
├── app/
│   ├── Console/
│   │   └── Commands/
│   │       └── SendEventReminders.php    # Comando de recordatorios
│   ├── Http/
│   │   ├── Controllers/                  # Controladores
│   │   └── Middleware/
│   │       └── RoleMiddleware.php        # Middleware de roles
│   ├── Livewire/                         # Componentes Livewire
│   │   ├── EventManager.php
│   │   ├── MemberManager.php
│   │   ├── RehearsalManager.php
│   │   ├── SongManager.php
│   │   ├── InstrumentManager.php
│   │   ├── AttendanceManager.php
│   │   ├── MySchedule.php
│   │   └── ScheduleBuilder.php
│   ├── Models/                           # Modelos Eloquent
│   │   ├── User.php
│   │   ├── Event.php
│   │   ├── Rehearsal.php
│   │   ├── Song.php
│   │   ├── Instrument.php
│   │   └── Attendance.php
│   ├── Services/
│   │   └── WhatsAppService.php           # Servicio WhatsApp
│   └── Providers/
│       └── AppServiceProvider.php
├── config/                               # Configuraciones
├── database/
│   ├── factories/                        # Factories para testing
│   ├── migrations/                       # Migraciones
│   └── seeders/
│       ├── InstrumentSeeder.php
│       └── DemoSeeder.php                # Datos de demostración
├── resources/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   └── views/                            # Vistas Blade
│       ├── layouts/
│       ├── livewire/
│       └── components/
├── routes/
│   ├── web.php                           # Rutas web
│   ├── console.php                       # Scheduler configurado
│   └── auth.php                          # Rutas de autenticación
├── storage/
│   └── logs/
│       └── reminders.log                 # Log de recordatorios
├── tests/                                # Tests
├── .env                                  # Variables de entorno
├── composer.json                         # Dependencias PHP
├── package.json                          # Dependencias Node
└── README.md                             # Este archivo
```

---

## ⌨️ Comandos Útiles

### Artisan (PHP)

```bash
# Servidor de desarrollo
php artisan serve

# Ejecutar migraciones
php artisan migrate

# Ejecutar migraciones con datos de prueba
php artisan migrate --seed

# Ejecutar seeder específico
php artisan db:seed --class=DemoSeeder

# Enviar recordatorios manualmente
php artisan events:send-reminders

# Enviar recordatorios en modo simulación
php artisan events:send-reminders --dry-run

# Ver tareas programadas
php artisan schedule:list

# Limpiar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Crear componente Livewire
php artisan make:livewire NombreComponente

# Tinker (consola interactiva)
php artisan tinker
```

### NPM

```bash
# Instalar dependencias
npm install

# Servidor de desarrollo Vite
npm run dev

# Compilar para producción
npm run build

# Actualizar dependencias
npm update
```

---

## 🎨 Funcionalidades del Sistema

### Dashboard Principal
- Vista resumida de eventos próximos
- Accesos directos a todas las funcionalidades
- Diseño moderno con efecto glassmorphism

### Gestión de Eventos
- Crear, editar y eliminar eventos
- Asignar miembros a eventos
- Agregar canciones al repertorio
- Estado de confirmación (confirmado/pendiente/rechazado)

### Gestión de Miembros
- Registro con datos completos
- Asignación de instrumentos
- Control de roles (líder/miembro)
- Números de teléfono para WhatsApp

### Ensayos
- Programar ensayos vinculados a eventos
- Control de asistencia
- Notas y observaciones

### Cancionero
- Base de datos de canciones
- Información de tono, tempo y duración
- Enlaces a letras y acordes
- Enlaces a videos de YouTube

### Instrumentos
- Inventario de instrumentos musicales
- Categorización por tipo
- Asignación a miembros

### Asistencias
- Registro de asistencia a eventos
- Registro de asistencia a ensayos
- Reportes de asistencia

### Recordatorios Automáticos
- Envío diario a las 9:00 AM (hora Guatemala)
- Recordatorios para eventos del día siguiente
- Mensajes personalizados por instrumento
- Log de envíos en `storage/logs/reminders.log`

---

## 🔐 Credenciales de Prueba (DemoSeeder)

Después de ejecutar `php artisan db:seed --class=DemoSeeder`:

| Rol | Email | Contraseña |
|-----|-------|------------|
| Líder | `admin@iglesia.com` | `password` |
| Líder | `maria@iglesia.com` | `password` |
| Miembro | `juan@iglesia.com` | `password` |
| Miembro | `ana@iglesia.com` | `password` |

---

## 📝 Notas Importantes

1. **Zona Horaria**: El sistema está configurado para `America/Guatemala` (UTC-6)
2. **WhatsApp**: El envío depende de la disponibilidad de CallMeBot (servicio gratuito)
3. **Base de Datos**: Por defecto usa SQLite. Para producción, considera MySQL o PostgreSQL
4. **Backup**: Realiza copias de seguridad periódicas de la base de datos

---

## 🤝 Contribución

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/nueva-funcionalidad`)
3. Commit tus cambios (`git commit -am 'Agregar nueva funcionalidad'`)
4. Push a la rama (`git push origin feature/nueva-funcionalidad`)
5. Abre un Pull Request

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver archivo `LICENSE` para más detalles.

---

## 📞 Soporte

Para reportar bugs o solicitar nuevas funcionalidades, por favor abre un issue en el repositorio.

---

<p align="center">Hecho con ❤️ para el ministerio de alabanza</p>
