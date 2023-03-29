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
use App\Entity\Category;
use App\Entity\City;
use App\Entity\ActiveType;
use App\Entity\CategoryType;
use App\Entity\User;

class CategoryController extends AbstractController
{
    protected $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    private function isAuth() {
        if(is_null($this->session->get('user'))){
            return false;
        }
        $user = $this->getDoctrine()->getRepository(User::class)->find($this->session->get('user')->getId());
        if ($user->getBan()) {
            $this->session->clear();
            return false;
        }
        return true;
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
     * @Route("/admin/category", name="admin_category")
     */
    public function admin_index(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach($categories as $category) {
            $categorytype = $this->getDoctrine()->getRepository(CategoryType::class)->find($category->getTypeId());
            $category->type = $categorytype->getName();
            $activetype = $this->getDoctrine()->getRepository(ActiveType::class)->find($category->getActiveTypeId());
            $category->activetype = $activetype->getName();
            $city = $this->getDoctrine()->getRepository(City::class)->find($category->getCityId());
            $category->city = $city->getName();
        }
        $types = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $active_types = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
        return $this->render('pages/admin/category/index.html.twig', [
            'categories' => $categories,
            'types' => $types,
            'cities' => $cities,
            'active_types' => $active_types,
        ]);
    }

    /**
     * @Route("/admin/category/create", name="admin_category_create")
     */
    public function admin_create(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $categorytypes = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $activetypes = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
        return $this->render('pages/admin/category/create.html.twig', [
            'categories' => $categories,
            'categorytypes' => $categorytypes,
            'cities' => $cities,
            'activetypes' => $activetypes,
        ]);
    }

    /**
     * @Route("/admin/category/store", name="admin_category_store")
     */
    public function admin_store(Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $city_id = $request->request->get("city_id");
        $type_id = $request->request->get("type_id");
        $active_type_id = $request->request->get("active_type_id");
        $input = [
            'city_id' => $city_id,
            'type_id' => $type_id,
            'active_type_id' => $active_type_id,
        ];
        $constraints = new Assert\Collection([
            'city_id' => [new Assert\NotBlank],
            'type_id' => [new Assert\NotBlank],
            'active_type_id' => [new Assert\NotBlank],
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
            $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
            $categorytypes = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
            $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
            $activetypes = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
            return $this->render('pages/admin/category/create.html.twig', [
                'categories' => $categories,
                'categorytypes' => $categorytypes,
                'cities' => $cities,
                'activetypes' => $activetypes,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $category = new Category();
        $city_id = $request->request->get('city_id');
        $category->setCityId($city_id);
        $type_id = $request->request->get('type_id');
        $category->setTypeId($type_id);
        $active_type_id = $request->request->get('active_type_id');
        $category->setActiveTypeId($active_type_id);
        
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($category);
        $doct->flush();
        return $this->redirectToRoute('admin_category');
    }

    /**
     * @Route("/admin/category/edit/{id}", name="admin_category_edit")
     */
    public function admin_edit($id): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $categorytypes = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $activetypes = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
        return $this->render('pages/admin/category/edit.html.twig', [
            'category' => $category,
            'categorytypes' => $categorytypes,
            'cities' => $cities,
            'activetypes' => $activetypes,
        ]);
    }

    /**
     * @Route("/admin/category/update/{id}", name="admin_category_update")
     */
    public function admin_update($id, Request $request, ValidatorInterface $validator): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $city_id = $request->request->get("city_id");
        $type_id = $request->request->get("type_id");
        $active_type_id = $request->request->get("active_type_id");
        $input = [
            'city_id' => $city_id,
            'type_id' => $type_id,
            'active_type_id' => $active_type_id,
        ];
        $constraints = new Assert\Collection([
            'city_id' => [new Assert\NotBlank],
            'type_id' => [new Assert\NotBlank],
            'active_type_id' => [new Assert\NotBlank],
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
            $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
            $categorytypes = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
            $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
            $activetypes = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
            return $this->render('pages/admin/category/edit.html.twig', [
                'categories' => $categories,
                'categorytypes' => $categorytypes,
                'cities' => $cities,
                'activetypes' => $activetypes,
                'errors' => $errorMessages,
                'old' => $input
            ]);
        }
        
        $doct = $this->getDoctrine()->getManager();
        $category = $doct->getRepository(Category::class)->find($id);
        $city_id = $request->request->get('city_id');
        $category->setCityId($city_id);
        $type_id = $request->request->get('type_id');
        $category->setTypeId($type_id);
        $active_type_id = $request->request->get('active_type_id');
        $category->setActiveTypeId($active_type_id);
        
        // update
        $doct->flush();
        return $this->redirectToRoute('admin_category', [
            'id' => $category->getId()
        ]);
    }

    /**
     * @Route("/admin/category/delete/{id}", name="admin_category_delete")
     */
    public function admin_delete($id): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $doct = $this->getDoctrine()->getManager();
        $category = $doct->getRepository(Category::class)->find($id);
        $doct->remove($category);
        $doct->flush();
        return $this->redirectToRoute('admin_category', [
            'id' => $category->getId()
        ]);
    }

    /**
     * @Route("/admin/category/search", name="admin_category_search")
     */
    public function admin_search(Request $request): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $type_id = $request->request->get('type_id');
        $city_id = $request->request->get('city_id');
        $active_type_id = $request->request->get('active_type_id');
        $filter = [];
        if ($type_id != '0')
            $filter['type_id'] = $type_id;
        if ($city_id != '0')
            $filter['city_id'] = $city_id;
        if ($active_type_id != '0')
            $filter['active_type_id'] = $active_type_id;
        
        $doct = $this->getDoctrine()->getManager();
        $categories = $doct->getRepository(Category::class)->findWithFilter($filter);
        foreach($categories as $category) {
            $categorytype = $this->getDoctrine()->getRepository(CategoryType::class)->find($category->getTypeId());
            $category->type = $categorytype->getName();
            $activetype = $this->getDoctrine()->getRepository(ActiveType::class)->find($category->getActiveTypeId());
            $category->activetype = $activetype->getName();
            $city = $this->getDoctrine()->getRepository(City::class)->find($category->getCityId());
            $category->city = $city->getName();
        }
        $types = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();
        $active_types = $this->getDoctrine()->getRepository(ActiveType::class)->findAll();
        return $this->render('pages/admin/category/index.html.twig', [
            'categories' => $categories,
            'filter' => $filter,
            'types' => $types,
            'cities' => $cities,
            'active_types' => $active_types,
        ]);
    }

    /**
     * @Route("/admin/categorytype", name="admin_categorytype")
     */
    public function admin_type_index(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $categorytypes = $this->getDoctrine()->getRepository(CategoryType::class)->findAll();
        return $this->render('pages/admin/categorytype/index.html.twig', [
            'categorytypes' => $categorytypes
        ]);
    }

    /**
     * @Route("/admin/categorytype/create", name="admin_categorytype_create")
     */
    public function admin_type_create(): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        return $this->render('pages/admin/categorytype/create.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/categorytype/store", name="admin_categorytype_store")
     */
    public function admin_type_store(Request $request, ValidatorInterface $validator): Response
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
            return $this->render('pages/admin/categorytype/create.html.twig', [
                'errors' => $errorMessages,
                'old' => $input,
            ]);
        }
        
        $categorytype = new CategoryType();
        $name = $request->request->get('name');
        $categorytype->setName($name);
        $icon = $this->icon($name);
        $categorytype->setIcon($icon);
        
        // save
        $doct = $this->getDoctrine()->getManager();
        $doct->persist($categorytype);
        $doct->flush();
        return $this->redirectToRoute('admin_categorytype');
    }

    /**
     * @Route("/admin/categorytype/edit/{id}", name="admin_categorytype_edit")
     */
    public function admin_type_edit($id): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $categorytype = $this->getDoctrine()->getRepository(CategoryType::class)->find($id);
        return $this->render('pages/admin/categorytype/edit.html.twig', [
            'categorytype' => $categorytype,
        ]);
    }

