# SPEC: Nuevo subsistema de planeacion AriStudio con BuybackSystem

## Objetivo

Crear un subsistema nuevo de planeacion en AriStudio para corregir contexto operativo, incorporar el proyecto COOTILCA y agregar una capa obligatoria de decision basada en el framework Buy Back Your Time.

El sistema debe ayudar a decidir que entra en el calendario de Nicolas, que debe delegarse, que debe documentarse, que debe automatizarse y que apoyo externo debe buscarse primero con base en datos.

El repo actual no tiene un modulo `planning`, rutas `/api/planning/*`, seeds con Laura/Luisa/COOTILCA/teatro, ni modelo de calendario. Por tanto, este SPEC no debe interpretarse como una modificacion incremental de un planner existente, sino como la creacion gradual de un subsistema sobre las entidades reales ya disponibles: `users`, `tasks`, `projects`, `roles`, `modules` y las tools MCP actuales.

## Decisiones v3 contra el repo real

- Este trabajo se implementa como subsistema nuevo, no como actualizacion de un modulo existente.
- Fase 1 no debe crear endpoints REST `/api/planning/*` ni MCP nuevo; debe concentrarse en datos, servicios, seeds y tests.
- Si se agregan endpoints REST en una fase posterior, primero debe crearse y decidirse la capa API porque el repo no tiene `routes/api.php`.
- Las reglas compartidas deben vivir en servicios de dominio reutilizables por MCP, controladores futuros y tests. Nombre sugerido: `App\Services\Planning\BuybackService`.
- Las tools MCP deben seguir convenciones reales del repo: clases PascalCase en `App\Mcp\Tools`, annotations, `schema()`, `outputSchema()`, `handle()`, permisos por `hasModulePermission()`, y registro en `App\Mcp\Servers\BuybackPlannerServer`.
- El sistema debe reutilizar señales existentes de `tasks`, `users` y `projects` antes de crear campos duplicados.
- El calendario/bloques protegidos no existe. Ninguna regla de "no desplazar bloques" es implementable hasta crear `protected_blocks` o integrar una fuente externa de calendario.

## Decisiones v4 de implementabilidad

- Este SPEC debe cumplir explicitamente las Laravel Boost Guidelines del repo.
- Las tools Buyback deben vivir en un MCP server separado, salvo decision explicita contraria.
- La Fase 1 no debe cambiar dependencias.
- La Fase 1 no debe crear carpetas base nuevas fuera de convenciones Laravel existentes.
- Las reglas de planning deben reusar `task_statuses`, `tasks.parent_id`, `users.termination_date`, `users.status_id`, `users.enterprise_id`, `project_users`, `tasks.points` y el flujo real de `TimerController`.
- `BuybackAudit` debe separar quien audita de quien es auditado.
- El ciclo Buyback debe incluir Review, no solo Audit/Transfer/Fill.
- Las sugerencias automaticas deben tener canal visible; si no hay UI/notificaciones, deben quedar como registros consultables.
- `MarkUserAsPlanningExit` es una operacion destructiva y no debe ejecutarse sin confirmacion explicita.

## Cumplimiento Laravel Boost

- No cambiar dependencias sin aprobacion. Fase 1 no requiere paquetes nuevos.
- No crear nuevas carpetas base sin aprobacion. Usar `app/Models`, `app/Services/Planning`, `app/Mcp/Servers`, `app/Mcp/Tools`, `app/Http/Requests`, `app/Http/Resources` y `database/*`.
- Crear modelos con `php artisan make:model` cuando pase a implementacion.
- Cada modelo nuevo debe tener factory util y seeder cuando aplique.
- Endpoints REST futuros deben usar Form Requests para validacion.
- Endpoints REST futuros deben usar API Resources y versionado `/api/v1`.
- Preferir `Model::query()` y relaciones Eloquent; no usar `DB::` para logica de dominio salvo queries administrativas puntuales.
- Las pruebas deben ser PHPUnit Feature/Unit tests, no Pest.
- Los tests MCP deben seguir el patron actual: montar schemas necesarios en `setUp()` cuando la suite lo requiera.
- Cada cambio de implementacion debe incrementar `VERSION` y commitearlo.
- Antes de finalizar implementacion PHP, correr `vendor/bin/pint --dirty` y tests minimos afectados.

## MCP server

Crear un server separado para Buyback:

- Clase: `App\Mcp\Servers\BuybackPlannerServer`.
- Registro local: `Mcp::local('buyback-planner', BuybackPlannerServer::class)`.
- Registro web futuro: `Mcp::web('/mcp/buyback-planner', BuybackPlannerServer::class)->middleware('auth:api')`.
- Instrucciones del server: usar filtros estrechos, limites pequenos y operar solo sobre clasificacion, delegacion, playbooks y recomendaciones Buyback.

