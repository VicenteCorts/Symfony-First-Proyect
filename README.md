# Proyecto Symfony
## Clase 461
### Iniciando el proyecto
#### Instalación de Symfony
Yo utilizaré el siguiente comando por consola para instalar el proyecto en el que trabajaremos:
~composer create-project symfony/website-skeleton 12proyecto-symfony~
**symfony new my_project_directory --version="6.4.*" --webapp**

#### Creación de VHost
Posteriormente Victor Robles genera un vhost para este proyecto, nosotros ignoraremos este paso en esta ocasión.

#### Comienzo en Github
Vamos a GitHub, creamos un nuevo repositorio, Damos un nombre, clicamos en Privado, .gitignore y licencia "None".
- Create repository
Nos dirigimos a la carpeta del nuevo proyecto por consola y ejecutamos los siguientes comandos recomendados por el propio GitHub:
```html
echo "# Symfony-First-Proyect" >> README.md //Me salto este paso
git init
git add README.md
git commit -m "first commit"
git branch -M main
git remote add origin git@github.com:VicenteCorts/Symfony-First-Proyect.git
git push -u origin main
```