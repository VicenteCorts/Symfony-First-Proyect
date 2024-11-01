<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Task;
use App\Entity\User;

class TaskController extends AbstractController {

    #[Route('/task', name: 'app_task')]
    public function index(EntityManagerInterface $entityManager): Response {

//Primera Prueba de Entidades y relaciones
        
/*        $task_repo = $entityManager->getRepository(Task::class);
            $tasks = $task_repo->findAll();
        
            foreach($tasks as $task){
                echo $task->getUser()->getName().": ".$task->getTitle()."<br/>";
            }
 */
        
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
//
//        return $this->render('task/index.html.twig', [
//                    'controller_name' => 'TaskController',
//        ]);
    }
}