Motivo:

- `AriStudioServer` actual esta orientado a leer/crear/actualizar tareas operativas.
- Buyback es un framework de planeacion y delegacion; mezclarlo con tools operativas diluye las instrucciones del LLM.
- Si una tool Buyback necesita crear/actualizar tareas, debe hacerlo mediante servicios compartidos y permisos explicitos, no por pertenecer al server operativo.

## RBAC y modulos

Agregar seeds/migraciones de modulos antes de exponer UI, API o MCP:

- `/planning`: lectura y gestion general de Buyback.
- `/planning/buyback-audits`: auditorias.
- `/planning/delegation-candidates`: candidatos de delegacion.
- `/planning/support-needs`: necesidades de apoyo.
- `/planning/playbooks`: playbooks.
- `/planning/settings`: operador principal y configuracion.

Permisos minimos:

- Admin/role_id 1: todos.
- Operador principal configurado: `list`, `read`, `create`, `update`.
- Otros usuarios: ninguno por defecto salvo asignacion explicita.

Las MCP tools deben fallar con `No autorizado.` si el usuario no tiene permiso, siguiendo las tools actuales.

## Estados, subtareas y dependencia

### Estados planificables

Una tarea es planificable si:

- Tiene `status_id` asociado a `task_statuses.pending = true`, cuando esa columna exista.
- O su `status.alias` esta en `pending`, `in_progress`, `todo`, `doing`.
- O, en instalaciones sin esos metadatos, su `status_id` no corresponde al estado de completado usado por Timer (`6`) ni a estados cerrados configurados.

Fase 1 debe definir un helper `PlanningContextService::isTaskPlannable(Task $task): bool`.

### Subtareas

`tasks.parent_id` ya existe. Regla:

- Si una tarea tiene subtareas abiertas, clasificar cada subtarea cuando tenga responsable, puntos o vencimiento propio.
- La tarea padre puede recibir una clasificacion agregada solo para reporte.
- No duplicar auditorias para padre e hijos si el padre es solo contenedor.
- `BuybackAudit` debe guardar `task_id` real auditado y opcionalmente `parent_task_id` derivado para reportes.

### Dependencia de Luisa

Una tarea/proyecto depende de Luisa si Luisa aparece en cualquiera:

- `tasks.user_id`.
- `tasks.creator_user_id`.
- `tasks.updator_user_id`.
- `project_users.user_id` para el proyecto de la tarea.
- Futuras relaciones watcher/commenter si existen.

La dependencia por `creator_user_id` o `updator_user_id` no implica asignacion actual, pero si debe elevar revision de continuidad cuando la tarea sea critica.

## Timer y actual_minutes

El repo tiene `TimerController` y tests de persistencia. El flujo actual guarda tiempo como `tasks.points = round(seconds / 3600, 2)` al hacer `timer.store`; no persiste `started_at`/`finished_at` desde el timer.

Decision:

- Fase 1: `BuybackAudit.actual_minutes` se calcula desde `tasks.points * 60` cuando `points` proviene del timer o no hay mejor fuente.
- Si en el futuro se agregan columnas persistentes `started_at`/`finished_at`, el calculo puede preferir diferencia real de timestamps.
- No crear un segundo sistema de tracking de tiempo.

## Auditoria: sujeto vs auditor

`BuybackAudit` debe distinguir:

- `subject_user_id`: persona cuyo trabajo se audita, normalmente `operator_user_id`.
- `auditor_user_id`: persona o usuario autenticado que registra la auditoria.
- `owner_user_id`: se mantiene como campo canonico y representa creador/dueno administrativo del registro, no sujeto.

Regla:

- Las recomendaciones sobre "Nicolas" usan `subject_user_id`.
- Las autorizaciones usan `auditor_user_id` o usuario autenticado.
- Los filtros multiempresa usan `enterprise_id`.

## Review loop y outcomes

El ciclo Buyback implementable es:

1. Audit.
2. Replace/Transfer.
3. Refill.
4. Review.

Agregar seguimiento de outcomes:

- `delegation_outcomes` o campos equivalentes en `delegation_candidates`.
- Campos minimos: `delegation_candidate_id`, `reviewed_by_user_id`, `reviewed_at`, `outcome`: `working`, `failed`, `returned_to_operator`, `needs_playbook_update`, `paused`, `completed`, `notes`.
- `playbooks.last_reviewed_at` nullable.
- `support_needs.last_reviewed_at` nullable.

Cadencia:

- Revisar candidatos delegados cada 14 dias.
- Revisar playbooks `ready` cada 60 dias.
- Revisar support needs activos cada 30 dias.

## Taxonomia unica

Usar `support_areas` como tabla maestra para:

