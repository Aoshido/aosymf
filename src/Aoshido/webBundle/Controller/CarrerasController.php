<?php

namespace Aoshido\webBundle\Controller;

use Aoshido\webBundle\Entity\Carrera;
use Aoshido\webBundle\Form\CarreraType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CarrerasController extends Controller {

    public function newAction(Request $request) {
        //Display a list of all Carreras
        $carreras = $this->getDoctrine()
                ->getRepository('AoshidowebBundle:Carrera')
                ->findBy(array('activo' => TRUE));

        $materiascarrera = array();
        foreach ($carreras as $carrera) {
            $materiascarrera[$carrera->getId()] = count($carrera->getMaterias());
        }

        $paginator = $this->get('knp_paginator');
        $pagination = $paginator->paginate($carreras, $this->getRequest()->query->get('page', 1), 10);
        $pagination->setPageRange(6);

        $cantidad = count($carreras);

        $carrera = new Carrera();
        $form = $this->createForm(new CarreraType(), $carrera);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $carrera->setActivo(TRUE);

            foreach ($carrera->getMaterias() as $materia) {
                $materia->setActivo(TRUE);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($carrera);
            $em->flush();

            return $this->redirect($this->generateUrl('abms_carreras'));
        }

        return $this->render('AoshidowebBundle:Carreras:new.html.twig', array(
                    'form' => $form->createView(),
                    'materiasxcarrera' => $materiascarrera,
                    'paginas' => $pagination,
                    'cantidad' => $cantidad,
        ));
    }

}