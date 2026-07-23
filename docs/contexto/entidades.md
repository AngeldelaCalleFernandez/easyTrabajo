# entidades.md

# EasyParte — Entidades principales

## 1. Propósito del documento

Este documento describe las entidades principales del modelo de datos objetivo de EasyParte.

No sustituye al SQL final, pero sirve como guía de negocio y arquitectura para construir la base de datos, los modelos, los endpoints y la interfaz.

---

## 2. Bloques del modelo

El modelo se organiza en estos bloques:

1. SaaS y suscripciones.
2. Empresas, usuarios y roles.
3. Estructura interna.
4. Clientes, avisos y presupuestos.
5. Partes de trabajo.
6. Materiales y almacén.
7. Facturación, trazabilidad y auditoría.

---

## 3. PLAN

Define los planes comerciales de EasyParte.

Campos principales:

- id_plan;
- nombre;
- tipo;
- min_usuarios;
- max_usuarios;
- max_avisos;
- max_partes;
- max_clientes;
- precio_mensual;
- requiere_contacto_comercial;
- activo;
- created_at;
- updated_at.

Relaciones:

- Un plan puede estar asociado a muchas suscripciones.

---

## 4. SUSCRIPCION

Representa la contratación de un plan por parte de una empresa.

Campos principales:

- id_suscripcion;
- id_empresa;
- id_plan;
- estado;
- fecha_inicio;
- fecha_fin;
- renovacion_automatica;
- created_at;
- updated_at.

Relaciones:

- Una suscripción pertenece a una empresa.
- Una suscripción pertenece a un plan.
- Una empresa puede tener varias suscripciones históricas.

Estados posibles:

- prueba;
- activa;
- pendiente_pago;
- cancelada;
- caducada;
- bloqueada.

---

## 5. EMPRESA

Representa a una empresa cliente que usa EasyParte.

Campos principales:

- id_empresa;
- nombre;
- nif;
- email;
- telefono;
- direccion;
- poblacion;
- pais;
- activa;
- created_at;
- updated_at;
- deleted_at.

Relaciones:

- Una empresa tiene usuarios vinculados.
- Una empresa tiene empleados.
- Una empresa tiene departamentos.
- Una empresa tiene clientes.
- Una empresa tiene avisos.
- Una empresa tiene presupuestos.
- Una empresa tiene partes.
- Una empresa tiene materiales.
- Una empresa tiene auditoría.

---

## 6. USUARIO

Representa una persona que puede iniciar sesión.

Campos principales:

- id_usuario;
- nombre;
- email;
- password_hash;
- activo_global;
- ultimo_login_at;
- created_at;
- updated_at;
- deleted_at.

Relaciones:

- Un usuario puede estar vinculado a varias empresas mediante `empresa_usuario`.
- Un usuario puede crear avisos.
- Un usuario puede crear presupuestos.
- Un usuario puede generar eventos de auditoría.

---

## 7. EMPRESA_USUARIO

Tabla intermedia que vincula usuario y empresa.

Permite que un usuario tenga distintos roles en distintas empresas.

Campos principales:

- id_empresa_usuario;
- id_empresa;
- id_usuario;
- id_empleado;
- estado;
- bloqueado;
- bloqueado_por;
- bloqueado_at;
- created_at;
- updated_at;
- deleted_at.

Relaciones:

- Pertenece a una empresa.
- Pertenece a un usuario.
- Puede estar vinculada a un empleado.
- Tiene roles mediante `empresa_usuario_rol`.

---

## 8. ROL

Define permisos generales.

Roles previstos:

- administrador_jefe;
- administrador;
- jefe_departamento;
- jefe_equipo;
- atencion_cliente;
- tecnico;
- solo_lectura.

Campos principales:

- id_rol;
- nombre;
- descripcion;
- activo;
- created_at;
- updated_at.

Relaciones:

- Un rol puede estar asignado a muchas vinculaciones empresa_usuario.

---

## 9. EMPRESA_USUARIO_ROL

Asocia roles a usuarios dentro de una empresa concreta.

Campos principales:

- id_empresa_usuario_rol;
- id_empresa_usuario;
- id_rol;
- created_at.

Relaciones:

- Pertenece a empresa_usuario.
- Pertenece a rol.

---

## 10. DEPARTAMENTO

Agrupa empleados y avisos dentro de una empresa.

Campos principales:

- id_departamento;
- id_empresa;
- id_jefe_departamento;
- nombre;
- activo;
- created_at;
- updated_at;
- deleted_at.

Relaciones:

- Pertenece a una empresa.
- Puede tener muchos empleados.
- Puede tener muchos equipos.
- Puede recibir avisos.

---

## 11. EMPLEADO

Representa a una persona trabajadora de la empresa.

Campos principales:

- id_empleado;
- id_empresa;
- id_departamento;
- nombre;
- apellido;
- email;
- telefono;
- nif;
- activo;
- created_at;
- updated_at;
- deleted_at.

Relaciones:

- Pertenece a una empresa.
- Puede pertenecer a un departamento.
- Puede estar vinculado a usuario.
- Puede participar en avisos.
- Puede participar en partes.
- Puede registrar horas.

---

## 12. EQUIPO

Agrupa empleados dentro de un departamento.

Campos principales:

- id_equipo;
- id_empresa;
- id_departamento;
- id_jefe_equipo;
- nombre;
- activo;
- created_at;
- updated_at;
- deleted_at.

Relaciones:

- Pertenece a una empresa.
- Pertenece a un departamento.
- Tiene un jefe de equipo.
- Tiene empleados mediante `equipo_empleado`.

---

## 13. EQUIPO_EMPLEADO

Asocia empleados a equipos.

Campos principales:

- id_equipo_empleado;
- id_equipo;
- id_empleado;
- created_at.

Relaciones:

- Pertenece a un equipo.
- Pertenece a un empleado.

---

## 14. CLIENTE

Representa al cliente final de la empresa.

Campos principales:

- id_cliente;
- id_empresa;
- nombre;
- nif;
- email;
- telefono;
- direccion;
- poblacion;
- contacto;
- activo;
- created_at;
- updated_at;
- deleted_at.

Relaciones:

- Pertenece a una empresa.
- Puede tener muchos avisos.
- Puede recibir presupuestos.
- Puede tener partes asociados.

---

## 15. AVISO

Representa una incidencia, solicitud o trabajo pendiente.

Campos principales:

- id_aviso;
- id_empresa;
- id_cliente;
- id_departamento;
- id_usuario_creador;
- titulo;
- descripcion;
- prioridad;
- estado;
- fecha_alta;
- fecha_prevista;
- fecha_cierre;
- created_at;
- updated_at;
- deleted_at.

Relaciones:

- Pertenece a una empresa.
- Pertenece a un cliente.
- Puede estar asociado a departamento.
- Lo crea un usuario.
- Puede tener varios técnicos mediante `aviso_empleado`.
- Puede tener presupuestos.
- Puede generar partes.

---

## 16. AVISO_EMPLEADO

Asocia técnicos o empleados a un aviso.

Campos principales:

- id_aviso_empleado;
- id_aviso;
- id_empleado;
- asignado_por;
- rol_en_aviso;
- fecha_asignacion;
- activo.

Relaciones:

- Pertenece a un aviso.
- Pertenece a un empleado.
- Registra quién asignó.

---

## 17. PRESUPUESTO

Representa una propuesta económica básica.

Campos principales:

- id_presupuesto;
- id_empresa;
- id_cliente;
- id_aviso;
- id_usuario_creador;
- numero_presupuesto;
- titulo;
- descripcion;
- estado;
- fecha_emision;
- fecha_validez;
- subtotal;
- descuento;
- impuestos;
- total;
- aceptado_at;
- rechazado_at;
- convertido_en_parte_at;
- observaciones;
- created_at;
- updated_at;
- deleted_at.

Relaciones:

- Pertenece a empresa.
- Pertenece a cliente.
- Puede estar vinculado a aviso.
- Lo crea un usuario.
- Tiene líneas.
- Puede originar partes.

---

## 18. PRESUPUESTO_LINEA

Representa cada concepto de un presupuesto.

Campos principales:

- id_presupuesto_linea;
- id_presupuesto;
- id_material;
- tipo;
- descripcion;
- cantidad;
- precio_unitario;
- descuento;
- impuesto;
- total;
- orden.