- Replacement Ladder.
- `Playbook.process_area`.
- `SupportNeed.role_type`/area funcional.
- Delegation candidates.

Cambios:

- `playbooks` no debe depender de `process_area` como campo canonico. Debe usar `support_area_id` nullable y `process_area_label` opcional solo para migracion/importacion.
- `support_needs.role_type` no debe ser enum rigido; debe derivarse de `support_area_id` o de un catalogo editable relacionado.
- `problem_solved` puede mantenerse como texto, pero para queries debe existir `problem_category` o `support_area_id`.

## Seguridad MCP destructiva

`MarkUserAsPlanningExit`:

- Debe llevar `#[IsDestructive(true)]`.
- Debe ser `#[IsIdempotent(true)]` si repetir la misma marca no genera efectos adicionales.
- Requiere permiso `/users`, `update` y `/planning/settings`, `update`.
- Requiere parametros de confirmacion:
  - `user_id`
  - `confirm_user_id_match` igual a `user_id`
  - `confirm_user_name` igual al nombre actual del usuario
  - `exit_status`
- Debe soportar `dry_run` default true.
- Con `dry_run=true`, solo devuelve tareas/proyectos afectados.
- Con `dry_run=false`, actualiza estado y crea registros de continuidad.
- No debe propagar cambios masivos invisibles; debe devolver `affected_tasks_count`, `affected_projects_count` y muestras limitadas.

## Canal de sugerencias

Si una regla "sugiere" crear playbook/candidato/support need, la sugerencia debe persistir.

Entidad recomendada: `planning_suggestions`.

Campos:

- `id`
- `enterprise_id` nullable
- `subject_user_id`
- `source_type`: `repeated_task`, `replacement_task`, `luisa_continuity`, `cootilca_dependency`, `review_due`
- `source_id` nullable
- `title`
- `description`
- `recommended_action`: `create_playbook`, `create_delegation_candidate`, `create_support_need`, `review_delegation`, `update_playbook`
- `status`: `open`, `accepted`, `dismissed`, `done`
- `created_at`
- `updated_at`

Sin esta tabla o UI equivalente, las sugerencias quedan invisibles y no deben contarse como implementadas.

## Mapping repo actual vs SPEC

| Necesidad del SPEC | Repo actual | Decision extender vs duplicar |
| --- | --- | --- |
| Valor economico de tarea | `tasks.value_generated` boolean, `tasks.points`, `tasks.estimated_points` | Extender. `economic_value_score` se guarda en `buyback_audits` como evaluacion contextual; no reemplaza `value_generated` ni `points`. La clasificacion debe usar `value_generated=true` y puntos altos como inputs sugeridos. |
| Valor/criticidad de proyecto | `projects.weight`, `projects.budget`, `projects.monthly_points_goal`, `projects.sales` | Extender. `strategic_value_score` puede derivarse parcialmente de `projects.weight` y override manual en audit. |
| Responsable actual | `tasks.user_id` FK a `users.id` | No duplicar como string. `DelegationCandidate` debe usar `current_owner_user_id` nullable FK; se puede exponer nombre serializado solo en responses. |
| Salida probable de Luisa | `users.termination_date`, `users.status_id` | Extender. Usar `termination_date` y `status_id` como senales existentes; agregar solo campos operativos faltantes: `exit_status`, `is_assignable`, `continuity_risk`, `needs_transition`. |
| Roles de apoyo | Tabla `roles` existe para RBAC y `role_modules` | No reutilizar `roles`. Para evitar colision semantica, renombrar entidad `SupportRole` a `SupportNeed` en implementacion o documentar que no es RBAC. Nombre recomendado: `support_needs`. |
| Tipo de rol de apoyo | No existe catalogo funcional | Crear `support_areas` seedable/editable. Evitar enum rigido. |
| Tiempo real dedicado | Timer usa session y al guardar convierte segundos a `tasks.points`; `started_at` y `finished_at` aparecen en casts pero no son fuente persistida del timer actual | Extender con cautela. Fase 1 calcula `actual_minutes` desde `tasks.points * 60` cuando aplique. No crear time tracking paralelo. |
| Tenant / empresa | `users.enterprise_id`; proyectos se asignan por `project_users` | Extender. Las tablas de planning deben tener `enterprise_id` nullable ademas de `owner_user_id` cuando el dato sea organizacional. `owner_user_id` representa operador/creador, no tenant. |
| Operador principal "Nicolas" | No existe `is_principal`, `operator_user_id` ni setting | Crear configuracion minima por empresa: `planning_operator_user_id` en una tabla de settings o config seedable. No hardcodear por nombre. |
| Calendario / bloques protegidos | No hay modelo de eventos; solo `due_date` y `delivery_date` en tasks | Crear `protected_blocks` en Fase 4 o integrar Google Calendar como fuente externa. Fases previas no deben prometer proteccion de calendario real. |
| Repeticion de tareas | No hay `template_id` ni recurrence | Fase 1 usa heuristica determinista exacta: nombre normalizado + `type_id` + `sub_type_id` + `project_id` en ventana de 30 dias. Semantica/embeddings quedan fuera. |

