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
use App\Entity\City;

class CityController extends AbstractController
{
    protected $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    /**
     * @Route("/citydetail2", name="citydetail2")
     */
    public function detail2(): Response
    {
        return $this->render('pages/city/detail2.html.twig', [
        ]);
    }

    /**
     * @Route("/citydetail3", name="citydetail3")
     */
    public function detail3(): Response
    {
        return $this->render('pages/city/detail3.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/city", name="admin_city")
     */
    public function admin_index(): Response
    {
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        return $this->render('pages/admin/city/index.html.twig', [
            'cities' => $cities
        ]);
    }

    /**
     * @Route("/admin/city/create", name="admin_city_create")
     */
    public function admin_create(): Response
    {
        return $this->render('pages/admin/city/create.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/city/store", name="admin_city_store")
     */
    public function admin_store(Request $request, ValidatorInterface $validator): Response
    {
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
            return $this->render('pages/admin/city/create.html.twig', [
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $city = new City();
        $name = $request->request->get('name');
        $city->setName($name);
        
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($city);
        $doct->flush();
        return $this->redirectToRoute('admin_city');
    }

    /**
     * @Route("/admin/city/edit/{id}", name="admin_city_edit")
     */
    public function admin_edit($id): Response
    {
        $city = $this->getDoctrine()->getRepository(City::class)->find($id);
        return $this->render('pages/admin/city/edit.html.twig', [
            'city' => $city,
        ]);
    }

    /**
     * @Route("/admin/city/update/{id}", name="admin_city_update")
     */
    public function admin_update($id, Request $request, ValidatorInterface $validator): Response
    {
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
            $city = $this->getDoctrine()->getRepository(City::class)->find($id);
            return $this->render('pages/admin/city/edit.html.twig', [
                'city' => $city,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $doct = $this->getDoctrine()->getManager();
        $city = $doct->getRepository(City::class)->find($id);
        $name = $request->request->get('name');
        $city->setName($name);
        
        // update
        $doct->flush();
        return $this->redirectToRoute('admin_city', [
            'id' => $city->getId()
        ]);
    }

    /**
     * @Route("/admin/city/delete/{id}", name="admin_city_delete")
     */
    public function admin_delete($id): Response
    {
        $doct = $this->getDoctrine()->getManager();
        $city = $doct->getRepository(City::class)->find($id);
        $doct->remove($city);
        $doct->flush();
        return $this->redirectToRoute('admin_city', [
            'id' => $city->getId()
        ]);
    }
}
