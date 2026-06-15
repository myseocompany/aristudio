# SPEC: Planeacion AriStudio con BuybackSystem

## Objetivo

Actualizar el sistema de planeacion de AriStudio para corregir contexto operativo, incorporar el proyecto COOTILCA y agregar una capa obligatoria de decision basada en el framework Buy Back Your Time.

El sistema debe ayudar a decidir que entra en el calendario de Nicolas, que debe delegarse, que debe documentarse, que debe automatizarse y que apoyo externo debe buscarse primero con base en datos.

## Correcciones de contexto obligatorias

### Laura

- Laura debe eliminarse completamente del sistema.
- No debe existir como miembro del equipo, recurso, responsable, opcion de delegacion ni compromiso diario.
- Ninguna tarea, seed, sugerencia, herramienta MCP o regla de planificacion puede asignar o recomendar tareas a Laura.

### Teatro musical

- Teatro musical debe eliminarse como compromiso, restriccion o bloque del calendario.
- Los compromisos personales validos de Nicolas son bachata y salsa.

### Luisa

- Luisa esta en salida probable.
- No puede ser responsable de procesos criticos.
- Puede aparecer solo como recurso transitorio, apoyo de cierre o fuente de transferencia de conocimiento.
- Toda tarea actualmente dependiente de Luisa debe marcarse con:
  - `continuity_risk: true`
  - `needs_transition: true`
- Toda tarea dependiente de Luisa debe aparecer en reportes como riesgo de continuidad.

## Proyecto nuevo: COOTILCA

COOTILCA debe agregarse como proyecto estrategico.

Campos y contexto esperado:

- Nombre: `COOTILCA`
- Programa: `Fabricas de Productividad`
- Tipo: entrega estrategica, consultoria e implementacion
- Importancia: alta
- Riesgo: alto si depende directamente de Nicolas
- Relacion con AriCRM: potencial caso de estudio institucional y metodologia replicable
- Requiere:
  - SCAN
  - levantamiento
  - diagnostico
  - flujos
  - Bowtie
  - entregables
  - posible implementacion tecnologica

Reglas operativas por tipo de trabajo:

- Diagnostico estrategico: Nicolas
- Documentacion: delegable
- Diseno de entregables: parcialmente delegable
- Seguimiento administrativo: apoyo externo
- Implementacion tecnica critica: Nicolas o tecnico contratado
- Project management: candidato fuerte a apoyo externo

COOTILCA no debe consumir todo el calendario. El planificador debe proteger bloques de AriCRM / Reto21Vendo+ como One Thing y mantener compromisos personales/familiares protegidos.

## BuybackSystem

Agregar una capa obligatoria llamada `BuybackSystem`.

### Objetivo

Identificar:

- Donde Nicolas es realmente valioso.
- Que tareas lo drenan.
- Que tareas deben delegarse.
- Que tareas deben automatizarse.
- Que tareas deben documentarse antes de salir de Nicolas.
- Que apoyo externo debe buscarse primero.

### Buyback Loop

El sistema debe aplicar el ciclo:

1. Audit: registrar y clasificar tareas por valor, energia, habilidad, disfrute y valor estrategico.
2. Transfer: mover tareas hacia delegacion, automatizacion, eliminacion o documentacion.
3. Fill: liberar calendario para trabajo de mayor valor, principalmente Production y algo de Investment.

### DRIP Matrix

Cada tarea debe clasificarse por:

- Valor economico: bajo / alto
- Energia: drena / energiza

Cuadrantes:

- Delegation: bajo valor + drena
- Replacement: alto valor + drena
- Investment: bajo valor + energiza
- Production: alto valor + energiza

Regla central:

- Nicolas debe pasar mas tiempo en Production.
- Nicolas puede dedicar algo de tiempo a Investment.
- Nicolas debe salir progresivamente de Delegation.
- Nicolas debe reducir Replacement con sistemas, playbooks, automatizacion y apoyo externo.

### Replacement Ladder

El sistema debe construir y mantener una escalera de reemplazo por areas:

