# Proyecto Symfony

## Clase 461
### Iniciando el proyecto

#### Instalación de Symfony
Yo utilizaré el siguiente comando por consola para instalar el proyecto en el que trabajaremos:
- ~composer create-project symfony/website-skeleton 12proyecto-symfony~
- **symfony new my_project_directory --version="6.4.*" --webapp**

#### Creación de VHost
Posteriormente Victor Robles genera un vhost para este proyecto, nosotros ignoraremos este paso en esta ocasión.

#### Comienzo en Github
Vamos a GitHub, creamos un nuevo repositorio, Damos un nombre, clicamos en Privado, .gitignore y licencia "None".
- Create repository <br/>
Nos dirigimos a la carpeta del nuevo proyecto por consola y ejecutamos los siguientes comandos recomendados por el propio GitHub:
```html
echo "# Symfony-First-Proyect" >> README.md //Me salto este paso
git init
~git add README.md~
git commit -m "first commit"
git branch -M main
git remote add origin git@github.com:VicenteCorts/Symfony-First-Proyect.git
git push -u origin main
```
Por último añadimos opcionalmente un README.md y creamos un nuevo commit.

## EXTRA
### Apache
Para hacer que la aplicación web funcione correctamente en servidores virtuales apache debemos instalar el paquete apache, para ello empleamos el siguiente comando por consola:
```html
composer require symfony/apache-pack
```

## Clase 462
### Diseñar la BBDD
Será un proyecto de Gestión de Tareas. En la videoclase Victor hace el mapa visual de la estructura que tendrá la BBDD en el programa **"DIA"**.

## Clase 463
### Crear la BBDD
Creamos un nuevo fichero de tipo sql (database.sql) en la raíz del proyecto, aquí añadiremos las líneas de código SQL para crear nuestra BBDD.
- **Realmente, en Symfony 6.4 no podemos extraer las tablas de la BBDD al proyecto, pero si al reves. Por lo que sería más conveniente crear la BBDD en phpmyadmin, pero elaborar las tablas en el proyecto Symfony para posetriormente migrarlas a la BBDD**
```html
CREATE DATABASE IF NOT EXISTS symfony_master;
USE symfony_master;

CREATE TABLE IF NOT EXISTS users(
id          int(255) auto_increment not null,
role        varchar(50),
name        varchar(100),
surname     varchar(200),
email       varchar(255),
password    varchar(255),
created_at  datetime,
CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE IF NOT EXISTS tasks(
id          int(255) auto_increment not null,
user_id     int(255) not null,
title       varchar(255),
content     text,
priority    varchar(20),
hours       int(100),
created_at  datetime,
CONSTRAINT pk_tasks PRIMARY KEY(id),
CONSTRAINT fk_task_user FOREIGN KEY(user_id) REFERENCES users(id)
)ENGINE=InnoDb;
```

## Clase 464
### Conectar proyecto a la BBDD
Abrimos el fichero .env y modificamos para crear la conexión con la BBDD:
```html
DATABASE_URL="mysql://root@127.0.0.1:3306/symfony_master?serverVersion=8.2.0&charset=utf8mb4"
```

## Clase 465
### Generar Entidades
Una vez realizada la conexión con la BBDD, crearemos las entidades basándonos en las tablas previamente creadas en la BBDD (**REPITO**, ES MÁS CONVENIENTE NO CREAR LAS TABLAS, SINO COMENZAR POR LA CREACIÓN DE ENTIDADES; YA QUE ESTAS LUEGO GENERARÁN TABLAS EN LA BBDD)
- Nos dirigimos a la consola de comandos
- ~php bin/console doctrine:mapping:import App\\Entity annotation --path=src/Entity~
- Creamos las Entidades con: **php bin/console make:entity nombre**
- Una vez creadas las entidades debemos establcer las relaciones entre ellas; en este caso en la tabla "Task" hay una foreign key (user_id) que hace referencia a la tabla "User"
- Empleamos el mismo comando para entrar de nuevo a la tabla que nos interese que tenga la FK: **php bin/console make:entity task**
- Creamos el nuevo atributo, con tipado **relation** 
- A partir de aquí debemos tener claro que tipo de relación guarda y completar la información solicitada por la consola y el ORM
- https://symfony.com/doc/6.4/doctrine/associations.html#mapping-the-manytoone-relationship
- Con esto se modificarán las Entidades previamente creadas y la relación estará establecida. **Revisar bien**.

