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
use App\Entity\User;

class CityController extends AbstractController
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        return $this->render('pages/admin/city/create.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/city/store", name="admin_city_store")
     */
    public function admin_store(Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $name = $request->request->get("name");
        $country = $request->request->get("country");
        $input = [
            'name' => $name,
            'country' => $country,
        ];
        $constraints = new Assert\Collection([
            'name' => [new Assert\NotBlank],
            'country' => [new Assert\NotBlank],
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

        $image_file = $request->files->get('image');
        if ($image_file) {
            $originalFilename = pathinfo($image_file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $this->generateRandomString();
            $newFilename = $safeFilename.'.'.$image_file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $image_file->move(
                    'upload/cities/',
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $image = 'upload/cities/' . $newFilename;
        }
        else {
            $errorMessages = ['image' => 'this field is require'];
            return $this->render('pages/admin/city/create.html.twig', [
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $city = new City();
        $name = $request->request->get('name');
        $city->setName($name);
        $country = $request->request->get('country');
        $city->setCountry($country);
        $city->setImage($image);
        
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
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

        $image_file = $request->files->get('image');
        if ($image_file) {
            $originalFilename = pathinfo($image_file->getClientOriginalName(), PATHINFO_FILENAME);
            // this is needed to safely include the file name as part of the URL
            $safeFilename = $this->generateRandomString();
            $newFilename = $safeFilename.'.'.$image_file->guessExtension();

            // Move the file to the directory where brochures are stored
            try {
                $image_file->move(
                    'upload/cities/',
                    $newFilename
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            // updates the 'brochureFilename' property to store the PDF file name
            // instead of its contents
            $city->setImage('upload/cities/' . $newFilename);
        }
        
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
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $doct = $this->getDoctrine()->getManager();
        $city = $doct->getRepository(City::class)->find($id);
        $doct->remove($city);
        $doct->flush();
        return $this->redirectToRoute('admin_city', [
            'id' => $city->getId()
        ]);
    }

    private function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
