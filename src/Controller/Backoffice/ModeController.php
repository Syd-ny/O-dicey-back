<?php

namespace App\Controller\Backoffice;

use App\Entity\Mode;
use App\Form\ModeType;
use App\Repository\ModeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @Route("/backoffice/modes")
 * 
 */
class ModeController extends AbstractController
{
    /**
     * endpoint for all modes
     * 
     * @Route("", name="app_backoffice_mode_getModes")
     */
    public function getModes(Request $request, ModeRepository $modeRepository): Response
    {
        // Variables to determine the display order of the games
        $search = $request->query->get('search', '');
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'asc');

        // Use the method findBySearchMode of the Mode repository to search the games according to the variables
        $modes = $modeRepository->findBySearchMode($search, $sort, $order);

        return $this->render('backoffice/mode/index.html.twig', [
            'modes' => $modes,
            'sort' => $sort,
            'order' => $order,
        ]);
    }

    /**
     * endpoint for a specific mode
     * 
     * @Route("/{id}", name="app_backoffice_mode_getModesById", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function getModesById(Mode $mode): Response
    {

        return $this->render('backoffice/mode/show.html.twig', [
            'mode' => $mode,
        ]);
    }

    /**
    * endpoint for adding a mode
    * 
    * @Route("/new", name="app_backoffice_mode_postModes", methods={"GET","POST"})
    */
    public function postModes(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Instantiation of the Mode entity
        $mode = new Mode();

        // Instantiation of the ModeType class using as starting data the instance of the Mode $mode class
        $form= $this->createForm(ModeType::class, $mode);
        // Processing of the form entry
        $form->handleRequest($request);

        // if the form has been entered and the validation rules are checked
        if($form->isSubmitted() && $form->isValid()) {

            // Encode modified datas before saving them into json_stats.
            $jsonstats = ($form->get('jsonstats')->getData());
            $mode->setJsonstats(json_decode($jsonstats, true));

            // register game informations in the database
            $entityManager->persist($mode);
            $entityManager->flush();

            return $this->redirectToRoute('app_backoffice_mode_getModes');
        }

        return $this->renderForm('backoffice/mode/new.html.twig', [
            'form'=> $form,
        ]);
    }

    /**
    * endpoint for editing a mode
    * 
    * @Route("/{id}/edit", name="app_backoffice_mode_editModes", methods={"GET","POST"})
    */
    public function editModes(Request $request, EntityManagerInterface $entityManager, Mode $mode): Response
    {
        
        // Instantiation of the ModeType class using as starting data the instance of the Mode $mode class
        $form= $this->createForm(ModeType::class, $mode);
        // Processing of the form entry
        $form->handleRequest($request);
        // if the form has been entered and the validation rules are checked
        if($form->isSubmitted() && $form->isValid()) {

            // Encode modified datas before saving them into json_stats.
            $jsonstats = $form->get('jsonstats')->getData();
            $mode->setJsonstats(json_decode($jsonstats, true));
          
            // register game informations in the database
            $entityManager->persist($mode);
            $entityManager->flush();

            return $this->redirectToRoute('app_backoffice_mode_getModes');
        }

        return $this->renderForm('backoffice/mode/edit.html.twig', [
            'mode' => $mode,
            'form'=> $form,
        ]);
    }
}