## Clase 466
### Relaciones ORM
Victor Robles añade una serie de importaciones a la Entidad User ya que no realizó la relación por consola, sino que al ser una versión más antigua de Symfony permitía importar las tablas de la BBDD al proyecto. Nosotros al hacerlo en la versión 6.4 y crear las relaciones por consola ya tenemos por defecto estas dos librerías de colecciones que añade en la videoclase:
```html
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
```
Igualmente crea de manera manual un nuevo constructor (que nosotros hemos creado de manera auomática):
```html
    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'user')]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }
```
Posteriormente añade más código que parece deprecado en la clase User y Task; por el momento lo ignoramos ya que al hacerlo por consola, nuestro proceso está automatizado.
- También crea un método para extraer todas las tareas de un usuario (también automatizado por nosotros).

## Clase 467
### Rellenar la BBDD
Primero haremos una migración: 
- **php bin/console make:migration** 
- y posteriormente la ejecutamos **php bin/console doctrine:migrations:migrate**
- TODO OK.
Creamos una serie de INSERTS en el fichero database.sql previamente creado por nosotros y los añadimos a la BBDD mediante phpmyadmin.

## Clase 468
### Probando las Entidades Relacionales
Comenzaremos creando un nuevo Controlador por consola para la Entidad Task y User
```html
php bin/console make:controller UserController
php bin/console make:controller TaskController
```
Ahora que vamos a dirigirnos a diferentes rutas de la aplicación web debemos cerciorarnos de que **el apache pack esta instalado**
```html
composer require symfony/apache-pack
```
### Rutas
Opcionalmente podemos crear una ruta en routes.yaml, aunque las creadas por anotaciones en el controlador por defecto también funcionan correctamente:
```html
tasks:
    path: /tasks
    controller: App\Controller\TaskController::index
```
### Primera prueba: Mostrar Task(s)
Nos dirigiremos al TaskController para modificarlo.
- Primero importaremos una serie de use(s):
```html
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Task;
```
- Luego añadiremos el Entity manager como parámetro del método index y crearemos un bloque de código para mostrar las tareas de la BBDD:
```html
    public function index(EntityManagerInterface $entityManager): Response
    {
        //Prueba de Entidades y relaciones
        $task_repo = $entityManager->getRepository(Task::class);
        $tasks = $task_repo->findAll();
        
        foreach($tasks as $task){
            echo $task->getTitle()."<br/>";
        }
        
        return $this->render('task/index.html.twig', [
            'controller_name' => 'TaskController',
        ]);
    }
```
- Ahora mostraremos el nombre dle usuario que ha creado la tarea al lado de cada tarea; para ver si están correctamente relacionadas ambas tablas:
```html
        foreach($tasks as $task){
            echo $task->getUser()->getName().": ".$task->getTitle()."<br/>";
        }
```
### Segunda prueba: Mostrar Todos los usuarios de la BBDD y luego sacar las tareas asociadas a cada usuario:
```html
use App\Entity\User;
(...)

    public function index(EntityManagerInterface $entityManager): Response
    {
 	//Segunda Prueba de Entidades y relaciones
        $user_repo = $entityManager->getRepository(User::class);
        $users = $user_repo->findAll();
        
        foreach ($users as $user) {
            echo "<h1>{$user->getName()} {$user->getSurname()}</h1>";

            foreach ($user->getTasks() as $task) {
                echo $task->getTitle() . "<br/>";
            }

        }

        return $this->render('task/index.html.twig', [
                    'controller_name' => 'TaskController',
        ]);
    }
```

