<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Task;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

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
    
    public function creation(Request $request) {
        return $this->render('task/creation.html.twig', [
            
        ]);
    }
}