Tipos posibles:

- mano_obra;
- material;
- desplazamiento;
- servicio;
- otro.

---

## 19. PRESUPUESTO_HISTORIAL_ESTADO

Guarda cambios de estado del presupuesto.

Campos principales:

- id_historial;
- id_presupuesto;
- id_usuario;
- estado_anterior;
- estado_nuevo;
- motivo;
- created_at.

---

## 20. PARTE_TRABAJO

Documento que registra el trabajo realizado.

Campos principales:

- id_parte_trabajo;
- id_empresa;
- id_aviso;
- id_cliente;
- id_presupuesto;
- descripcion;
- estado;
- fecha_inicio;
- fecha_fin;
- horas_total;
- observaciones;
- facturado;
- facturado_at;
- cerrado_at;
- cerrado_por;
- rectificable_hasta;
- bloqueado_definitivo;
- created_at;
- updated_at;
- deleted_at.

Relaciones:

- Pertenece a empresa.
- Pertenece a aviso.
- Pertenece a cliente.
- Puede venir de presupuesto.
- Tiene empleados.
- Tiene horas.
- Tiene materiales.
- Tiene firma.
- Tiene hash.
- Puede tener rectificaciones.

---

## 21. PARTE_EMPLEADO

Asocia empleados a un parte.

Campos principales:

- id_parte_empleado;
- id_parte_trabajo;
- id_empleado;
- rol_en_parte;
- created_at.

---

## 22. PARTE_HORA

Registra franjas de horas trabajadas.

Campos principales:

- id_parte_hora;
- id_parte_trabajo;
- id_empleado;
- fecha_inicio;
- fecha_fin;
- horas;
- tipo_hora;
- created_at.

---

## 23. PARTE_MATERIAL

Registra materiales usados en un parte.

Campos principales:

- id_parte_material;
- id_parte_trabajo;
- id_material;
- descripcion_manual;
- cantidad;
- precio_unitario;
- total;
- created_at.

---

## 24. MATERIAL

Representa material de catálogo o almacén.

Campos principales:

- id_material;
- id_empresa;
- codigo;
- nombre;
- descripcion;
- unidad;
- precio_coste;
- precio_venta;
- stock_actual;
- activo;
- created_at;
- updated_at;
- deleted_at.

---

## 25. MOVIMIENTO_ALMACEN

Registra movimientos de material.

Campos principales:

- id_movimiento;
- id_empresa;
- id_material;
- tipo;
- cantidad;
- origen;
- id_origen;
- created_at.

Tipos:

- entrada;
- salida;
- ajuste;
- devolucion.

---

## 26. PARTE_FIRMA

Guarda la firma del cliente asociada a un parte.

Campos principales:

- id_parte_firma;
- id_parte_trabajo;
- ruta_archivo;
- hash_firma;
- algoritmo_hash;
- nombre_firmante;
- documento_firmante;
- fecha_firma;
- ip;
- user_agent.

---

## 27. PARTE_HASH

Guarda el hash de integridad del parte.

Campos principales:

- id_parte_hash;
- id_parte_trabajo;
- hash_actual;
- hash_anterior;
- algoritmo;
- payload_firmado;
- version;
- created_at.

---

## 28. PARTE_RECTIFICACION

Registra solicitudes o autorizaciones de rectificación.

Campos principales:

- id_rectificacion;
- id_parte_trabajo;
- id_usuario_solicita;
- id_usuario_autoriza;
- motivo;
- estado;
- fecha_solicitud;
- fecha_autorizacion;
- fecha_limite.

---

## 29. EXPORTACION_FACTURACION

Registra exportaciones a sistemas externos.

Campos principales:

- id_exportacion;
- id_empresa;
- tipo_origen;
- id_origen;
- sistema_destino;
- estado;
- payload_exportado;
- respuesta_externa;
- fecha_exportacion;
- created_at.

---

## 30. AUDITORIA_EVENTO

Registra acciones importantes.

Campos principales:

- id_evento;
- id_empresa;
- id_usuario;
- entidad;
- entidad_id;
- accion;
- descripcion;
- valores_anteriores;
- valores_nuevos;
- ip;
- user_agent;
- created_at.