## Clase 469
### Registro de Usuarios
Cambiamos el método index-> register del UserController y la plantilla que se generó de manera automática index.html.twig->register.hmtl.twig para crear un formulario de Registro.
- Nos creamos una ruta para esta nueva acción "register":
```html
registro:
    path: /register
    controller: App\Controller\UserController::register
```
Creamos una carpeta "Form" dentro de src y dentro de esta un archivo RegisterType.php en el que crearemos el formulario:
```html
<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegisterType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder-> add('name', TextType::class, array(
            'label' => 'Nombre'
        ))
                -> add('surname', TextType::class, array(
            'label' => 'Apellidos'
        ))
                -> add('email', EmailType::class, array(
            'label' => 'Correo electrónico'
        ))
                -> add('password', PasswordType::class, array(
            'label' => 'Contraseña'
        ))
                -> add('submit', SubmitType::class, array(
            'label' => 'Registrarse'
        ));
    }
}
```
Ahora debemos hacer uso de este formulario en la acción del controlador:
```html
//Cargamos estos dos elementos
use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\Request;

//dentro del método register
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class , $user);
        return $this->render('user/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
```
Y por último imprimimos el fomrulario en la vista de registro:
```html
{{ form_start(form) }}
{{ form_widget(form)}}
{{ form_end(form)}}
```

## Clase 470
### Guardar Usuario Registrado
Dentro del método register añadiremos un nuevo bloque de código para hacer que lo que envía la request quede plasmado en el objeto que vincula al formulario:
```html
//IMPORTAR:
use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Provider\DateTime;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
//(...)

    public function register(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Request $request): Response 
    {
        //CREANDO FORMULARIO
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        //RELLENAR EL OBJETO CON LOS DATOS DEL FORM
        $form->handleRequest($request);
        
        //COMPROBAR SI EL FORM SE HA EJECUTADO
        if ($form->isSubmitted()) {
            //MODIFICANDO EL OBJETO
            
                //Dando valor a $user->role
                $user->setRole('USER');

                //Dando valor a $user->created_at
                $user->setCreatedAt(new \DateTime('now'));

                //Cifrando la Contraseña: https://www.udemy.com/course/master-en-php-sql-poo-mvc-laravel-symfony-4-wordpress/learn/lecture/12140094#questions/16641310 || https://symfony.com/doc/6.4/security/passwords.html#hashing-the-password
                $passwordHasher = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($passwordHasher);
                
		//Guardar Usuario
                $entityManager->persist($user); //Invocar doctrine para que guarde el objeto
                $entityManager->flush(); //Ejecutar orden para que doctrine guarde el objeto

                return $this->redirectToRoute('tasks');
        }
           
        return $this->render('user/register.html.twig', [
                    'form' => $form->createView()
        ]);       
    }
```
Sin embargo hay que rellenar una serie de atributos del usuario que no vienen establecidos en la request del formulario: Role, Cifrado de Contraseña y Curtime:
```html
                //Dando valor a $user->role
                $user->setRole('USER');

                //Dando valor a $user->created_at
                $user->setCreatedAt(new \DateTime('now'));

                //Cifrando la Contraseña: https://www.udemy.com/course/master-en-php-sql-poo-mvc-laravel-symfony-4-wordpress/learn/lecture/12140094#questions/16641310 || https://symfony.com/doc/6.4/security/passwords.html#hashing-the-password
                $passwordHasher = $passwordHasher->hashPassword($user, $user->getPassword());
                $user->setPassword($passwordHasher);

```
#### Para el cifrado de la Contraseña
En src/Entity/User.php:
```html
//IMPORTAR:
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

//DECLARACIÓN DE LA CLASE:
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

//Final del código:
    public function getUsername() {
        return $this->email;
    }
    public function getSalt() {
        return null;
    }
    
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';
 
        return array_unique($roles);
    }
    public function eraseCredentials() {
    
    }
 
    public function getUserIdentifier(): string {
        
    }
```

## Clase 471
### Validar Formulario de Registro
Para aplicar la validación del formulario por un lado debemos añadir al if del método register lo siguinete:
```html
if ($form->isSubmitted() && $form->isValid()) {
	//...
}
```
Por otro lado debemos aplicar las restricciones a los diferentes atributos del objeto usuario recogido por el formulario mediante la entidad User.php:
```hmtl
//Añadimos un nuevo import:
use Symfony\Component\Validator\Constraints as Assert;

//Ahora añadimos las validaciones a los atributos:
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $role = null;

    #[ORM\Column(length: 100, nullable: true)]
    #[Assert\NotBlank(message: 'El nombre no puede estar vacío')]
    #[Assert\Regex("/[a-zA-Z ]+/")]
    private ?string $name = null;

    #[ORM\Column(length: 200, nullable: true)]
    #[Assert\NotBlank(message: 'El Apellido no puede estar vacío')]
    #[Assert\Regex("/[a-zA-Z ]+/")]
    private ?string $surname = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'El correo no puede estar vacío')]
    #[Assert\Email(
            message: "El email '{{ value }}' no es válido",
            checkMX: ture
    )]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\NotBlank(message: 'La contraseña no puede estar vacía')]
    private ?string $password = null;
```
**al final hago cambios en la validación por un error SIN RESOLVER en el que no me muestra los mensajes de error en caso de meter mal los datos**

