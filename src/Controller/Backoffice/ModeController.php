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
use Knp\Component\Pager\PaginatorInterface;


/**
 * @Route("/backoffice/modes")
 * 
 */
class ModeController extends AbstractController
{
    /**
     * Endpoint for all modes
     * 
     * @Route("", name="app_backoffice_mode_getModes")
     */
    public function getModes(Request $request, ModeRepository $modeRepository, PaginatorInterface $paginator): Response
    {
        // Variables to determine the display order of the modes
        $search = $request->query->get('search', '');
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'asc');

        // Use the method findBySearchMode of the Mode repository to search the games according to the variables
        $modes = $modeRepository->findBySearchMode($search, $sort, $order);

        $pagination = $paginator->paginate(
            $modes, // refers to repository
            $request->query->getInt('page', 1), // Current page number
            15 // Number of items per page
        );

        return $this->render('backoffice/mode/index.html.twig', [
            'pagination' => $pagination, // Pass the pagination object to the template
            'modes' => $pagination->getItems(), // Pass the items of the current page to the template
            'sort' => $sort,
            'order' => $order,
        ]);
    }

    /**
     * Endpoint for a specific mode
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
    * Endpoint for adding a mode
    * 
    * @Route("/new", name="app_backoffice_mode_postModes", methods={"GET","POST"})
    */
    public function postModes(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Instance of the Mode entity
        $mode = new Mode();

        // Instance of the ModeType class using as starting data the instance of the Mode $mode
        $form= $this->createForm(ModeType::class, $mode);
        // Processing the form data
        $form->handleRequest($request);

        // Ff the form has been completed and is valid
        if($form->isSubmitted() && $form->isValid()) {

            // Encode modified data before saving it into json_stats.
            $jsonstats = ($form->get('jsonstats')->getData());
            $mode->setJsonstats(json_decode($jsonstats, true));

            // Register mode information in the database
            $entityManager->persist($mode);
            $entityManager->flush();

            $this->addFlash("success", "Le mode a bien été créé.");

            return $this->redirectToRoute('app_backoffice_mode_getModes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/mode/new.html.twig', [
            'form'=> $form,
        ]);
    }

    /**
    * Endpoint for editing a mode
    * 
    * @Route("/{id}/edit", name="app_backoffice_mode_editModes", methods={"GET","POST"})
    */
    public function editModes(Request $request, EntityManagerInterface $entityManager, Mode $mode): Response
    {
        
        // Instance of the ModeType class using as starting data the instance of the Mode $mode 
        $form= $this->createForm(ModeType::class, $mode);
        // Processing the form data
        $form->handleRequest($request);
        // If the form has been completed and is valid
        if($form->isSubmitted() && $form->isValid()) {

            // Encode modified data before saving it  into json_stats.
            $jsonstats = $form->get('jsonstats')->getData();
            $mode->setJsonstats(json_decode($jsonstats, true));
          
            // Register mode information in the database
            $entityManager->persist($mode);
            $entityManager->flush();

            $this->addFlash("warning", "Le mode a bien été modifié.");

            return $this->redirectToRoute('app_backoffice_mode_getModes', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backoffice/mode/edit.html.twig', [
            'mode' => $mode,
            'form'=> $form,
        ]);
    }

    /**
     * Endpoint for deleting a mode
     * 
     * @Route("/{id}", name="app_backoffice_mode_deleteModes", methods={"POST"}, requirements={"id"="\d+"})
     */
    public function deleteModes(Request $request, Mode $mode, ModeRepository $modeRepository): Response
    {
        // Implementation of the CSRF token validation (symfony bundle)
        if ($this->isCsrfTokenValid('delete'.$mode->getId(), $request->request->get('_token'))) {
            $modeRepository->remove($mode, true);
            $this->addFlash("danger", "Le mode a bien été supprimé.");
        }

        return $this->redirectToRoute('app_backoffice_mode_getModes', [], Response::HTTP_SEE_OTHER);
    }
}