## Landing tecnico por capas

### Servicios compartidos obligatorios

Crear servicios antes de MCP/endpoints para evitar divergencia:

- `BuybackClassifier`: calcula DRIP y recommended action.
- `BuybackAuditService`: crea/actualiza auditorias idempotentes.
- `DelegationRecommendationService`: puntua `support_areas` y recomienda primer apoyo.
- `PlanningContextService`: resuelve operador principal, empresa, COOTILCA y exclusiones de Laura.

Los tests deben cubrir estos servicios directamente. MCP y endpoints futuros solo orquestan request/response.

### REST

El repo no tiene `routes/api.php`. Por eso:

- Fase 1: sin endpoints REST.
- Fase 3: decidir una de estas rutas antes de implementar:
  - Crear `routes/api.php` con middleware `auth:api` porque el repo ya usa Passport y MCP web usa `auth:api`.
  - O exponer rutas web autenticadas en `routes/web.php` si son pantallas internas con CSRF.
- Si se crea API, usar prefijo versionado `/api/v1/planning/...`.
- Todo request debe usar Form Request.
- Todo response API debe usar Resources o arrays estructurados consistentes con los MCP outputs.

### MCP

Las tools se implementan en `app/Mcp/Tools` y se registran en `App\Mcp\Servers\BuybackPlannerServer` en Fase 2.

Naming de clases:

| Nombre funcional | Clase MCP recomendada | Annotation | Permiso |
| --- | --- | --- | --- |
| run_buyback_audit | `RunBuybackAudit` | `#[IsDestructive(false)]`, `#[IsIdempotent(true)]` | `/planning`, `create` |
| classify_task_buyback | `ClassifyTaskBuyback` | `#[IsReadOnly]` si `dry_run=true`; si persiste, `#[IsDestructive(false)]` | `/planning`, `read` o `create` segun modo |
| list_delegation_candidates | `ListDelegationCandidates` | `#[IsReadOnly]` | `/planning`, `read` |
| create_delegation_candidate | `CreateDelegationCandidate` | `#[IsDestructive(false)]`, `#[IsIdempotent(false)]` | `/planning`, `create` |
| create_playbook_from_task | `CreatePlaybookFromTask` | `#[IsDestructive(false)]`, `#[IsIdempotent(false)]` | `/planning`, `create` |
| list_support_roles | `ListSupportNeeds` | `#[IsReadOnly]` | `/planning`, `read` |
| recommend_first_support_role | `RecommendFirstSupportNeed` | `#[IsReadOnly]` | `/planning`, `read` |
| mark_team_member_as_exiting | `MarkUserAsPlanningExit` | `#[IsDestructive(true)]`, `#[IsIdempotent(true)]` | `/users`, `update` + `/planning/settings`, `update` |
| get_cootilca_operating_profile | `GetCootilcaOperatingProfile` | `#[IsReadOnly]` | `/projects`, `read` |

Los nombres snake_case pueden usarse solo como identificadores conceptuales en prompts; las clases deben ser PascalCase.

### Identidad de Nicolas / operador principal

No hardcodear "Nicolas" por nombre, email ni id.

Fase 1 debe crear una forma explicita de resolver al operador principal:

- Opcion preferida: tabla `planning_settings` con `enterprise_id`, `operator_user_id`, `one_thing_project_id` nullable.
- Opcion minima: config seedable con email/id documentado solo para ambiente local.

Regla:

- En textos de negocio se puede decir Nicolas.
- En codigo se usa `operator_user_id`.
- `owner_user_id` es creador/dueno del registro de planning; no necesariamente operador principal ni tenant.

### Calendario y bloques protegidos

Hasta que exista `protected_blocks` o integracion Google Calendar:

- El sistema solo puede clasificar tareas y recomendar prioridades.
- No puede garantizar que COOTILCA no desplace bloques familiares/personales.
- Los criterios sobre Wake 5:30, meditacion, carrera, bachata/salsa y Botanazo solo pueden existir en Fase 1 como preferencias seed/configuradas; no como proteccion efectiva de agenda hasta Fase 4.

Fase 4 debe escoger:

- Tabla local `protected_blocks` como fuente de verdad.
- Integracion Google Calendar como fuente de verdad externa.
- Modelo hibrido: sincronizar Google Calendar hacia `protected_blocks`.

Para este repo, la opcion local es mas verificable en tests; Google Calendar puede ser una integracion posterior.

