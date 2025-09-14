El Esta aplicación consiste de un proyecto que buscar solucionar gestión de empleados en Zitro.

El stack de la aplicación:
\- contenedores hecho con Docker
\- frontend hecho con vue
\- gestión de datos hecha con mysql
\- Api hecha con PHP

Para iniciar la aplicación es necesario crear el fichero .env en la raiz del proyecto

```
# App
APP_ENV=local
APP_DEBUG=true

# MySQL
DB_HOST=db
DB_PORT=3306
DB_DATABASE=myapp
DB_USERNAME=myuser
DB_PASSWORD=mypassword

# Backend
PHP_PORT=8081

# Frontend
VUE_PORT=8080
```

Una vez que tengamos el fichero .env en su sitio podemos iniciar la aplicación mediante docker

```
docker compose up
```

La aplicación consiste de 2 aplicaciones una hecha con PHP puro que sirve como backend proporcionando un sistema de API. Esta API la consume el entorno de frontend para gestionar la información que se genera mediante las diferentes acciones que realiza los usuarios.

Cuando se inicia la aplicación por primera vez se pide que cree un usuario administrador, una vez configurado este usuario, el sistema hace el login en la plataforma. El login se ha gestionadno de mnaera sencilla y hace pocas validaciones. Se ha usado el sistema de JWT token y algunas peticiones que cambian estados de la base de datos require que se envie este token en el header usando el protocolo de Authentication Bearer Token.

Una vez que se inicia el login veremos el dashboard que contiene diferente tabs para gestionar los datos de la base de datos. Es muy importante crear el usuario inicial para que se peude registar más usuarios. De momento todos los usuarios son tratados como administradores aunque no se refleje en la base de datos. El endpoint de login retorna un token que pude ser utilizado para futuras peticiones. Este token tiene duración de 1 hora.

Para las tablas se ha utilizado el plugin de Vue Easy table [vue-easytable](https://github.com/Happy-Coding-Clans/vue-easytable)

En las tablas tenemos los botones de crear, editar y borrar elementos que salgan en ella.

Funcionalides implementadas:

1. Registro del usuario inicial
2. Login
3. Registro de nuevos usuarios
4. CRUD de usaurios
5. CRUD de apps
6. CRUD de posiciones dentro de la empresa
7. CRUD de departamentos
8. Asignación de posición y departamento a un usuario
9. Logout

El codigo del backend como visto anteriomnete consiste de una API. Para realizar la API el index.php se ocupa de leer la petición que permite urls friendly, como por ejemplo [http://localhost:8081/api/user/user/list](http://localhost:8081/api/user/user/list) que retorna todos los usuarios.

La estructura de la llamada es simple, la url se divide en 4:

1. /api indica que queremos acceder a la api
2. / el modulo que queremos acceder, de momento tenemos user o app
3. /\<componente (controlador) > el controlador que gestiona la función que queremos acceder
4. / por ultimo, se indica que función se quiere acceder

El metodo de la petición depende de cada endpoint, en la documentación de cada controlador podemos ver que metodo usar, por norma general las peticiones de solicitar datos se hacen mediante GET y las peticiones que cambian estados y datos se hace mediante el POST. Cabe resaltar que las peticiones de POST requeiren un usuario autenticado y su token de authentificación sino retornan error.

Una vez que el sistema sabe que controlador y función llamar, se hace la petición y el resultado es algo similar a:

```
{
    "status": "success",
    "code": 200,
    "data": []
}
```

Aqui podemos ver que nos retorna un status que generalemente son success o error, un codigo que utiliza el standard del protocolo http y el data. Este ultimo hace referencia al contenidos solicitado.

Ejemplo de llamada de error:

```
{
    "status": "error",
    "code": 422,
    "data": "The field 'username' is required"
}
```

La aplicación backend, esta dividida en modulos y cada modulo contiene la siguiente estructura basica:

1. controller: punto de inicio para gestionar las peticiones, es importante que cada controlador extienda del controlador base que se encuentra en la carpeta core. El nombre del controllador tiene que contener la palabra Controllador al final y el principio es el endpoint.
2. dto: clases para gestionar los datos, son los Data Transder Objects que permite transporar los datos en los diferentes compoentes del modulo.
3. repository: Aqui se encarga de las querys necesarias, en la teoria no debe contener logica de negocio
4. service: Aqui se pone toda la logica de negocio que se necesita para el modulo.

Ultimos puntos a resaltar:

1. La conexión a la base de datos es un singleton
2. Hay algunos tests en la carpeta test que utiliza PHPunit para realziar tests unitarios, pero de momento comparte la base de datos con los datos de la aplicación
3. Buena parte del borrado es borrado logico, el contenido sigue en la base de datos, si borramos un user con username x o email x este username y email seguirá en uso.

Puntos a mejorar:

1. La respuesta puede contener fallos de base de datos, exponiendo sus vunerabilidades
2. Se viola el principio de responsabilidades unicas y el principio de inversión de dependencia
3. Hacer más tests para resolver los problemas anteriores.
4. Utilizar la base de datos de prueba para los tests

Pasamos a la aplicación de frontend, aqui no hay mucho secreto ya que es una aplicación sencilla para gestionar unos cuantos cruds. Para el estilo se ha utilizado SASS. Para guardar información que se quiere persistir en la web se ha utilizado [pinia](https://pinia.vuejs.org/).

El código está separado entre formulario, paginas y componentes. Los formularios se utilizan para crear y editar. Se abre mediante una modal que renderiza el formulario. Si es edición enseña el contenido realizando una llamada al backend para buscar la información.

En la carpeta services es donde se encuentra toda las llamadas que se hacen al backend. Se ha implementado una función propia que gestiona todas las llamadas para no duplicar código. La vantaja es que no se duplica código, lo malo es que es una función complicada, y si tuviera test behat, sería lo primero a cambiar y refactorizar.

Puntos a mejorar:

1. Usar más el fichero frontend/src/styles/\_variables.scss
2. Mantener el token en un sitio seguro
3. Utilizar componentes para elementos que se repiten en la web
4. Al editar un user se pide editar la password por usar el mismo form de registro, nuevo y ediar.
5. Revisar los errores que se envian del backend para enseñar mensajes
6. Utilizar el .env
7. Trabajar sobre el responsive