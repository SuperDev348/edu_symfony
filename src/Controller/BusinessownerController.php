<?php

namespace App\Controller;

use App\Entity\Businessowner;
use App\Entity\User;
use App\Form\BusinessownerType;
use App\Repository\BusinessownerRepository;
use PhpParser\Builder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
/**
 * @Route("/businessowner")
 */
class BusinessownerController extends AbstractController
{

    /**
     * @Route("/admin", name="businessowner_index", methods={"GET"})
     */
    public function index(BusinessownerRepository $businessownerRepository,SessionInterface $session): Response
    {if(is_null($session->get('user'))||$session->get('user')->getType()!="admin"){
        return $this->redirectToRoute('deconnexion');
    }
        return $this->render('pages/businessowner/index.html.twig', [
            'businessowners' => $businessownerRepository->findAll(),
        ]);
    }

    /**
     * @Route("/inscription/{id}", name="businessowner_new", methods={"GET","POST"})
     */
    public function new(Request $request,$id): Response
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);
        $existe = $user->getBusinessowner();
        $businessowner = new Businessowner();
        if (is_null($existe)) {

            $form = $this->createForm(BusinessownerType::class, $businessowner);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setType('businessowner');
                $businessowner->setUseraccount($user);
                $businessowner->setEtat(false);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($businessowner);
                $entityManager->flush();

                return $this->render('pages/businessowner/message.html.twig', [
                    'message' => "votre demande est en cours de traitement",
                ]);
            }
            return $this->render('pages/businessowner/new.html.twig', [
                'form' => $form->createView(),
            ]);
        } else if ($existe->getEtat() == false) {
            return $this->render('pages/businessowner/message.html.twig', [
                'message' => "votre demande est en cours de traitement",
            ]);
        } else {
            return $this->render('pages/businessowner/message.html.twig', [
                'message' => "Vous pouvez maintenant creer votre annonce"
            ]);
        }
    }

    /**
     * @Route("/{id}", name="businessowner_show", methods={"GET"})
     */
    public function show(Businessowner $businessowner,SessionInterface $session): Response
    {if(is_null($session->get('user'))||$session->get('user')->getType()!="admin"){
        return $this->redirectToRoute('deconnexion');
    }
        return $this->render('pages/businessowner/show.html.twig', [
            'businessowner' => $businessowner,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="businessowner_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Businessowner $businessowner,SessionInterface $session): Response
    {
        if(is_null($session->get('user'))||$session->get('user')->getType()!="admin"){
            return $this->redirectToRoute('deconnexion');
        }
        $form = $this->createForm(BusinessownerType::class, $businessowner);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('businessowner_index');
        }

        return $this->render('pages/businessowner/edit.html.twig', [
            'businessowner' => $businessowner,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/confirmer", name="businessowner_confirmer", methods={"GET","POST"})
     */
    public function confirmer(Request $request, $id,SessionInterface $session): Response
    {if(is_null($session->get('user'))||$session->get('user')->getType()!="admin"){
        return $this->redirectToRoute('deconnexion');
    }
        $businessowner = $this->getDoctrine()->getRepository(Businessowner::class)->find($id);
       $businessowner->setEtat(true);
       $businessowner->getUseraccount()->setType('businessowner');
       $this->getDoctrine()->getManager()->flush();
    return $this->redirectToRoute("businessowner_index");
    }

    /**
     * @Route("/{id}", name="businessowner_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Businessowner $businessowner): Response
    {
        if ($this->isCsrfTokenValid('delete'.$businessowner->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($businessowner);
            $entityManager->flush();
        }

        return $this->redirectToRoute('businessowner_index');
    }
}
