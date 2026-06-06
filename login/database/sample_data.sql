-- PALMED Clinic v1.0 - Sample Data
SET NAMES utf8mb4;

-- Roles
INSERT IGNORE INTO `roles` (`id`, `name`, `slug`, `description`, `is_system`) VALUES
(1, 'Super Administrador', 'super_admin', 'Acceso total al sistema', 1),
(2, 'Administrador', 'admin', 'Administración de clínica', 1),
(3, 'Médico', 'physician', 'Médico tratante', 1),
(4, 'Asistente', 'assistant', 'Asistente administrativo', 1);

-- Permissions
INSERT IGNORE INTO `permissions` (`id`, `name`, `slug`, `module`, `description`) VALUES
(1, 'Ver dashboard', 'dashboard.view', 'dashboard', 'Acceso al panel principal'),
(2, 'Gestionar pacientes', 'patients.manage', 'patients', 'Crear, editar y ver pacientes'),
(3, 'Ver pacientes', 'patients.view', 'patients', 'Solo lectura de pacientes'),
(4, 'Gestionar consultas', 'consultations.manage', 'consultations', 'Crear y editar consultas'),
(5, 'Ver consultas', 'consultations.view', 'consultations', 'Solo lectura de consultas'),
(6, 'Gestionar citas', 'appointments.manage', 'appointments', 'Crear y editar citas'),
(7, 'Ver citas', 'appointments.view', 'appointments', 'Solo lectura de citas'),
(8, 'Gestionar usuarios', 'users.manage', 'users', 'Administrar usuarios del sistema'),
(9, 'Gestionar roles', 'roles.manage', 'roles', 'Configurar roles y permisos'),
(10, 'Gestionar especialidades', 'specialties.manage', 'specialties', 'Administrar especialidades'),
(11, 'Gestionar documentos', 'documents.manage', 'documents', 'Subir y gestionar documentos'),
(12, 'Generar PDF', 'pdf.generate', 'documents', 'Generar documentos PDF'),
(13, 'Ver auditoría', 'audit.view', 'audit', 'Ver registros de auditoría'),
(14, 'Configuración del sistema', 'settings.manage', 'settings', 'Configuración general');

-- Super Admin
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`)
SELECT 1, id FROM permissions;

-- Admin
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(2,1),(2,2),(2,3),(2,4),(2,5),(2,6),(2,7),(2,8),(2,10),(2,11),(2,12),(2,13);

-- Médico
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(3,1),(3,2),(3,3),(3,4),(3,5),(3,6),(3,7),(3,11),(3,12);

-- Asistente
INSERT IGNORE INTO `role_permissions` (`role_id`, `permission_id`) VALUES
(4,1),(4,2),(4,3),(4,6),(4,7),(4,11);

-- Especialidades
INSERT IGNORE INTO `specialties`
(`id`,`name`,`slug`,`description`,`is_active`) VALUES
(1,'Medicina General','general-medicine','Consulta médica general',1),
(2,'Pediatría','pediatrics','Atención pediátrica',1),
(3,'Urología','urology','Especialidad urológica',1),
(4,'Dermatología','dermatology','Especialidad dermatológica',1),
(5,'Cardiología','cardiology','Especialidad cardiológica',1),
(6,'Cirugía','surgery','Especialidad quirúrgica',1);

-- ICD10
INSERT IGNORE INTO `icd10_codes`
(`code`,`description_es`,`description_en`) VALUES
('J06.9','Infección aguda de las vías respiratorias superiores, no especificada','Acute upper respiratory infection, unspecified'),
('I10','Hipertensión esencial (primaria)','Essential (primary) hypertension'),
('E11.9','Diabetes mellitus tipo 2 sin complicaciones','Type 2 diabetes mellitus without complications'),
('M54.5','Lumbago no especificado','Low back pain'),
('K21.0','Enfermedad por reflujo gastroesofágico con esofagitis','Gastro-esophageal reflux disease with esophagitis'),
('J45.9','Asma no especificada','Asthma, unspecified'),
('F41.1','Trastorno de ansiedad generalizada','Generalized anxiety disorder'),
('N39.0','Infección de vías urinarias, sitio no especificado','Urinary tract infection, site not specified'),
('L30.9','Dermatitis, no especificada','Dermatitis, unspecified'),
('R51','Cefalea','Headache');