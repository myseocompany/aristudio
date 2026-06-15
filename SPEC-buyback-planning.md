# SPEC: Planeacion AriStudio con BuybackSystem

## Objetivo

Actualizar el sistema de planeacion de AriStudio para corregir contexto operativo, incorporar el proyecto COOTILCA y agregar una capa obligatoria de decision basada en el framework Buy Back Your Time.

El sistema debe ayudar a decidir que entra en el calendario de Nicolas, que debe delegarse, que debe documentarse, que debe automatizarse y que apoyo externo debe buscarse primero con base en datos.

## Decisiones v2

- El Replacement Ladder no debe implementarse como enum hardcodeado. Debe existir como catalogo seedable y editable.
- Las columnas JSON deben tener una forma canonica validada por Form Requests, herramientas MCP y tests.
- Las herramientas MCP deben usar filtros estrechos y limites pequenos por defecto, siguiendo la instruccion actual del servidor MCP.
- `owner_user_id` siempre se deriva del usuario autenticado, no del body.
- `audit_date` representa el dia operativo auditado; `created_at` representa cuando se creo el registro.
- La primera implementacion debe separarse por fases para evitar convertir este cambio en tres sprints mezclados.

## Definiciones operativas

### Umbrales DRIP

Los scores usan escala 1-5.

- Valor economico alto: `economic_value_score >= 4` o `strategic_value_score >= 4`.
- Valor economico bajo: ambos scores menores a 4.
- Energiza: `energy_score >= 4` o `enjoyment_score >= 4`.
- Drena: ambos scores menores a 4.

Clasificacion:

- `delegation`: valor bajo + drena.
- `replacement`: valor alto + drena.
- `investment`: valor bajo + energiza.
- `production`: valor alto + energiza.

Empates o datos incompletos:

- Si faltan scores, la tarea queda `unclassified` a nivel de servicio y no debe programarse automaticamente.
- Si hay empate entre valor alto y energia baja, prevalece `replacement` para forzar accion de salida.
- Si hay empate entre Production e Investment, prevalece `production` cuando la tarea pertenece a AriCRM, Reto21Vendo+ o COOTILCA estrategico.

### Tarea repetida

Una tarea es repetida si cumple cualquiera de estas condiciones:

- Mismo `task_type_id` y mismo `project_id` aparece 3 veces o mas en 30 dias.
- Nombre normalizado similar aparece 3 veces o mas en 30 dias.
- El usuario marca manualmente `is_recurring_candidate: true`.

Nombre normalizado significa lowercase, sin tildes, sin puntuacion y sin fechas/numeros aislados.

### Formula de recommended_first_support_role

Cada area del Replacement Ladder recibe un score:

`score = repeated_weight + drain_weight + low_value_weight + strategic_blocker_weight + continuity_risk_weight + nicolas_dependency_weight + urgency_weight`

Pesos iniciales:

- Tareas repetidas: hasta 25 puntos.
- Tareas que drenan: hasta 20 puntos.
- Bajo valor economico: hasta 15 puntos.
- Bloqueo de proyecto estrategico: hasta 20 puntos.
- Riesgo de continuidad por Luisa: hasta 10 puntos.
- Dependencia directa de Nicolas: hasta 15 puntos.
- Urgencia alta o critica: hasta 15 puntos.

La respuesta debe incluir los factores usados y el puntaje por factor. Los pesos deben guardarse con version de framework para poder auditar recomendaciones futuras.

## Correcciones de contexto obligatorias

### Laura

- Laura debe eliminarse completamente del sistema.
- No debe existir como miembro del equipo, recurso, responsable, opcion de delegacion ni compromiso diario.
- Ninguna tarea, seed, sugerencia, herramienta MCP o regla de planificacion puede asignar o recomendar tareas a Laura.

Politica historica:

- No se deben borrar registros historicos si ya existen tareas, auditorias o comentarios vinculados.
- Si existe un usuario historico llamado Laura, debe marcarse como inactivo/no asignable y excluirse de toda busqueda de responsables.
- Las tareas abiertas asignadas a Laura deben quedar sin responsable o reasignarse manualmente; el sistema debe marcarlas como `needs_reassignment: true`.
- Las tareas cerradas conservan referencia historica, pero las respuestas publicas del planificador deben mostrar `responsable historico no asignable` en vez de sugerir a Laura.
- Seeds nuevos no pueden crear Laura.

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

El sistema debe construir y mantener una escalera de reemplazo por areas seedables y editables. Estas areas no son enum de codigo; deben vivir en una tabla/catalogo `support_areas`.

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

Campos sugeridos para `support_areas`:

- `id`
- `name`
- `slug`
- `description`
- `default_role_type`
- `priority`
- `is_active`
- `created_at`
- `updated_at`

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

