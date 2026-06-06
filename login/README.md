# PALMED Clinic v1.0

**PALMED Health Group S.A.S.**

Plataforma moderna de consulta médica ambulatoria para médicos y asistentes. Diseñada para ser elegante, rápida y fácil de aprender.

## Stack tecnológico

- PHP 8.3
- MySQL 8
- Bootstrap 5
- PDO
- Vanilla JavaScript
- HTML5 / CSS3

## Requisitos del servidor

- PHP 8.1+ (recomendado 8.3)
- MySQL 8.0+
- Extensiones PHP: `pdo_mysql`, `mbstring`, `json`, `gd`
- Apache con `mod_rewrite` (Hostinger Shared Hosting compatible)
- Sin Node.js, sin Docker, sin Composer en producción

## Instalación en Hostinger

### 1. Subir archivos

Suba todo el contenido de la carpeta `palmed-clinic` a su `public_html` o subdirectorio (ej: `public_html/clinic`).

### 2. Crear base de datos

En el panel de Hostinger:
1. Cree una base de datos MySQL
2. Cree un usuario con permisos completos
3. Anote host, nombre, usuario y contraseña

### 3. Ejecutar el instalador

Visite: `https://tudominio.com/install/`

Siga los 4 pasos:
1. **Base de datos** — Conexión MySQL
2. **Administrador** — Cuenta Super Administrador
3. **Configuración** — URL, empresa, idioma
4. **Finalizar** — Instalación automática

### 4. Seguridad post-instalación

- Elimine o renombre la carpeta `install/`
- Verifique que `config/config.php` no sea accesible públicamente
- Configure permisos: `uploads/` escribible (755 o 775)

## Estructura del proyecto

```
palmed-clinic/
├── index.php              # Front controller
├── .htaccess              # URL rewriting y seguridad
├── app/
│   ├── bootstrap.php      # Inicialización
│   ├── routes.php         # Rutas
│   ├── Core/              # Auth, Database, Router, Audit
│   ├── Controllers/       # Controladores
│   ├── Models/            # Modelos de datos
│   └── Helpers/           # Funciones globales
├── assets/
│   ├── css/palmed.css     # Estilos premium
│   └── js/                # JavaScript
├── config/
│   ├── config.sample.php
│   └── config.php         # Generado por instalador
├── database/
│   ├── schema.sql         # Esquema completo
│   └── sample_data.sql    # Datos de muestra
├── install/               # Asistente de instalación
├── lang/                  # Traducciones (ES/EN)
├── uploads/               # Documentos y firmas
└── views/                 # Plantillas PHP
```

## Módulos — Fase 1 (implementados)

| Módulo | Estado |
|--------|--------|
| Esquema de base de datos | ✅ Completo |
| Instalador web (4 pasos) | ✅ Completo |
| Autenticación y roles | ✅ Completo |
| Dashboard | ✅ Completo |
| Gestión de pacientes | ✅ Completo |
| Consulta médica (pantalla única) | ✅ Completo |
| CIE-10 búsqueda | ✅ Completo |
| Firma digital | ✅ Completo |
| Auditoría | ✅ Completo |
| Multilenguaje ES/EN | ✅ Completo |

## Módulos — Fases siguientes

- Citas (calendario día/semana/mes)
- Generación PDF (DomPDF)
- WhatsApp integration
- Telemedicina
- Gestión de documentos
- Administración de especialidades y usuarios

## Roles del sistema

1. **Super Administrador** — Acceso total
2. **Administrador** — Gestión de clínica
3. **Médico** — Consultas y pacientes
4. **Asistente** — Pacientes y citas

Los permisos son configurables por rol en la tabla `role_permissions`.

## Colores de marca

- Primario: `#0A5FD8`
- Secundario: `#00A86B`
- Tipografía: Inter

## Credenciales por defecto

No hay credenciales por defecto. El Super Administrador se crea durante la instalación.

## Licencia

Propiedad de PALMED Health Group S.A.S. Todos los derechos reservados.
