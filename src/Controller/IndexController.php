<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Hospital;
use App\Form\ContactType;
use App\Repository\HospitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class IndexController extends AbstractController
{

    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="home")
     * @Route("/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="home_paginated")
     *
     * @param int $page
     * @param HospitalRepository $hospitalRepository
     * @return Response
     */
    public function index(int $page, HospitalRepository $hospitalRepository): Response
    {
        return $this->render('index/index.twig', [
            'paginator' => $hospitalRepository->findAllPaginated($page),
        ]);
    }

    /**
     * @Route("/{id}/send-message", name="send_message", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Hospital $hospital
     * @return Response
     */
    public function sendMessage(Request $request, Hospital $hospital): Response
    {
        $contact = new Contact();
        $contact->setHospital($hospital);

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($contact);
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Message sent');

            return $this->redirectToRoute('home');
        }

        return $this->render('index/send_message.twig', [
            'hospital' => $hospital,
            'form' => $form->createView(),
        ]);
    }
}
