<?php

namespace App\Controller\Backoffice;

use App\Entity\Mode;
use App\Form\ModeType;
use App\Repository\ModeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * @Route("/backoffice/modes")
 * 
 */
class ModeController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("", name="app_backoffice_mode_list")
     */
    public function list(ModeRepository $modeRepository): Response
    {

        $modes = $modeRepository->findAll();

        return $this->render('backoffice/mode/index.html.twig', [
            'modes' => $modes,
        ]);
    }

    /**
     * @Route("/{id}", name="app_backoffice_mode_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Mode $mode): Response
    {

        return $this->render('backoffice/mode/show.html.twig', [
            'mode' => $mode,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_backoffice_mode_edit", methods={"GET","POST"})
     */
    /**public function edit(Request $request, EntityManagerInterface $entityManager, Mode $mode): Response
    {
        $mode = $entityManager->getRepository(Mode::class)->find($id);
        $jsonData = $mode->getJsonStats();
        
        $decodedData = json_decode($jsonData, true);
        

        $form= $this->createForm(ModeType::class, null, [
            'data_class' => null,
            'data' => $decodedData,
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            // Encode modified datas before saving them into json_stats.
            $encodedData = json_encode($form->getData());

            $mode->setJsonStats($encodedData);
            $entityManager->flush();

            return $this->redirectToRoute('app_backoffice_mode_list');
        }
        //Sdump($mode);

        return $this->renderForm('backoffice/mode/edit.html.twig', [
            'mode' => $mode,
            'form'=> $form,
        ]); */

        /**
     * @Route("/{id}/edit", name="app_backoffice_mode_edit", methods={"GET","POST"})
     */
     /**public function edit( $id, Request $request, EntityManagerInterface $entityManager, Mode $mode): Response
    {
        $mode = $entityManager->getRepository(Mode::class)->find($id);
        $jsonData = $mode->getJsonStats();
        
        $decodedData = json_decode($jsonData, true);
        

        

        return $this->render('backoffice/mode/edit.html.twig', [
            'decodedData' => $decodedData,
        ]);
    }  */
}