### SupportArea

Catalogo editable para Replacement Ladder y tipos de apoyo. Reemplaza enums rigidos de areas.

Tabla: `support_areas`

Campos:

- `id`
- `name`
- `slug`
- `description` nullable
- `default_role_type` nullable
- `priority` integer
- `is_active` boolean
- `created_at`
- `updated_at`

### BuybackAudit

Tabla: `buyback_audits`

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
- `framework_version`
- `classification_hash`
- `notes`
- `created_at`
- `updated_at`

Idempotencia:

- Debe existir indice unico compuesto por `owner_user_id`, `audit_date`, `task_id`, `project_id`, `classification_hash`.
- Si se corre el mismo audit dos veces el mismo dia con los mismos datos de entrada, se actualiza el registro existente.
- Si cambian scores o inputs relevantes, se crea una nueva version con `classification_hash` distinto.
- `audit_date` no reemplaza `created_at`: permite auditar un dia operativo aunque el registro se cree despues.

### DelegationCandidate

Tabla: `delegation_candidates`

Campos:

- `id`
- `owner_user_id`
- `task_id` nullable
- `project_id` nullable
- `title`
- `description`
- `current_owner`
- `recommended_owner_type`: `freelancer`, `contractor`, `employee`, `agency`, `automation`, `eliminate`
- `support_area_id` nullable
- `support_role_id` nullable
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

Relacion con SupportRole:

- Un `DelegationCandidate` puede sugerir o vincular un `SupportRole`.
- Delegar o automatizar un candidato no cierra automaticamente el rol.
- Un `SupportRole` se marca `active`, `paused` o `rejected` segun el estado real de contratacion/experimento.

### SupportRole

Tabla: `support_roles`

Campos:

- `id`
- `owner_user_id`
- `title`
- `support_area_id` nullable
- `role_type`: texto validado contra catalogo seedable, no enum rigido de codigo
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

Tabla: `playbooks`

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

### TeamMemberStatus

No crear una entidad `TeamMember` en Fase 1. El repo ya usa `User` y `Role`; se debe extender `users` con campos operativos.

Migracion sugerida: `add_planning_status_fields_to_users_table`

Campos:

- `exit_status`: `active`, `probable_exit`, `exited`, `historical_non_assignable`
- `is_assignable` boolean
- `continuity_risk` boolean
- `needs_transition` boolean
- `transition_notes` nullable text

`mark_team_member_as_exiting` debe operar sobre `users`.

### ProtectedBlock

Fase 2, salvo que ya exista una entidad equivalente.

Tabla sugerida: `protected_blocks`

Campos:

- `id`
- `owner_user_id`
- `title`
- `block_type`: `family`, `health`, `learning`, `one_thing`, `personal_commitment`
- `day_of_week` nullable integer
- `starts_at` nullable
- `ends_at` nullable
- `date` nullable
- `is_recurring` boolean
- `priority`
- `created_at`
- `updated_at`

### PersonalCommitment

Fase 2, salvo que los compromisos personales se modelen como `protected_blocks`.

Tabla sugerida: `personal_commitments`

Campos:

- `id`
- `owner_user_id`
- `title`
- `commitment_type`: `health`, `dance`, `family`, `spiritual`, `learning`, `other`
- `status`: `active`, `paused`, `removed`
- `notes`
- `created_at`
- `updated_at`

## Schemas JSON canonicos

### required_skills_json

```json
[
  {
    "name": "Project management",
    "level": "intermediate",
    "required": true,
    "notes": "Puede coordinar entregables, fechas y responsables."
  }
]
```

Reglas:

- `name`: string requerido.
- `level`: `basic`, `intermediate`, `advanced`, `expert`.
- `required`: boolean requerido.
- `notes`: string nullable.

### checklist_json

```json
[
  {
    "order": 1,
    "title": "Revisar brief del proyecto",
    "description": "Confirmar objetivos, restricciones y entregables.",
    "required": true
  }
]
```

Reglas:

- `order`: integer requerido.
- `title`: string requerido.
- `description`: string nullable.
- `required`: boolean requerido.

### responsibilities_json

```json
[
  {
    "area": "Seguimiento administrativo",
    "responsibility": "Actualizar estado semanal de entregables",
    "success_criteria": "Tablero actualizado antes del viernes 12:00"
  }
]
```

Reglas:

- `area`: string requerido.
- `responsibility`: string requerido.
- `success_criteria`: string nullable.

### not_responsible_for_json

```json
[
  {
    "area": "Estrategia",
    "boundary": "No define diagnostico ni decisiones consultivas finales"
  }
]
```

Reglas:

- `area`: string requerido.
- `boundary`: string requerido.

## Migraciones esperadas

Nombres sugeridos:

- `create_support_areas_table`
- `create_buyback_audits_table`
- `create_support_roles_table`
- `create_delegation_candidates_table`
- `create_playbooks_table`
- `add_planning_status_fields_to_users_table`
- Fase 2: `create_protected_blocks_table`
- Fase 2: `create_personal_commitments_table`

Convenciones:

- Usar FKs para `owner_user_id`, `task_id`, `project_id`, `support_area_id` y `support_role_id`.
- `owner_user_id` debe apuntar a `users.id`.
- `task_id` y `project_id` deben ser nullable con `nullOnDelete()`.
- No usar enum de base de datos para areas editables.
- Para estados cerrados y pequenos, se permiten strings validados por Form Request y constantes de modelo.

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

## Auth y visibilidad

- Todos los endpoints `/api/planning/...` requieren usuario autenticado.
- `owner_user_id` se deriva exclusivamente del token/session del usuario autenticado.
- El body no puede definir `owner_user_id`.
- Por defecto, Nicolas ve sus datos y administradores pueden ver datos de otros usuarios con filtros explicitos.
- Usuarios no administradores solo pueden listar/crear recursos propios.
- Las herramientas MCP deben usar `$request->user()` y los permisos existentes del modulo correspondiente.
- Para crear o modificar tareas: permiso `/tasks` con `create` o `update`.
- Para leer planeacion: permiso futuro `/planning` con `read` o rol admin.
- Para crear auditorias, candidatos, roles y playbooks: permiso futuro `/planning` con `create` o rol admin.

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
- `GET /api/planning/buyback-metrics`

Reglas de listado:

- Todos los `GET` deben aceptar `limit` con default 30 y maximo 100.
- Listados grandes deben aceptar filtros por `project_id`, `task_id`, `support_area_id`, `status`, `from`, `to` segun aplique.
- Ningun endpoint debe devolver todas las tareas abiertas sin limite.

Metricas esperadas en `GET /api/planning/buyback-metrics`:

- `% tiempo Production`
- `% tiempo Investment`
- `% tiempo Replacement`
- `% tiempo Delegation`
- `delegation_candidates_created`
- `playbooks_created`
- `tasks_moved_out_of_nicolas`
- `replacement_exit_actions_open`
- `continuity_risks_open`
- `cootilca_nicolas_dependency_score`

## Herramientas MCP nuevas

Las tools deben seguir el estilo actual de `app/Mcp/Tools/*.php`: clase por tool, `schema(JsonSchema $schema)`, `outputSchema(JsonSchema $schema)`, `handle(Request $request)`, validacion de request y respuesta `Response::structured(...)`.

### run_buyback_audit

Clasifica tareas abiertas usando DRIP Matrix.

Input schema:

- `project_id` integer nullable.
- `user_id` integer nullable; solo admin puede usarlo.
- `status_id` integer nullable.
- `from` string date nullable.
- `to` string date nullable.
- `limit` integer default 30, min 1, max 100.
- `dry_run` boolean default false.

Output schema:

- `count` integer.
- `created` integer.
- `updated` integer.
- `skipped` integer.
- `audits` array con `task_id`, `project_id`, `drip_quadrant`, `recommended_action`, `classification_hash`.

Regla:

- Nunca procesa mas de `limit`.
- Debe recomendar filtros mas estrechos si encuentra mas de 100 candidatas.
- Debe ser idempotente para mismos inputs y mismo `audit_date`.

### classify_task_buyback

Clasifica una tarea individual.

Input schema:

- `task_id` integer required.
- `energy_score` integer nullable 1-5.
- `economic_value_score` integer nullable 1-5.
- `skill_fit_score` integer nullable 1-5.
- `enjoyment_score` integer nullable 1-5.
- `strategic_value_score` integer nullable 1-5.
- `notes` string nullable.

Output schema:

- `task_id` integer.
- `drip_quadrant` string.
- `recommended_action` string.
- `exit_action_required` boolean.
- `suggested_exit_actions` array string.

### list_delegation_candidates

Lista tareas candidatas a delegar.

Input schema:

- `project_id` integer nullable.
- `support_area_id` integer nullable.
- `status` string nullable.
- `urgency` string nullable.
- `limit` integer default 30, min 1, max 100.

Output schema:

- `count` integer.
- `candidates` array con `id`, `title`, `status`, `urgency`, `support_area`, `role_needed`, `documentation_status`.

### create_delegation_candidate

Crea un candidato de delegacion.

Input schema:

- `task_id` integer nullable.
- `project_id` integer nullable.
- `title` string required.
- `description` string nullable.
- `recommended_owner_type` string required.
- `support_area_id` integer nullable.
- `role_needed` string nullable.
- `required_skills_json` array nullable con schema canonico.
- `urgency` string required.
- `documentation_status` string required.

