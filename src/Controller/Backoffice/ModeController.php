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
    public function list(Request $request, ModeRepository $modeRepository): Response
    {

        $search = $request->query->get('search', '');
        $sort = $request->query->get('sort', 'id');
        $order = $request->query->get('order', 'asc');

        $modes = $modeRepository->findBySearch($search, $sort, $order);

        return $this->render('backoffice/mode/index.html.twig', [
            'modes' => $modes,
            'sort' => $sort,
            'order' => $order,
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
    public function edit(Request $request, EntityManagerInterface $entityManager, Mode $mode): Response
    {
        

        $form= $this->createForm(ModeType::class, $mode);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            // Encode modified datas before saving them into json_stats.
            $jsonstats = ($form->get('jsonstats')->getData());
            $mode->setJsonStats(json_decode($jsonstats, true));
            $entityManager->persist($mode);
            $entityManager->flush();

            return $this->redirectToRoute('app_backoffice_mode_list');
        }

        return $this->renderForm('backoffice/mode/edit.html.twig', [
            'mode' => $mode,
            'form'=> $form,
        ]);
    }

    /**
     * @Route("/new", name="app_backoffice_mode_new", methods={"GET","POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mode = new Mode();

        $form= $this->createForm(ModeType::class, $mode);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {


            // Encode modified datas before saving them into json_stats.
            $jsonstats = $form->get('jsonstats')->getData();
            $mode->setJsonStats(json_decode($jsonstats, true));
            $entityManager->persist($mode);
            $entityManager->flush();

            return $this->redirectToRoute('app_backoffice_mode_list');
        }

        return $this->renderForm('backoffice/mode/new.html.twig', [
            'form'=> $form,
        ]);
    }
}
