<?php
namespace App\Controller;
      
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use App\Entity\ActiveType;
use App\Entity\User;

class ActiveTypeController extends AbstractController
{
    protected $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }


    private function isAdmin() {
        if(is_null($this->session->get('user'))||$this->session->get('user')->getType()!="admin"){
            return false;
        }
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->session->get('user')->getId());
        if ($user->getBan()) {
            $this->session->clear();
            return false;
        }
        return true;
    }
    /**
     * @Route("/admin/activetype", name="admin_activetype")
     */
    public function admin_index(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $activetypes = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
        return $this->render('pages/admin/activetype/index.html.twig', [
            'activetypes' => $activetypes
        ]);
    }

    /**
     * @Route("/admin/activetype/create", name="admin_activetype_create")
     */
    public function admin_create(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        return $this->render('pages/admin/activetype/create.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/activetype/store", name="admin_activetype_store")
     */
    public function admin_store(Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $name = $request->request->get("name");
        $input = [
            'name' => $name,
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
            return $this->render('pages/admin/activetype/create.html.twig', [
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $activetype = new ActiveType();
        $name = $request->request->get('name');
        $activetype->setName($name);
        
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($activetype);
        $doct->flush();
        return $this->redirectToRoute('admin_activetype');
    }

    /**
     * @Route("/admin/activetype/edit/{id}", name="admin_activetype_edit")
     */
    public function admin_edit($id): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $activetype = $this->getDoctrine()->getRepository(ActiveType::class)->find($id);
        return $this->render('pages/admin/activetype/edit.html.twig', [
            'activetype' => $activetype,
        ]);
    }

    /**
     * @Route("/admin/activetype/update/{id}", name="admin_activetype_update")
     */
    public function admin_update($id, Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $name = $request->request->get("name");
        $input = [
            'name' => $name,
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
            $activetype = $this->getDoctrine()->getRepository(ActiveType::class)->find($id);
            return $this->render('pages/admin/activetype/edit.html.twig', [
                'activetype' => $activetype,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $doct = $this->getDoctrine()->getManager();
        $activetype = $doct->getRepository(ActiveType::class)->find($id);
        $name = $request->request->get('name');
        $activetype->setName($name);
        
        // update
        $doct->flush();
        return $this->redirectToRoute('admin_activetype', [
            'id' => $activetype->getId()
        ]);
    }

    /**
     * @Route("/admin/activetype/delete/{id}", name="admin_activetype_delete")
     */
    public function admin_delete($id): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $doct = $this->getDoctrine()->getManager();
        $activetype = $doct->getRepository(ActiveType::class)->find($id);
        $doct->remove($activetype);
        $doct->flush();
        return $this->redirectToRoute('admin_activetype', [
            'id' => $activetype->getId()
        ]);
    }
}
