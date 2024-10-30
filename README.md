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
### 