### Laura, Luisa y criticidad

Laura:

- Implementacion compatible con el repo: excluir de seeds, sugerencias y respuestas del planner.
- No hard-delete porque `users` no usa SoftDeletes y `tasks.user_id` puede referenciar historico.
- Si existe en BD, marcar no asignable para planning y no mostrarla como opcion futura.

Luisa:

- "Proceso critico" debe definirse de forma falsable.
- Una tarea/proceso es critico si cumple cualquiera:
  - proyecto con `projects.weight <= 3` si menor peso significa mayor prioridad visual en listas;
  - proyecto marcado estrategico por planning metadata;
  - tarea con `priority >= 4`;
  - tarea asociada a COOTILCA, AriCRM o Reto21Vendo+;
  - audit con `strategic_value_score >= 4`.
- Luisa puede aparecer solo en tareas de cierre/transicion. Si una tarea critica queda asignada a ella, el servicio debe marcar riesgo y sugerir transferencia.

### Naming

- Clases PHP: PascalCase en ingles.
- Tablas: snake_case plural en ingles.
- Endpoints futuros: kebab-case bajo `/api/v1/planning`.
- MCP classes: PascalCase; nombres conceptuales en documentacion pueden estar en snake_case.
- Estados persistidos: snake_case en ingles.
- Texto visible al usuario: espanol sin depender de nombres de clases.
- `recommended_first_hire` queda como nombre descartado; `recommend_first_support_role` queda como alias conceptual; el concepto canonico es `recommended_first_support_need`.

## Decisiones v2

Estas decisiones siguen vigentes solo cuando no contradicen la seccion v3. Si hay conflicto, prevalece v3.

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

- Laura debe quedar completamente excluida del subsistema de planning.
- No debe existir como miembro asignable, recurso, responsable, opcion de delegacion ni compromiso diario dentro de planning.
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
4. Review: revisar outcomes, actualizar playbooks, validar delegaciones y recalibrar prioridades.

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
- Crear `SupportNeed`

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
- `enterprise_id` nullable
- `owner_user_id`
- `subject_user_id`
- `auditor_user_id`
- `audit_date`
- `task_id` nullable
- `parent_task_id` nullable
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
- Todos los scores manuales deben ser nullable para permitir estado `unclassified` cuando falten datos.
- `drip_quadrant`: `delegation`, `replacement`, `investment`, `production`
- `recommended_action`: `keep`, `delegate`, `automate`, `eliminate`, `document`, `create_support_need`
- `framework_version`
- `classification_hash`
- `audit_fingerprint`
- `notes`
- `created_at`
- `updated_at`

Idempotencia:

- Debe existir una clave idempotente no nullable, por ejemplo `audit_fingerprint`, derivada de `owner_user_id`, `subject_user_id`, `audit_date`, `task_id`, `project_id` y `classification_hash`.
- `audit_fingerprint` debe tener indice unico. No depender de un `UNIQUE` con columnas nullable porque permitiria duplicados en motores como MySQL.
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
- `current_owner_user_id` nullable
- `recommended_owner_type`: `freelancer`, `contractor`, `employee`, `agency`, `automation`, `eliminate`
- `support_area_id` nullable
- `support_need_id` nullable
- `role_needed`
- `required_skills_json`
- `estimated_hours_per_week`
- `estimated_budget_monthly`
- `urgency`: `low`, `medium`, `high`, `critical`
- `risk_if_not_delegated`
- `documentation_status`: `none`, `partial`, `ready`
- `status`: `candidate`, `approved`, `sourcing`, `delegated`, `automated`, `eliminated`
- `last_reviewed_at` nullable
- `created_at`
- `updated_at`

Relacion con SupportNeed:

- Un `DelegationCandidate` puede sugerir o vincular un `SupportNeed`.
- Delegar o automatizar un candidato no cierra automaticamente el rol.
- Un `SupportNeed` se marca `active`, `paused` o `rejected` segun el estado real de contratacion/experimento.

### SupportNeed

Nombre de producto: rol de apoyo. Nombre tecnico recomendado: `SupportNeed` para no confundirse con `Role` RBAC.

Tabla: `support_needs`

Campos:

- `id`
- `owner_user_id`
- `title`
- `support_area_id` nullable
- `role_type` nullable: etiqueta visible o borrador humano derivable de `support_area_id`; no es enum rigido ni el campo canonico de clasificacion
- `problem_solved`
- `problem_category` nullable
- `responsibilities_json`
- `not_responsible_for_json`
- `required_skills_json`
- `estimated_hours_weekly`
- `budget_range`
- `priority`
- `status`: `needed`, `sourcing`, `testing`, `active`, `paused`, `rejected`
- `last_reviewed_at` nullable
- `created_at`
- `updated_at`

