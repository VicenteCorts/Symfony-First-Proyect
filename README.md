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

## Clase 471
### Cargar Estilos





