- Admin / finanzas / facturacion
- Diseno / produccion creativa
- Pauta / performance
- Social media / publicacion
- Atencion / seguimiento comercial
- Implementacion tecnica
- Project management
- Ventas operativas
- Soporte AriCRM
- Documentacion / SOPs

Cada area debe poder conectarse con tareas repetidas, candidatos de delegacion, roles de soporte y playbooks.

### Camcorder Method / Playbooks

Cada tarea repetida que Nicolas haga debe poder convertirse en:

- Grabacion
- Checklist
- SOP
- Plantilla
- Tarea delegable

Si una tarea se repite 3 veces o mas, AriStudio debe sugerir:

- Crear `Playbook`
- Crear `DelegationCandidate`
- Crear `SupportRole`

## Entidades nuevas

### BuybackAudit

Campos:

- `id`
- `owner_user_id`
- `audit_date`
- `task_id` nullable
- `project_id` nullable
- `title`
- `description`
- `estimated_minutes`
- `actual_minutes` nullable
- `energy_score` integer 1-5
- `economic_value_score` integer 1-5
- `skill_fit_score` integer 1-5
- `enjoyment_score` integer 1-5
- `strategic_value_score` integer 1-5
- `drip_quadrant`: `delegation`, `replacement`, `investment`, `production`
- `recommended_action`: `keep`, `delegate`, `automate`, `eliminate`, `document`, `hire_for`
- `notes`
- `created_at`
- `updated_at`

### DelegationCandidate

Campos:

- `id`
- `owner_user_id`
- `task_id` nullable
- `project_id` nullable
- `title`
- `description`
- `current_owner`
- `recommended_owner_type`: `freelancer`, `contractor`, `employee`, `agency`, `automation`, `eliminate`
- `role_needed`
- `required_skills_json`
- `estimated_hours_per_week`
- `estimated_budget_monthly`
- `urgency`: `low`, `medium`, `high`, `critical`
- `risk_if_not_delegated`
- `documentation_status`: `none`, `partial`, `ready`
- `status`: `candidate`, `approved`, `sourcing`, `delegated`, `automated`, `eliminated`
- `created_at`
- `updated_at`

### SupportRole

Campos:

- `id`
- `owner_user_id`
- `title`
- `role_type`: `admin`, `design`, `ads`, `content`, `developer`, `project_manager`, `va`, `sales`, `automation`, `finance`
- `problem_solved`
- `responsibilities_json`
- `not_responsible_for_json`
- `required_skills_json`
- `estimated_hours_weekly`
- `budget_range`
- `priority`
- `status`: `needed`, `sourcing`, `testing`, `active`, `paused`, `rejected`
- `created_at`
- `updated_at`

### Playbook

Campos:

- `id`
- `owner_user_id`
- `title`
- `process_area`
- `related_task_id` nullable
- `related_project_id` nullable
- `description`
- `checklist_json`
- `loom_url` nullable
- `video_url` nullable
- `template_url` nullable
- `sop_body`
- `status`: `draft`, `usable`, `tested`, `deprecated`
- `created_at`
- `updated_at`

## Reglas de planificacion

Antes de poner una tarea en el plan diario de Nicolas, AriStudio debe evaluar:

- Si esta en Production.
- Si esta en Investment.
- Si esta en Replacement.
- Si esta en Delegation.
- Si debe documentarse antes de delegarse.
- Si debe eliminarse.

Reglas por cuadrante:

- Delegation: no debe entrar al dia de Nicolas salvo emergencia explicita.
- Replacement: puede entrar, pero debe generar una accion de salida.
- Investment: puede entrar si no desplaza Production ni compromisos protegidos.
- Production: debe tener prioridad alta si esta alineada con One Thing o proyectos estrategicos.

Acciones de salida validas para Replacement:

- Crear playbook.
- Grabar ejecucion.
- Buscar apoyo.
- Automatizar.
- Definir rol.

## Recomendacion de primer apoyo

El sistema debe recomendar el primer apoyo a buscar desde datos, no desde intuicion.

Factores de decision:

- Tareas mas repetidas.
- Tareas que mas drenan.
- Tareas de bajo valor economico.
- Tareas que bloquean proyectos estrategicos.
- Tareas que Nicolas no deberia hacer.
- Riesgo de continuidad por salida probable de Luisa.
- Dependencia directa de Nicolas en COOTILCA.

La recomendacion debe devolver:

- Rol recomendado.
- Area del Replacement Ladder.
- Problema principal que resuelve.
- Evidencia usada.
- Tareas candidatas relacionadas.
- Riesgo de no contratar/delegar.
- Primer experimento sugerido.

## Endpoints nuevos

- `GET /api/planning/buyback-audit`
- `POST /api/planning/buyback-audit`
- `POST /api/planning/tasks/{id}/buyback-classify`
- `GET /api/planning/delegation-candidates`
- `POST /api/planning/delegation-candidates`
- `GET /api/planning/support-roles`
- `POST /api/planning/support-roles`
- `GET /api/planning/playbooks`
- `POST /api/planning/playbooks`
- `POST /api/planning/playbooks/from-task`
- `GET /api/planning/recommended-first-hire`

## Herramientas MCP nuevas

### run_buyback_audit

Clasifica tareas abiertas usando DRIP Matrix.

### classify_task_buyback

Clasifica una tarea individual.

### list_delegation_candidates

Lista tareas candidatas a delegar.

### create_delegation_candidate

Crea un candidato de delegacion.

### create_playbook_from_task

Crea un borrador de playbook desde una tarea repetida.

### list_support_roles

Lista roles de apoyo necesarios.

### recommend_first_support_role

Recomienda el primer apoyo a buscar con base en datos de tareas, energia, valor y urgencia.

### mark_team_member_as_exiting

Marca a una persona como salida probable y activa riesgo de continuidad.

### get_cootilca_operating_profile

Devuelve el estado operativo del proyecto COOTILCA.

## Seeds

### Eliminar

- Laura.
- Teatro musical.
- Reunion diaria con Laura.

### Mantener

- Wake 5:30 am.
- Meditacion.
- Carrera.
- Bachata / salsa.
- Botanazo como bloque familiar protegido.
- AriCRM / Reto21Vendo+ como One Thing.

### Agregar

- COOTILCA / Fabricas de Productividad.
- Luisa como recurso transitorio con salida probable.
- Necesidad de buscar apoyo externo.
- Buy Back Your Time como framework de delegacion.

## Criterios de aceptacion

- Laura no aparece en seeds, asignaciones, reglas, opciones de delegacion, herramientas MCP ni respuestas del planificador.
- Teatro musical no aparece como compromiso, restriccion ni bloque de agenda.
- Bachata y salsa permanecen como compromisos validos.
- Luisa puede existir solo como recurso transitorio y salida probable.
- Las tareas dependientes de Luisa quedan marcadas con riesgo de continuidad y necesidad de transicion.
- COOTILCA existe como proyecto estrategico de alta importancia.
- COOTILCA no puede desplazar completamente AriCRM / Reto21Vendo+ ni bloques protegidos.
- Cada tarea planificable puede evaluarse con DRIP Matrix.
- Las tareas Delegation no entran al dia de Nicolas salvo emergencia.
- Las tareas Replacement generan una accion de salida.
- Las tareas repetidas 3 veces o mas disparan sugerencias de playbook, candidato de delegacion y rol de soporte.
- El endpoint de primer apoyo entrega una recomendacion basada en evidencia de tareas, energia, valor, urgencia y bloqueos.
- Las herramientas MCP nuevas reflejan las mismas reglas que los endpoints.

## Pruebas esperadas

- Feature tests para los endpoints nuevos.
- Tests de clasificacion DRIP para los cuatro cuadrantes.
- Tests de reglas de planificacion diaria para Production, Investment, Replacement y Delegation.
- Tests de exclusion total de Laura.
- Tests de eliminacion de teatro musical.
- Tests de riesgo de continuidad para Luisa.
- Tests de perfil operativo de COOTILCA.
- Tests de recomendacion de primer apoyo basada en datos.