### Playbook

Tabla: `playbooks`

Campos:

- `id`
- `owner_user_id`
- `title`
- `support_area_id` nullable
- `process_area_label` nullable
- `related_task_id` nullable
- `related_project_id` nullable
- `description`
- `checklist_json`
- `assets_json` nullable
- `sop_body`
- `status`: `draft`, `ready`, `tested`, `deprecated`
- `last_reviewed_at` nullable
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

Fase 4, salvo que ya exista una entidad equivalente.

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

Fase 4, salvo que los compromisos personales se modelen como `protected_blocks`.

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

### assets_json

```json
[
  {
    "type": "loom",
    "url": "https://www.loom.com/share/example",
    "title": "Ejecucion grabada",
    "notes": "Primera version del proceso"
  }
]
```

Reglas:

- `type`: `loom`, `video`, `template`, `doc`, `sheet`, `other`.
- `url`: string URL requerido.
- `title`: string nullable.
- `notes`: string nullable.

## Migraciones esperadas

Nombres sugeridos:

- `create_support_areas_table`
- `create_buyback_audits_table`
- `create_support_needs_table`
- `create_delegation_candidates_table`
- `create_playbooks_table`
- `create_planning_suggestions_table`
- `create_delegation_outcomes_table`
- `add_planning_status_fields_to_users_table`
- `create_planning_settings_table`
- Fase 4: `create_protected_blocks_table`
- Fase 4: `create_personal_commitments_table`

Convenciones:

- Usar FKs para `owner_user_id`, `subject_user_id`, `auditor_user_id`, `operator_user_id`, `enterprise_id` cuando aplique, `task_id`, `parent_task_id`, `project_id`, `support_area_id` y `support_need_id`.
- `owner_user_id` debe apuntar a `users.id`.
- `current_owner_user_id` debe apuntar a `users.id` y reemplaza cualquier `current_owner` string persistido.
- `task_id` y `project_id` deben ser nullable con `nullOnDelete()`.
- No usar enum de base de datos para areas editables.
- Para estados cerrados y pequenos, se permiten strings validados por Form Request y constantes de modelo.

Factories y seeders requeridos:

- Factory para `BuybackAudit`, `DelegationCandidate`, `SupportNeed`, `Playbook`, `PlanningSuggestion`, `DelegationOutcome`.
- Seeder para `support_areas`, modulos RBAC de planning, COOTILCA, planning settings y contexto Luisa/Laura cuando aplique.
- Los seeders no deben crear Laura.

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

- Todos los endpoints futuros `/api/v1/planning/...` requieren usuario autenticado.
- `owner_user_id` se deriva exclusivamente del token/session del usuario autenticado.
- El body no puede definir `owner_user_id`.
- Por defecto, el operador principal configurado ve sus datos y administradores pueden ver datos de otros usuarios con filtros explicitos.
- Usuarios no administradores solo pueden listar/crear recursos propios.
- Las herramientas MCP deben usar `$request->user()` y los permisos existentes del modulo correspondiente.
- Para crear o modificar tareas: permiso `/tasks` con `create` o `update`.
- Para leer planeacion: permiso futuro `/planning` con `read` o rol admin.
- Para crear auditorias, candidatos, roles y playbooks: permiso futuro `/planning` con `create` o rol admin.

## Recomendacion de primera necesidad de apoyo

El sistema debe recomendar la primera necesidad de apoyo a resolver desde datos, no desde intuicion.

No usar "hire" como concepto canonico porque la accion puede ser contratar, delegar, automatizar o eliminar.

Factores de decision:

- Tareas mas repetidas.
- Tareas que mas drenan.
- Tareas de bajo valor economico.
- Tareas que bloquean proyectos estrategicos.
- Tareas que Nicolas no deberia hacer.
- Riesgo de continuidad por salida probable de Luisa.
- Dependencia directa de Nicolas en COOTILCA.

La recomendacion debe devolver:

- Support need recomendado.
- Area del Replacement Ladder.
- Problema principal que resuelve.
- Evidencia usada.
- Tareas candidatas relacionadas.
- Riesgo de no contratar/delegar.
- Primer experimento sugerido.

## Endpoints futuros

Estos endpoints son Fase 3. No forman parte del MVP porque el repo actual no tiene `routes/api.php`.

Forma final recomendada si se habilita API:

- `GET /api/v1/planning/buyback-audit`
- `POST /api/v1/planning/buyback-audit`
- `POST /api/v1/planning/tasks/{task}/buyback-classify`
- `GET /api/v1/planning/delegation-candidates`
- `POST /api/v1/planning/delegation-candidates`
- `GET /api/v1/planning/support-needs`
- `POST /api/v1/planning/support-needs`
- `GET /api/v1/planning/playbooks`
- `POST /api/v1/planning/playbooks`
- `POST /api/v1/planning/playbooks/from-task`
- `GET /api/v1/planning/recommended-first-support-need`
- `GET /api/v1/planning/buyback-metrics`