## Clase 472
### Cargar Estilos
Abrimos el archivo base.html.twig; que es la plantilla base de la aplicación web. Dentro de este podemos apreciar un bloque de estilos, donde podremos incluir las hojas de estilo que deseemos:
```html
        {% block stylesheets %}
            <link href="{{ asset('assets/css/style.css') }}" type="text/css" rel="stylesheet" />
        {% endblock %}
```
Nos dirigimos a la carpeta public y creamos: assets/css/style.css con el sigueinte contenido:
- Sin embargo, para que funcione previamente debemos dirigirnos a la carpeta raíz assets/styles/app.css y eliminar el código de ese archivo.
```html
body{
    background-color: #eb87a7;
}

.example-wrapper {
    margin: 1em auto;
    max-width: 800px;
    width: 95%;
    font: 18px/1.5 sans-serif;
}

.example-wrapper code {
    background: #F5F5F5;
    padding: 2px 6px;
}

```

## Clase 473
### Maquetar Formulario
Ahora que ya tenemos el control de los Estilos, vamos a modificar el front-end del formulario creado en los apartados anteriores.
```hmtl
label{
    display:block;
    width: 80%;
    margin-top: 15px;
    margin-bottom: 5px;
}

input[type="text"],
input[type="email"],
input[type="password"]{
    width: 70%;
    padding: 10px;
    font-size: 16px;
}

button,
input[type="submit"]{
    padding: 8px;
    margin-top: 15px;
    background-color: #8ceb87;
    color:white;
    border:1px solid darkgreen;
    cursor: pointer;
    transition: all 300ms;
}

button:hover,
input[type="submit"]:hover{
    background-color:darkgreen;
}
```
### Maquetación Layout base
Modificamos el archivo base.html.twig, añadiendo un bloque "header" con un menú para navegar por la web. También modificamos las rutas en config/routes.yaml para dar sentido y orden al proyecto:
- **base.html.twig**
```html
    <body>
        {% block header %}
            <h1>Tareas Symfony</h1>
            <ul>
                <li><a href="{{ path('tasks') }}">Inicio</a></li>
                <li><a href="">Mis Tareas</a></li>
                <li><a href="">Login</a></li>
                <li><a href="{{ path('register') }}">Registro</a></li>
            </ul>
        {% endblock %}
        {% block body %}{% endblock %}
    </body>
```
- **routes.yaml** -> Simple modificación del path
```html
tasks:
    path: /
    controller: App\Controller\TaskController::index
```

## Clase 474
### Maquetar Cabecera y Menú
(Simplemente se aplican estilos generales a la template base, al menú y a la cabecera)

