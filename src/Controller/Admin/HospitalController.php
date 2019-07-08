<?php

namespace App\Controller\Admin;

use App\Entity\Hospital;
use App\Form\HospitalType;
use App\Repository\HospitalRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/hospital")
 */
class HospitalController extends AbstractController
{

    /**
     * @Route("/", defaults={"page": "1", "_format"="html"}, methods={"GET"}, name="hospital_index")
     * @Route("/page/{page<[1-9]\d*>}", defaults={"_format"="html"}, methods={"GET"}, name="home_paginated")
     *
     * @param int $page
     * @param HospitalRepository $hospitalRepository
     * @return Response
     */
    public function index(int $page, HospitalRepository $hospitalRepository): Response
    {
        return $this->render('admin/hospital/index.html.twig', [
            'rows' => $hospitalRepository->findWithMessages($page),
        ]);
    }

    /**
     * @Route("/new", name="hospital_new", methods={"GET","POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $hospital = new Hospital();
        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($hospital);
            $entityManager->flush();

            $this->addFlash('success', 'Data saved');

            return $this->redirectToRoute('hospital_index');
        }

        return $this->render('admin/hospital/new.html.twig', [
            'hospital' => $hospital,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="hospital_show", methods={"GET"})
     *
     * @param Hospital $hospital
     * @return Response
     */
    public function show(Hospital $hospital): Response
    {
        return $this->render('admin/hospital/show.html.twig', [
            'hospital' => $hospital,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="hospital_edit", methods={"GET","POST"})
     *
     * @param Request $request
     * @param Hospital $hospital
     * @return Response
     */
    public function edit(Request $request, Hospital $hospital): Response
    {
        $form = $this->createForm(HospitalType::class, $hospital);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'Data saved');

            return $this->redirectToRoute('hospital_index', [
                'id' => $hospital->getId(),
            ]);
        }

        return $this->render('admin/hospital/edit.html.twig', [
            'hospital' => $hospital,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="hospital_delete", methods={"DELETE"})
     *
     * @param Request $request
     * @param Hospital $hospital
     * @return Response
     */
    public function delete(Request $request, Hospital $hospital): Response
    {
        if ($this->isCsrfTokenValid('delete'.$hospital->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($hospital);
            $entityManager->flush();

            $this->addFlash('success', 'Data removed');
        }

        return $this->redirectToRoute('hospital_index');
    }
}
