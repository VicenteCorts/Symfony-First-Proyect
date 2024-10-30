<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\User;
use App\Form\RegisterType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Provider\DateTime;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController {

    #[Route('/user', name: 'app_user')]
    public function register(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Request $request): Response 
    {
        //CREANDO FORMULARIO
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);

        //RELLENAR EL OBJETO CON LOS DATOS DEL FORM
        $form->handleRequest($request);
        
        //COMPROBAR SI EL FORM SE HA EJECUTADO
        if ($form->isSubmitted() && $form->isValid()) {
            //MODIFICANDO EL OBJETO
            
                //Dando valor a $user->role
                $user->setRole('USER');

                //Dando valor a $user->created_at
                $user->setCreatedAt(new \DateTime('now'));

                //Cifrando la Contraseña->config/packages/security.yaml
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
}
