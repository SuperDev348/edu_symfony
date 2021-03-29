<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ConnexionType;
use App\Form\ImageUploadType;
use App\Form\PasswordbackupType;
use App\Form\PasswordkeyType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/intunisia/profil", name="profil")
     */
    public function profil(Request $request,SessionInterface $session): Response
    {   
        if(is_null($session->get('user'))){
            return $this->redirectToRoute('connexion');
        }
        $user=$session->get('user');
        $user= $this->getDoctrine()->getRepository(User::class)
            ->find($user->getId());
        $form_image=$this->createForm(ImageUploadType::class);
        $form_image->add('save image',SubmitType::class);
        $form_image->handleRequest($request);
        if($form_image->isSubmitted()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form_image['image']->getData();
            $destination = $this->getParameter('kernel.project_dir') . '/public/profil';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $originalFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

            if ($uploadedFile) {
                $uploadedFile->move(
                    $destination,
                    $newFilename
                );}
                $user->setImage($newFilename);
                $this->getDoctrine()->getManager()->flush();
                $session->set('user',$user);

        }
        return $this->render('pages/user/profil.html.twig',['user'=>$user,'formup'=>$form_image->createView(),]);
    }
    /**
     * @Route("/intunisia/profil/edit", name="editprofil" )
     */
    public function editprofil(Request $request,SessionInterface $session): Response
    {
        $user=$session->get('user');
        $user= $this->getDoctrine()->getRepository(User::class)
            ->find($user->getId());
        $formuser=$this->createForm(UserType::class,$user);
        $formuser->add('save',SubmitType::class);
        $formuser->handleRequest($request);

        if($formuser->isSubmitted()&&$formuser->isValid()){
            $user->setPassword(password_hash ($user->getPassword(),PASSWORD_DEFAULT));
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('profil');
        }

        return $this->render('pages/user/profilfrontedit.html.twig',['userform'=>$formuser->createView(),
            'user'=>$user

        ]);
    }
    /**
     * @Route("/intunisia/profil/deactiver", name="desactiver_compte")
     */
    public function Desactiver_compte(Request $request,SessionInterface $session): Response
    {
       if(is_null($session->get('user'))){
       return  $this->redirectToRoute('connexion');
       }
        $session->get('user')->setActive(false);
       return $this->redirectToRoute('profil');

    }
    /**
     * @Route("/user/connexion", name="connexion")
     */
    public function connexion(Request $request, SessionInterface $session): Response
    {
        $user = new User();
        $user->setNom("static");
        $user->setPrenom("static");
        $form = $this->createForm(ConnexionType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $verifexite = $this->getDoctrine()->getRepository(User::class)->findOneBy([
                'mail' => $user->getMail(),
            ]);
            if (is_null($verifexite)||password_verify($user->getPassword(),$verifexite->getPassword())==false) {
                return $this->render('pages/user/message.html.twig',['message'=>"Email ou password non valid" ]);
            }else if($verifexite->getBan()){
                return $this->render('pages/user/message.html.twig',['message'=>'vous avez été banni de notre site']);
            }
            else {
                $verifexite->setActive(true);
                $session->set('user',$verifexite);
                if ($verifexite->getType() == "client"||$verifexite->getType()=="businessowner") {
                    return $this->redirectToRoute('dashboard', array('id' => $verifexite->getId()));
                } elseif ($verifexite->getType() == "admin") {
                    return $this->redirectToRoute('admin_dashboard');
                }
            }
        }
        return $this->render('pages/user/connexion.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/user/deconnexion", name="deconnexion")
     */
    public function deconnexion(Request $request, SessionInterface $session): Response
    {   $session->clear();
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/user/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request,SessionInterface $session): Response
    {

        if(!(is_null($session->get('googleuser')))){
            $user= new User();
            $gooleuser=$session->get('googleuser');
            $user->setMail($gooleuser->getEmail());

            $user->setNom($gooleuser->getName());
            $user->setPrenom($gooleuser->getLastName());

        }else{
            $user = new User();
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $verifmail=$this->getDoctrine()->getRepository(User::class)->findOneBy(['mail'=>$user->getMail()]);
            if(is_null($verifmail)) {
                $user->setType('client');
                $user->setBan(false);
                $user->setActive(true);
                $user->setPassword(password_hash($user->getPassword(), PASSWORD_DEFAULT));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                $session->clear();

                return $this->redirectToRoute('home');
            }else{
                $this->addFlash('message',"l'adresse e-mail existe déjà ");
            }
            }

        return $this->render('pages/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/user/password_backup", name="user_password_backup",)
     */
    public function passwordbackup(MailerInterface $mailer,Request $request,SessionInterface $session): Response
    {

        $form = $this->createForm(PasswordbackupType::class);
        $form->add('Envoyer',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $email=$form->get('email')->getData();
            $user=$this->getDoctrine()->getRepository(User::class)->findOneBy(['mail'=>$email]);
            if(is_null($user)){
                $this->addFlash('message','Cette adresse e-mail est inconnue');
            }else{
                $cle =(bin2hex(random_bytes(3)));
                $session->set('cle',$cle);
                $session->set('userbackup',$user);
                $email=(new Email())
                    ->from('intunisia.symfony@gmail.com')
                    ->to($user->getMail())
                    ->subject('code de verification')
                    ->text($cle);
                $mailer->send($email);
                $this->addFlash('message','un code de vérification vous vous être envoyé pour confirmer votre identité ');
                return $this->redirectToRoute('user_password_backup_key');

            }
        }
        return $this->render('pages/user/passwordbackup.html.twig',['passwordform'=>$form->createView()]);
    }

    /**
     * @Route("/user/password_backup/key", name="user_password_backup_key",)
     */
    public function passwordbackupkey(MailerInterface $mailer,Request $request,SessionInterface $session): Response
    {
        if(is_null($session->get('cle'))||is_null($session->get('userbackup'))){
            return $this->redirectToRoute("user_password_backup");
        }
        $paswwordkey=$this->createForm(PasswordkeyType::class);
        $paswwordkey->add('Envoyer',SubmitType::class);
        $paswwordkey->handleRequest($request);
        if($paswwordkey->isSubmitted()){
            $secret_key=$session->get('cle');
            $key=$paswwordkey->get('cle')->getData();
            if($secret_key==$key){
                $this->addFlash('message','Changer votre mot de passe ');
                return $this->redirectToRoute('usereditpassword');
            }else{
                $this->addFlash('message','code de verification non valid');
            }
        }
        return  $this->render('pages/user/passwordbackup.html.twig',['keyform'=>$paswwordkey->createView()]);

    }
    /**
     * @Route("/admin/user", name="admin_user", methods={"GET"})
     */
    public function index(UserRepository $userRepository,SessionInterface $session): Response
    {   if(is_null($session->get('user'))||$session->get('user')->getType()!="admin"){
        return $this->redirectToRoute('deconnexion');
    }
        return $this->render('pages/admin/user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }
    /**
     * @Route("/admin/user/editpassword", name="usereditpassword",)
     */
    public function editpassword(Request $request,SessionInterface $session): Response
    {
        if(is_null($session->get('cle'))||is_null($session->get('userbackup'))){
            return $this->redirectToRoute("user_password_backup");
        }
        $user=new User();
        $user=$this->getDoctrine()->getRepository(User::class)->find($session->get('userbackup')->getId());
        $user->setPassword('');
        $passwordform=$this->createForm(UserType::class,$user);
        $passwordform->add('Envoyer',SubmitType::class);
        $passwordform->handleRequest($request);
        if($passwordform->isSubmitted()){
            $user->setPassword(password_hash($user->getPassword(),PASSWORD_DEFAULT));
            $this->getDoctrine()->getManager()->flush();
            $session->clear();
            return $this->redirectToRoute('connexion');
        }
        return  $this->render('pages/user/passwordbackup.html.twig',['passwordbackupform'=>$passwordform->createView()]);
    }
    /**
     * @Route("/admin/user/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user,SessionInterface $session): Response
    {if(is_null($session->get('user'))||$session->get('user')->getType()!="admin"){
        return $this->redirectToRoute('deconnexion');
    }
        return $this->render('pages/admin/user/detail.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/admin/user/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user,SessionInterface $session): Response
    {if(is_null($session->get('user'))||$session->get('user')->getType()!="admin"){
        return $this->redirectToRoute('deconnexion');
    }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_user');
        }

        return $this->render('pages/admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_user');
    }
    /**
     * @Route("/admin/user/ban/{iduser}", name="user_ban")
     */
    public function ban(Request $request, $iduser,SessionInterface $session): Response
    {
        if(is_null($session->get('user'))||$session->get('user')->getType()!="admin"){
            return $this->redirectToRoute('deconnexion');
        }
        $us=$this->getDoctrine()->getRepository(User::class)->find($iduser);
        $us->setBan(true);
        $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_user');

    }
    /**
     * @Route("/admin/user/restaurer/{iduser}", name="user_restaurer")
     */
    public function restaurer(Request $request, $iduser,SessionInterface $session): Response
    {
        if(is_null($session->get('user'))||$session->get('user')->getType()!="admin"){
            return $this->redirectToRoute('deconnexion');
        }
        $us=$this->getDoctrine()->getRepository(User::class)->find($iduser);
        $us->setBan(false);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('admin_user');

    }
    /**
     * @Route("/admin/{filtre}/{valeur}", name="user_filtre")
     */
    public function filtre($filtre,$valeur,SessionInterface $session,UserRepository $userRepository): Response
    {
        if(is_null($session->get('user'))||$session->get('user')->getType()!="admin"){
            return $this->redirectToRoute('deconnexion');
        }
        return $this->render('pages/admin/user/index.html.twig', [
            'users' => $userRepository->findBy([$filtre=>$valeur]),
        ]);
    }
}