Reglas de listado:

- Todos los `GET` deben aceptar `limit` con default 30 y maximo 100.
- Listados grandes deben aceptar filtros por `project_id`, `task_id`, `support_area_id`, `status`, `from`, `to` segun aplique.
- Ningun endpoint debe devolver todas las tareas abiertas sin limite.

Metricas esperadas en `GET /api/v1/planning/buyback-metrics`:

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
- `dry_run` boolean default true.

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
- `process_area_label` string nullable; solo para migracion/importacion o borrador humano antes de mapear `support_area_id`.
- `support_area_id` integer nullable.
- `include_task_description` boolean default true.

Output schema:

- `message` string.
- `playbook` object con `id`, `title`, `status`, `related_task_id`, `checklist_json`.

### list_support_roles

Alias conceptual heredado. La clase implementable debe llamarse `ListSupportNeeds`.

Input schema:

- `support_area_id` integer nullable.
- `status` string nullable.
- `priority` integer nullable.
- `limit` integer default 30, min 1, max 100.

Output schema:

- `count` integer.
- `support_needs` array con `id`, `title`, `support_area`, `role_type`, `priority`, `status`, `estimated_hours_weekly`.

Implementacion recomendada:

- Clase: `ListSupportNeeds`.
- Output key: `support_needs`, no `roles`, para evitar confusion con RBAC.

### recommend_first_support_role

Alias conceptual heredado. La clase implementable debe llamarse `RecommendFirstSupportNeed` y el concepto canonico es `recommended_first_support_need`.

Input schema:

- `project_id` integer nullable.
- `from` string date nullable.
- `to` string date nullable.
- `include_cootilca` boolean default true.
- `limit` integer default 30, min 1, max 100.

Output schema:

- `recommended_support_need` object.
- `support_area` object.
- `score` integer.
- `factor_scores` object.
- `evidence_tasks` array limitado.
- `risk_if_not_resolved` string.
- `first_experiment` string.

Implementacion recomendada:

- Clase: `RecommendFirstSupportNeed`.
- Concepto canonico: `recommended_first_support_need`.

### mark_team_member_as_exiting

Marca a una persona como salida probable y activa riesgo de continuidad.

Input schema:

- `user_id` integer required.
- `confirm_user_id_match` integer required; debe ser igual a `user_id`.
- `confirm_user_name` string required; debe coincidir con el nombre actual del usuario.
- `exit_status` string required: `probable_exit` o `exited`.
- `transition_notes` string nullable.
- `dry_run` boolean default true.

Output schema:

- `message` string.
- `user` object con `id`, `name`, `exit_status`, `is_assignable`, `continuity_risk`, `needs_transition`.
- `dry_run` boolean.
- `affected_tasks_count` integer.
- `affected_projects_count` integer.
- `affected_tasks` array limitado.

Regla:

- Opera sobre `users`; no requiere entidad `TeamMember`.
- Si el usuario tiene tareas abiertas, marca esas tareas para revision de continuidad segun el mecanismo existente o una tabla de metadata de planeacion.
- No debe modificar datos cuando `dry_run=true`.
- Debe rechazar la ejecucion si `confirm_user_id_match` o `confirm_user_name` no coinciden.

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

- Wake 5:30 am como preferencia/configuracion inicial.
- Meditacion como preferencia/configuracion inicial.
- Carrera como preferencia/configuracion inicial.
- Bachata / salsa como preferencia/configuracion inicial.
- Botanazo como preferencia/configuracion inicial; no como bloque efectivamente protegido en Fase 1.
- AriCRM / Reto21Vendo+ como preferencia de One Thing en configuracion; no como proteccion efectiva de agenda en Fase 1.

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

- Reencuadre como subsistema nuevo.
- Mapping contra `users`, `tasks` y `projects`.
- Correcciones de contexto verificables: Laura, teatro musical, Luisa.
- COOTILCA en seeds.
- Modulos RBAC de planning.
- `support_areas`.
- `buyback_audits`.
- `delegation_candidates`.
- `support_needs`.
- `playbooks`.
- `planning_suggestions`.
- `delegation_outcomes`.
- `planning_settings` para resolver `operator_user_id`.
- Servicios compartidos de clasificacion y auditoria.
- DRIP manual con tests.
- Sin endpoints REST.
- Sin calendario real.
- Sin MCP nuevo salvo que Fase 1 se amplie explicitamente.

### Fase 2

