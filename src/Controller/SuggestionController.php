<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\Suggestion;

class SuggestionController extends AbstractController
{
    protected $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    /**
     * @Route("/admin/suggestion", name="suggestion")
     */
    public function index(): Response
    {
        if(is_null($this->session->get('user'))){
            return $this->redirectToRoute('connexion');
        }
        $suggestions = $this->getDoctrine()->getRepository(Suggestion::class)->findAll();
        return $this->render('pages/admin/suggestion/index.html.twig', [
            'suggestions' => $suggestions
        ]);
    }

    /**
     * @Route("/admin/suggestion/create", name="suggestion_create")
     */
    public function create(): Response
    {
        if(is_null($this->session->get('user'))){
            return $this->redirectToRoute('connexion');
        }
        return $this->render('pages/admin/suggestion/create.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/suggestion/store", name="suggestion_store")
     */
    public function store(Request $request, ValidatorInterface $validator): Response
    {
        if(is_null($this->session->get('user'))){
            return $this->redirectToRoute('connexion');
        }
        $name = $request->request->get("name");
        $input = [
            'name' => $name
        ];
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank],
        ]);
        $violations = $validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
            }
            return $this->render('pages/admin/suggestion/create.html.twig', [
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }

        $suggestion = new Suggestion();
        $name = $request->request->get('name');
        $suggestion->setName($name);

        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($suggestion);
        $doct->flush();
        return $this->redirectToRoute('suggestion');
    }

    /**
     * @Route("/admin/suggestion/edit/{id}", name="suggestion_edit")
     */
    public function edit($id): Response
    {
        if(is_null($this->session->get('user'))){
            return $this->redirectToRoute('connexion');
        }
        $suggestion = $this->getDoctrine()->getRepository(Suggestion::class)->find($id);
        return $this->render('pages/admin/suggestion/edit.html.twig', [
            'suggestion' => $suggestion
        ]);
    }

    /**
     * @Route("/admin/suggestion/update/{id}", name="suggestion_update")
     */
    public function update($id, Request $request, ValidatorInterface $validator): Response
    {
        if(is_null($this->session->get('user'))){
            return $this->redirectToRoute('connexion');
        }
        $name = $request->request->get("name");
        $input = [
            'name' => $name
        ];
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank],
        ]);
        $violations = $validator->validate($input, $constraints);
        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();
            $errorMessages = [];
            foreach ($violations as $violation) {
                $accessor->setValue($errorMessages,
                $violation->getPropertyPath(),
                $violation->getMessage());
            }
            return $this->render('pages/admin/suggestion/edit.html.twig', [
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }

        $doct = $this->getDoctrine()->getManager();
        $suggestion = $doct->getRepository(Suggestion::class)->find($id);
        $name = $request->request->get('name');
        $suggestion->setName($name);
        
        // update
        $doct->flush();
        return $this->redirectToRoute('suggestion', [
            'id' => $suggestion->getId()
        ]);
    }

    /**
     * @Route("/admin/suggestion/delete/{id}", name="suggestion_delete")
     */
    public function delete($id): Response
    {
        if(is_null($this->session->get('user'))){
            return $this->redirectToRoute('connexion');
        }
        $doct = $this->getDoctrine()->getManager();
        $suggestion = $doct->getRepository(Suggestion::class)->find($id);
        $doct->remove($suggestion);
        $doct->flush();
        return $this->redirectToRoute('suggestion', [
            'id' => $suggestion->getId()
        ]);
    }
}