## Clase 475
### Login de Usuarios
En Symfony el sistema d eLogin viene predetemrinado, muy seguro y facil de implementar a falta de una serie de configuraciones.
- https://www.youtube.com/watch?v=cRCZyU3nUL8&ab_channel=dfbastidas (TAMBIÉN ES DE UTILIDAD)
- En primer lugar, debemos haber importado el UserInterface en nuestra entidad User.php (cosa que se hizo en clases anteriores).
```html
use Symfony\Component\Security\Core\User\UserInterface;
//...
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    //...

    //FUNCIONES PARA AUTENTICACIÓN
    public function getUsername() {
        return $this->email;
    }
    public function getSalt() {
        return null;
    }
    
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has USER
        $roles[] = 'USER';
 
        return array_unique($roles);
    }
    public function eraseCredentials() {
    
    }
 
    public function getUserIdentifier(): string {
        
    }
}
```
- Luego en el fichero config/packages/security.yaml debemos añadir en el apartado de security:firewalls:main:
```html
        main:
            lazy: true
            provider: users_in_memory
            form_login:
                login_path: login
                check_path: login
                provider: proveedor
                
            logout:
                path: /logout
                target: /login
```
- Luego debemos crear un **provider** en este mismo archivo
```html
    providers:
        users_in_memory: { memory: null }
        proveedor:
            entity:
                class: App\Entity\User
                property: email
```
- Luego modificaremos el acces_control en este mismo archivo (más adelante) para crear diferenciación entre acceso de user o de admin
- Ahora vamos al controlador de Usuario y añadimos un nuevo método:
```html
//Autenticación -> método login
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

	//...

    public function login (AuthenticationUtils $autenticationUtils) {
       $error = $autenticationUtils->getLastAuthenticationError();
       $lastUsername = $autenticationUtils->getLastUsername();
       
       return $this->render('user/login.html.twig', array(
           'error' => $error,
           'last_username' => $lastUsername
       ));
    }
```
- Creamos la ruta para dicho método:
```html
login:
    path: /login
    controller: App\Controller\UserController::login

logout:
    path: /logout
```
- A continuación creamos la vista del formulario de login asociada a estas rutas y métodos anteriores: templates/user/login.html.twig
```html
{% extends 'base.html.twig' %}

{% block title %}Login de usuarios{% endblock %}

{% block body %}

<div class="example-wrapper">
    <h2>Login de Usuarios</h2>
    
    {% if error %}
        <div class="alert alert-error">
            {{ error.messagekey|trans(error.messageData, 'security') }} {# Para mostrar los errores del fomrulario de LOGIN #}
        </div>
    {% endif %}
    
    {#    Mostrar datos del usuario registrado en caso de logearse #}
    {% if app.user %}
        {{ dump(app.user) }}
    {% endif %}
        
    <form action="{{ path('login') }}" method="POST">
{#        Los elementos _username y _password son palabras reservadas para login en Symfony#}
        <label for="username">Email</label>
        <input type="email" id="username" name="_username" value="{{ last_username }}"/>
        
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="_password"/>
        
        <div class="clearfix"></div>
        
        <input type="submit" value="Entrar"/>
    </form>
        
</div>
{% endblock %}
```
- Añadimos el link de login a base.html.twig:
```html
<li><a href="{{ path('login') }}">Login</a></li>
```
- **He tenido que hacer cambios en User.php debido a un fallo con el método getRoles**
```html
    public function getRoles(): array
    {
//        $roles = $this->roles;
//        // guarantee every user at least has USER
//        $roles[] = 'USER';
// 
//        return array_unique($roles);
            
    // Convierte el rol único a un array de roles
    $roles = ['ROLE_USER']; // Rol por defecto
    
    if ($this->role) {
        // Convierte el rol a mayúsculas y con el prefijo ROLE_
        $roles[] = 'ROLE_' . strtoupper($this->role);
    }
```
- **He tenido que hacer cambios en User.php debido a un fallo con el método getUserIdentifier()**
```html
    public function getUserIdentifier(): string {
	//Estaba vacío
        return $this->email;
    }
```

## Clase 476
### Logout (Cerrar sesión)
En primer lugar haremos modificaciones en el menú en base.html.twig en fucnción de si estamos logueados o no como usuario (role USER)
```html
    <body>
        {% block header %}
            <div class="header">
                <h1>Tareas Symfony</h1>
                <ul id="menu">
                        {% if app.user %}
                        <li><a href="{{ path('tasks') }}">Inicio</a></li>
                        <li><a href="">Mis Tareas</a></li>
                        <li class="right"><a href="">{{ app.user.name ~' '~ app.user.surname}}</a></li>
                        <li class="right"><a href="{{ path('logout') }}">Cerrar Sesión</a></li>
                        {% else %}
                        <li><a href="{{ path('login') }}">Login</a></li>
                        <li><a href="{{ path('register') }}">Registro</a></li>
                        {% endif %}
                </ul>
            </div>
            <div class="clearfix"></div>
        {% endblock %}
        {% block body %}{% endblock %}
    </body>
```
- Añadirmos un par de línas de css para flotar la mitad del menú a la derecha:
```html
#menu li.right{
    float: right;
    border-right: 0px;
    border-left: 1px solid #ccc;
}
```

