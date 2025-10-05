# VUT App - Proyecto DAW 2º Curso

Aplicación web de gestión de vivienda de uso turístico (VUT) desarrollada con **Laravel 12, MySQL y TailwindCSS**.  
Proyecto académico para 2º de DAW (septiembre - diciembre 2025).

## Objetivo
Facilitar la gestión directa de un alojamiento turístico sin depender de plataformas intermediarias (Airbnb, Booking, etc.).  
Permite al propietario controlar reservas, calendario y precios, mientras el cliente puede consultar disponibilidad, reservar y recibir confirmaciones automáticas.

## Funcionalidades principales
- **Autenticación y roles:** registro/login para clientes y administrador.
- **Gestión de propiedades:** datos, fotos y calendario de precios.
- **Reservas:** formulario con validaciones (capacidad, estancia mínima, solapamientos).
- **Pagos simulados:** flujo de pago ficticio con generación de factura HTML.
- **Facturación:** numeración automática de facturas y descarga imprimible.
- **Emails (sandbox):** confirmaciones y avisos a través de Mailtrap.
- **Panel de administración:** listado, cancelación y control de reservas.
- **Perfil de cliente:** edición de avatar y acceso a "mis reservas" y "mis facturas".
- **Interfaz pública:** carrusel, pestañas informativas, mapa y formulario de contacto.

## Tecnologías utilizadas
- **Backend:** PHP 8.2 · Laravel 12 · Breeze · Blade · Eloquent ORM
- **Frontend:** TailwindCSS · Vite · TypeScript (componentes dinámicos)
- **Base de datos:** MySQL 8.x (con migraciones y seeders)
- **Emails:** Mailtrap (entorno de pruebas SMTP)
- **Pruebas:** Pest (unitarias e integradas)
- **Control de versiones:** Git + GitHub

## Estado del proyecto
En desarrollo...  
Versión actual: v0.3.1  
Entrega final prevista: 12 de diciembre de 2025  
Demo pública: *(pendiente de despliegue)*  

## Autor
**Raquel Fernández Santos**  
2º DAW · Curso 2025  
Proyecto guiado por **Mario Álvarez Fernández**  
