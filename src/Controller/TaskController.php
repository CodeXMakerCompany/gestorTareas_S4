<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
//Importar repositorio(Entidad)
use App\Entity\Task;
use App\Entity\User;
//Cargar formulario
use App\form\TaskType;
//Cargar data del usuario logeado
use Symfony\Component\Security\Core\User\UserInterface;

class TaskController extends AbstractController
{
    
    public function index()
    {	

    	//test entidades y relaciones
    	
    	$em = $this->getDoctrine()->getManager();

    	
    	$task_repo = $this->getDoctrine()->getRepository(Task::class);
    	$tasks = $task_repo->findBy([], ['id' => 'DESC']);

    	

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks
        ]);
    }

    public function detail(Task $task) {
        if (!$task) {
            return $this->redirectToRoute('tasks');
        }else{
            return $this->render('task/detail.html.twig', [
                'task' => $task
            ]);
        }
    }

    public function creation(Request $request, UserInterface $user){

        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setCreatedAt(new \Datetime('now'));
            $task->setUser($user);
            $task->setStatus('Inicializada');

            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirect($this->generateUrl('task_detail', ['id' =>$task->getId()]));
        }

        return $this->render('task/creation.html.twig',[
            'form' => $form->createView()
        ]);
    }

    public function myTasks(UserInterface $user) {
        $tasks = $user->getTasks();

        return $this->render('task/my-tasks.html.twig',[
            'tasks' => $tasks
        ]);
    }

    public function edit(Request $request, UserInterface $user, Task $task){

        //Comporbar ser el usuario que creo la tarea
        if (!$user || $user->getId() != $task->getUser()->getId()) {
           return $this->redirectToRoute('tasks');
        }

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$task->setCreatedAt(new \Datetime('now'));
            //$task->setUser($user);
            //$task->setStatus('Inicializada');

            $em = $this->getDoctrine()->getManager();
            $em->persist($task);
            $em->flush();

            return $this->redirect($this->generateUrl('task_detail', ['id' =>$task->getId()]));
        }

        return $this->render('task/creation.html.twig',[
            'form' => $form->createView(),
            'edit' => true
        ]);
    }

    public function delete(UserInterface $user, Task $task) {
        //Comporbar ser el usuario que creo la tarea
        if (!$user || $user->getId() != $task->getUser()->getId()) {
           return $this->redirectToRoute('tasks');
        }

        if (!$task) {
            return $this->redirectToRoute('tasks');
        }

        $em = $this->getDoctrine()->getManager();
        //adios de doctryne
        $em->remove($task);
        //adios de la bd
        $em->flush();

        return $this->redirectToRoute('tasks');
    }
}