## Clase 477
### Listado de Tareas (en la pag Inicio -Tasks-)
Elaboraremos una tabla para listar las Tareas de la aplicación, para ello modifcamos el index.html.twig:
```html
        <table>
            <tr>
                <th>Tarea</th>
                <th>Prioridad</th>
                <th>Horas aprox.</th>
                <th>Acciones</th>
            </tr>
            {% for task in tasks %}
                <tr>
                    <td>{{ task.title }}</td>
                    <td>{{ task.priority }}</td>
                    <td>{{ task.hours }}</td>
                    <td> ACCIONES </td>
                </tr>
            {% endfor %}
        </table>
-----------------------------------------
//Estilos

table{
    width: 100%;
}

table th,
table td{
    background: #f5c6cb;
    padding: 15px;
    padding-right: 25px;
    padding-left: 25px;
    border-bottom: 1px solid #eee;
    text-align: center;
}

table td{
    background: #fce6e2;
}

```
- Tras esto, nos dirigimos a TaskController para crear un método para extraer toda la info de la BBDD:
```html
    public function index(EntityManagerInterface $entityManager): Response {

        $task_repo = $entityManager->getRepository(Task::class);
        $tasks = $task_repo->findBy([],['id' => 'DESC']);

        return $this->render('task/index.html.twig', [
                    'tasks' => $tasks,
        ]);   
    }
```

## Clase 478
### Mejoras para el listado
Vamos a hacer una condicional para convertir los textos en inglés de las prioridades a español, mediante un if dentro del "td" de "task.priority" en index.html.twig:
```html
<td>
	{% if task.priority == 'High' %}
		{{ 'Alta' }}
	{% endif %}
	{% if task.priority == 'Medium' %}
		{{ 'Media' }}
	{% endif %}
	{% if task.priority == 'Low' %}
		{{ 'Baja' }}
	{% endif %}
</td>
```
Ahora añadiremos una serie de botones para la columna de Acciones:
```html
<td class="buttons"> 
	<a href="" class="see">Ver</a>
	<a href="" class="edit">Editar</a>
	<a href="" class="delete">Borrar</a>
</td>
----------------------------------------
//Estilos:

.buttons a{
    text-decoration: none;
    border: 1px solid #444;
    padding: 10px;
    color: white;
    background: #8ceb87;
}

.buttons .see{
    background: #8ceb87;
}

.buttons .edit{
    background: #007bff;
}

.buttons .delete{
    background: #a71d2a;
}
```

## Clase 479
### Botón "Ver" 
Añadimos un nuevo método "detail" al TaskController 
- Le pasamos el parámetro Task $task, para que tenga acceso al objeto completo
```html
    public function detail(Task $task) {
        if(!$task){
            return $this->redirectToRoute('tasks');
        }
        
        return $this->render('task/detail.html.twig',[
            'task' => $task
        ]);
    }
```
Creamos la ruta a la que nos dirige este método:
```html
task_detail:
    path: /tarea/{id}
    controller: App\Controller\TaskController::detail 
```
Y por último creamos la vista correspondiente:
```html
{% extends 'base.html.twig' %}

{% block title %}Todas las Tareas{% endblock %}

{% block body %}

    <div class="example-wrapper">
        <h2>{{ task.title }}</h2>
        <p class="data-task">
            {{ task.user.name }} 
            {{ task.user.name }} 
            || Creado: {{ task.createdAt|date('d/m/Y (h:i)') }} 
            || Prioridad: {% if task.priority == 'High' %}
                            {{ 'Alta' }}
                            {% endif %}
                            {% if task.priority == 'Medium' %}
                                {{ 'Media' }}
                            {% endif %}
                            {% if task.priority == 'Low' %}
                                {{ 'Baja' }}
                            {% endif %}
            || Horas: {{ task.hours }}
        </p>
        <p>{{ task.content }}</p>
    </div>
{% endblock %}
```
Añadimos en index.html.twig el enlace para ver los detalles de la tarea seleccionada
```html
<a href="{{ path('task_detail', {'id':task.id}) }}" class="see">Ver</a>
```