- `BuybackPlannerServer` separado.
- MCP tools principales registradas en `BuybackPlannerServer`.
- Listados con limites y filtros.
- `RunBuybackAudit`, `ClassifyTaskBuyback`, `ListDelegationCandidates`, `CreateDelegationCandidate`, `CreatePlaybookFromTask`, `ListSupportNeeds`, `RecommendFirstSupportNeed`, `MarkUserAsPlanningExit`, `GetCootilcaOperatingProfile`.
- Metricas basicas desde servicios compartidos.

### Fase 3

- Crear `routes/api.php` con `auth:api` o decidir rutas web internas.
- Endpoints `/api/v1/planning/*`.
- Form Requests, Resources y controladores.
- Recomendador de primer apoyo expuesto por API.

### Fase 4

- `protected_blocks` o integracion Google Calendar.
- `personal_commitments` si no se modelan como protected blocks.
- Proteccion real de calendario.
- Recalculo historico por version de framework.
- Admin UI para editar Replacement Ladder, pesos y umbrales.

## Criterios de aceptacion

- Laura no aparece en seeds, asignaciones, reglas, opciones de delegacion, herramientas MCP ni respuestas del planificador.
- Teatro musical no aparece como compromiso, restriccion ni bloque de agenda.
- Bachata y salsa permanecen como compromisos validos.
- Luisa puede existir solo como recurso transitorio y salida probable.
- Las tareas dependientes de Luisa quedan marcadas con riesgo de continuidad y necesidad de transicion.
- COOTILCA existe como proyecto estrategico de alta importancia.
- Fase 1 solo registra que COOTILCA no debe desplazar AriCRM / Reto21Vendo+ ni bloques protegidos; la proteccion real de calendario queda para Fase 4.
- Cada tarea planificable puede evaluarse con DRIP Matrix.
- Las tareas Delegation quedan marcadas como no recomendadas para el operador principal salvo emergencia.
- Las tareas Replacement generan una sugerencia persistida de salida.
- Las tareas repetidas 3 veces o mas generan sugerencias persistidas de playbook, candidato de delegacion y support need; la autocreacion solo aplica si la fase implementada la habilita explicitamente.
- Fase 2: las herramientas MCP usan los mismos servicios compartidos que usaran los endpoints futuros.
- Fase 3: el endpoint de primera necesidad de apoyo entrega una recomendacion basada en evidencia de tareas, energia, valor, urgencia y bloqueos.
- Los listados y tools MCP respetan `limit` default 30 y maximo 100.
- `run_buyback_audit` es idempotente para mismos inputs y mismo dia operativo.
- `owner_user_id` no se acepta desde el body.
- Las areas del Replacement Ladder son seedables/editables, no enums hardcodeados.
- Las columnas JSON se validan contra el schema canonico.
- El SPEC se implementa como subsistema nuevo y ningun test debe asumir que ya existia `/api/planning`.
- La clasificacion DRIP usa un servicio compartido consumible por MCP y endpoints futuros.
- `current_owner` no se persiste como string; se usa `current_owner_user_id`.
- `SupportNeed` no reutiliza ni modifica la tabla `roles` de RBAC.
- El operador principal se resuelve por configuracion, no por nombre "Nicolas".
- Las reglas de calendario quedan fuera de Fase 1 salvo que exista `protected_blocks` o integracion de calendario.
- `MarkUserAsPlanningExit` es destructiva, requiere confirmacion y corre en `dry_run` por defecto.
- `BuybackAudit` distingue `subject_user_id` y `auditor_user_id`.
- Las sugerencias automaticas quedan en `planning_suggestions` o canal equivalente verificable.

## Pruebas esperadas

- Unit tests para servicios de dominio: classifier, audit idempotente, recomendador, contexto planning.
- Feature tests MCP en Fase 2 siguiendo el patron de `tests/Feature/Mcp/*`.
- Feature tests API solo en Fase 3, cuando exista `routes/api.php`.
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
- Tests del mapping contra `tasks.value_generated`, `tasks.points`, `projects.weight`, `users.termination_date` y `users.status_id`.
- Tests de exclusion de Laura sin hard-delete.
- Tests de criticidad de Luisa con reglas falsables.
- Tests de servicios compartidos para evitar divergencia MCP/API.
- Tests de resolucion de `operator_user_id`.
- Tests de modulos RBAC `/planning/*` y permisos `hasModulePermission()`.
- Tests de estados planificables usando `task_statuses`.
- Tests de subtareas con `tasks.parent_id`.
- Tests de dependencia de Luisa por assignee, creator, updator y `project_users`.
- Tests de `actual_minutes` derivado del flujo real del timer/puntos.
- Tests de `MarkUserAsPlanningExit` con `dry_run`, confirmacion fallida y confirmacion exitosa.
- Tests de review/outcomes y sugerencias persistidas.