    /**
     * @Route("/admin/categorytype/update/{id}", name="admin_categorytype_update")
     */
    public function admin_type_update($id, Request $request, ValidatorInterface $validator): Response
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
            $categorytype = $this->getDoctrine()->getRepository(CategoryType::class)->find($id);
            return $this->render('pages/admin/categorytype/edit.html.twig', [
                'categorytype' => $categorytype,
                'errors' => $errorMessages,
                'old' => $input,
            ]);
        }
        
        $doct = $this->getDoctrine()->getManager();
        $categorytype = $doct->getRepository(CategoryType::class)->find($id);
        $name = $request->request->get('name');
        $categorytype->setName($name);
        $icon = $this->icon($name);
        $categorytype->setIcon($icon);
        // update
        $doct->flush();
        return $this->redirectToRoute('admin_categorytype', [
            'id' => $categorytype->getId()
        ]);
    }

    /**
     * @Route("/admin/categorytype/delete/{id}", name="admin_categorytype_delete")
     */
    public function admin_type_delete($id): Response
    {
        if (!$this->isAdmin())
            return $this->redirectToRoute('deconnexion');
        $doct = $this->getDoctrine()->getManager();
        $categorytype = $doct->getRepository(CategoryType::class)->find($id);
        $doct->remove($categorytype);
        $doct->flush();
        return $this->redirectToRoute('admin_categorytype', [
            'id' => $categorytype->getId()
        ]);
    }

    private function icons() {
        $res = [
            ["value" =>"las la-utensils", "name" => "restaurant"],
            ["value" =>"las la-spa", "name" => "beauty"],
            ["value" =>"las la-dumbbell", "name" => "fitness"],
            ["value" =>"las la-cocktail", "name" => "nightlight"],
            ["value" =>"las la-shopping-bag", "name" => "shopping"],
            ["value" =>"las la-film", "name" => "cinema"],
        ];
        return $res;
    }

    private function icon($name) {
        $res = "las la-spa";
        $icons = $this->icons();
        foreach($icons as $icon) {
            if(str_contains($icon['name'], strtolower($name)) || str_contains(strtolower($name), $icon['name'])) {
                $res = $icon['value'];
                break;
            }
        }
        return $res;
    }
}