## Clase 480
### Método Crear Tarea
Comenzamos por añadir un enlace en el menú de base.html.twig para crear tareas:
```html
<li><a href="">Crear Tarea</a></li>
```
Creamos el método "creation" dentro de TaskController para redirigir al formulariod e creación de Tarea:
```html
use Symfony\Component\HttpFoundation\Request;

//...

    public function creation(Request $request) {
        return $this->render('task/creation.html.twig');
    }
```
Creamos la ruta correspondiente:
```html
task_creation:
    path: /crear-tarea
    controller: App\Controller\TaskController::creation
```
Añadimos el href correspondiente en base.html.twig:
```html
<li><a href="{{ path('task_creation') }}">Crear Tarea</a></li>
```
Creamos la vista asociada (basica para comprobar que funciona, luego se ampliará)
```html
{% extends 'base.html.twig' %}

{% block title %}Crear Tarea{% endblock %}

{% block body %}

    <div class="example-wrapper">
        <h2>Crear Tarea</h2>
        
    </div>
{% endblock %}
```

## Clase 481
### Formulario para Crear Tarea
- Tomamos el RegisterType de la carpeta src/Form y lo copiamos y renombramos a "TaskType"
- A continuación lo editamos en fucnión de su labor:
```html
<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class TaskType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder-> add('title', TextType::class, array(
            'label' => 'Título'
        ))
                -> add('content', TextAreaType::class, array(
            'label' => 'Contenido'
        ))
                -> add('priority', ChoiceType::class, array(
            'label' => 'Prioridad',
            'choices' => array(
                'Alta' => 'high',
                'Media' => 'medium',
                'Baja' => 'low',
            )
        ))
                -> add('hours', TextType::class, array(
            'label' => 'Horas Presupuestadas'
        ))
                -> add('submit', SubmitType::class, array(
            'label' => 'Crear Tarea'
        ));
    }
```
Ahora lo usaremos dentro del método del controlador, importando previamente el objeto Task y el TaskType recien creado
```html
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Form\TaskType;

//... 

    public function creation(EntityManagerInterface $entityManager, Request $request): Response  {
        
        //CREANDO EL FORMULARIO
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        
        //RELLENAR EL OBJETO CON LOS DATOS DEL FORM
        $form->handleRequest($request);
        
        //COMPROBAR SI EL FORM SE HA EJECUTADO
        if ($form->isSubmitted() && $form->isValid()) {
            
        }
        
        return $this->render('task/creation.html.twig', [
                    'form' => $form->createView()
        ]);
    }
```
Imprimimos el Formulario en creation.html.twig:
```html
{% extends 'base.html.twig' %}

{% block title %}Crear Tarea{% endblock %}

{% block body %}

    <div class="example-wrapper">
        <h2>Crear Tarea</h2>
        {{ form_start(form) }}
        {{ form_widget(form)}}
        {{ form_end(form)}}
    </div>
{% endblock %}
```
Hacemos asjutes en css
```html
input[type="text"],
input[type="email"],
input[type="password"],
textarea,
select{
    width: 70%;
    padding: 10px;
    font-size: 16px;
}
```
En el Formulario todavia tenemos que añadir valores a los atributos createdAt y UserId para ello: añadimos por parámetro UserInterface y modificamos el códgio:
```html
    public function creation(EntityManagerInterface $entityManager, Request $request, \Symfony\Component\Security\Core\User\UserInterface $user): Response  {
        
        //CREANDO EL FORMULARIO
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        
        //RELLENAR EL OBJETO CON LOS DATOS DEL FORM
        $form->handleRequest($request);
        
        //COMPROBAR SI EL FORM SE HA EJECUTADO
        if ($form->isSubmitted() && $form->isValid()) {
            dump($task);
            dump($user);
            
            //MODIFICANDO EL OBJETO
            
            //Dando valor al user
            $task->setUser($user);
            
            //Dando valor a $task->created_at
            $task->setCreatedAt(new \DateTime('now'));

            //Guardar la Tarea
            $entityManager->persist($task); //Invocar doctrine para que guarde el objeto
            $entityManager->flush(); //Ejecutar orden para que doctrine guarde el objeto

            return $this->redirect($this->generateUrl('task_detail', ['id' => $task->getId()]));
        }
        
        return $this->render('task/creation.html.twig', [
                    'form' => $form->createView(),
        ]);
    }
```

## VarDumper
### dump($task);
Si aparece el menú inferior de symfony server puede clicarse para ver los elementos en Post que han surgido de hacer el submit en el formulario
- Sino, se puede acceder escribiendo en la url: **http://localhost:8000/_profiler** y navegando un poco

## Clase 482
### Mejorar Estilos

