<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Form\TaskType;
use \Symfony\Component\Security\Core\User\UserInterface;

class TaskController extends AbstractController {

    #[Route('/task', name: 'app_task')]
    public function index(EntityManagerInterface $entityManager): Response {

//Primera Prueba de Entidades y relaciones       
//       $task_repo = $entityManager->getRepository(Task::class);
//            $tasks = $task_repo->findAll();
//        
//            foreach($tasks as $task){
//                echo $task->getUser()->getName().": ".$task->getTitle()."<br/>";
//            }
        
//Segunda Prueba de Entidades y relaciones       
//        $user_repo = $entityManager->getRepository(User::class);
//        $users = $user_repo->findAll();
//
//        foreach ($users as $user) {
//            echo "<h1>{$user->getName()} {$user->getSurname()}</h1>";
//            foreach ($user->getTasks() as $task) {
//                echo $task->getTitle() . "<br/>";
//            }
//        }

        $task_repo = $entityManager->getRepository(Task::class);
        $tasks = $task_repo->findBy([],['id' => 'DESC']);
        return $this->render('task/index.html.twig', [
                    'tasks' => $tasks,
        ]);   
    }
    
    public function detail(Task $task) {
        if(!$task){
            return $this->redirectToRoute('tasks');
        }
        
        return $this->render('task/detail.html.twig',[
            'task' => $task
        ]);
    }
    
    public function creation(EntityManagerInterface $entityManager, Request $request, UserInterface $user): Response  {
        
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
    
    public function myTasks(UserInterface $user) {
        $tasks = $user->getTasks();
        return $this->render('task/my_tasks.html.twig', [
           'tasks' => $tasks 
        ]);
    }
}
