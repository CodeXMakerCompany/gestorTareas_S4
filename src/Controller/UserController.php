<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\User;
use App\form\RegisterType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
   
    public function register(Request $request, UserPasswordEncoderInterface $encoder)
    {	
    	//Crear formulario
    	$user = new User();
    	$form = $this->createForm(RegisterType::class, $user);

    	//Vincular formulario con el objeto
    	$form->handleRequest($request);

    	//Condicionar si el formulario ha sido enviado y validarlo de paso
    	if ($form->isSubmitted() && $form->isValid()) {
    		//Modificando parametros del objeto
    		$user->setRole('user');
    		//pasar la hora
    		$user->setCreatedAt(new \Datetime('now'));
    		//Cifrar la contraseña
    		$encoded = $encoder->encodePassword($user, $user->getPassword());
    		$user->setPassword($encoded);

    		//Guardar usuario
    		$em = $this->getDoctrine()->getManager();
    		$em->persist($user);
    		$em->flush();

    		return $this->redirectToRoute('tasks');
    	}

        return $this->render('user/register.html.twig', [
        	'form' => $form->createView()
        ]);
    }
}