Output schema:

- `message` string.
- `candidate` object con campos principales.

### create_playbook_from_task

Crea un borrador de playbook desde una tarea repetida.

Input schema:

- `task_id` integer required.
- `process_area` string nullable.
- `support_area_id` integer nullable.
- `include_task_description` boolean default true.

Output schema:

- `message` string.
- `playbook` object con `id`, `title`, `status`, `related_task_id`, `checklist_json`.

### list_support_roles

Lista roles de apoyo necesarios.

Input schema:

- `support_area_id` integer nullable.
- `status` string nullable.
- `priority` integer nullable.
- `limit` integer default 30, min 1, max 100.

Output schema:

- `count` integer.
- `roles` array con `id`, `title`, `support_area`, `role_type`, `priority`, `status`, `estimated_hours_weekly`.

### recommend_first_support_role

Recomienda el primer apoyo a buscar con base en datos de tareas, energia, valor y urgencia.

Input schema:

- `project_id` integer nullable.
- `from` string date nullable.
- `to` string date nullable.
- `include_cootilca` boolean default true.
- `limit` integer default 30, min 1, max 100.

Output schema:

- `recommended_role` object.
- `support_area` object.
- `score` integer.
- `factor_scores` object.
- `evidence_tasks` array limitado.
- `risk_if_not_hired` string.
- `first_experiment` string.

### mark_team_member_as_exiting

Marca a una persona como salida probable y activa riesgo de continuidad.

Input schema:

- `user_id` integer required.
- `exit_status` string required: `probable_exit` o `exited`.
- `transition_notes` string nullable.

Output schema:

- `message` string.
- `user` object con `id`, `name`, `exit_status`, `is_assignable`, `continuity_risk`, `needs_transition`.

Regla:

- Opera sobre `users`; no requiere entidad `TeamMember`.
- Si el usuario tiene tareas abiertas, marca esas tareas para revision de continuidad segun el mecanismo existente o una tabla de metadata de planeacion.

### get_cootilca_operating_profile

Devuelve el estado operativo del proyecto COOTILCA.

Input schema:

- `include_tasks` boolean default true.
- `limit` integer default 30, min 1, max 100.

Output schema:

- `project` object.
- `strategic_importance` string.
- `nicolas_dependency_score` integer.
- `risk_level` string.
- `task_breakdown` object por tipo operativo.
- `delegation_candidates` array limitado.
- `recommended_next_actions` array string.

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

### Support areas iniciales

Seed editable para `support_areas`:

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

Estas filas pueden modificarse desde admin o seeders futuros sin cambiar codigo.

## Versionado del framework

- Cada clasificacion debe guardar `framework_version`.
- Version inicial: `buyback-v1`.
- Cambios en umbrales DRIP, pesos de recomendacion o reglas de desempate deben crear nueva version.
- El historico no se reclasifica automaticamente.
- Debe existir una accion explicita futura para recalcular auditorias historicas con otra version.

## Fases de implementacion

### Fase 1

- Correcciones de contexto: Laura, teatro musical, Luisa.
- COOTILCA en seeds.
- `support_areas`.
- `buyback_audits`.
- `delegation_candidates`.
- `support_roles`.
- `playbooks`.
- Endpoints principales de CRUD/listado.
- MCP tools con limites y schemas.
- Metricas basicas de BuybackSystem.

### Fase 2

- `protected_blocks` si no existe una entidad equivalente.
- `personal_commitments` si no se modelan como protected blocks.
- Reportes avanzados.
- Recalculo historico por version de framework.
- Admin UI para editar Replacement Ladder, pesos y umbrales.

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
- Los listados y tools MCP respetan `limit` default 30 y maximo 100.
- `run_buyback_audit` es idempotente para mismos inputs y mismo dia operativo.
- `owner_user_id` no se acepta desde el body.
- Las areas del Replacement Ladder son seedables/editables, no enums hardcodeados.
- Las columnas JSON se validan contra el schema canonico.

## Pruebas esperadas

- Feature tests para los endpoints nuevos.
- Tests de clasificacion DRIP para los cuatro cuadrantes.
- Tests de reglas de planificacion diaria para Production, Investment, Replacement y Delegation.
- Tests de exclusion total de Laura.
- Tests de eliminacion de teatro musical.
- Tests de riesgo de continuidad para Luisa.
- Tests de perfil operativo de COOTILCA.
- Tests de recomendacion de primer apoyo basada en datos.
- Tests de idempotencia de `run_buyback_audit`.
- Tests de autorizacion y derivacion de `owner_user_id` desde el usuario autenticado.
- Tests de limites/paginacion en endpoints y MCP tools.
- Tests de validacion de schemas JSON.
