<?php
   
namespace App\Controller;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use Symfony\Component\HttpFoundation\Request;

class GoogleController extends AbstractController
{
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/connect/google/", name="connect_google_start")
     */
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // will redirect to google!
        return $clientRegistry
            ->getClient('google_connect') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect();
    }

    /**
     * After going to GOOGle, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/connect/google/check/", name="connect_google_check")
     */
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry,SessionInterface $session)
    {
        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\googleClient $client */
        $client = $clientRegistry->getClient('google_connect');

        try {
            /** @var \League\OAuth2\Client\Provider\googleUser $user */
            $user = $client->fetchUser();
            $googleuser=new User();
            $googleuser=$this->getDoctrine()->getRepository(User::class)->findOneBy(array('mail'=>$user->getEmail()));
            if (is_null($googleuser)){
                return $this->render('pages/user/message.html.twig',['message'=>'compte introuvable']);
            }
            else if($googleuser->getBan()) {
                return $this->render('pages/user/message.html.twig',['message'=>'Vous avez été banni ']);

            }else {

                if ($googleuser->getType() == "client" || $googleuser->getType() == "businessowner") {
                    $googleuser->setActive(true);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->flush();
                    $session->set('user', $googleuser);

                    return $this->redirectToRoute('dashboard');
                } elseif ($googleuser->getType() == "admin") {
                    $session->set('user', $googleuser);
                    return $this->redirectToRoute('admin_dashboard');
                }
            }
        } catch (IdentityProviderException $e) {
            return $this->redirectToRoute('home');
        }

    }
    /**
     * Link to this controller to start the "connect" process
     *
     * @Route("/inscription/google/", name="inscription_google_start")
     */
    public function inscriptionAction(ClientRegistry $clientRegistry)
    {
        // will redirect to google!
        return $clientRegistry
            ->getClient('google_inscription') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect();
    }

    /**
     * After going to GOOGle, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     *
     * @Route("/inscription/google/check/", name="inscription_google_check")
     */
    public function inscriptionCheckAction(Request $request, ClientRegistry $clientRegistry,SessionInterface $session)
    {
        /** @var \KnpU\OAuth2ClientBundle\Client\Provider\googleClient $client */
        $client = $clientRegistry->getClient('google_inscription');

        try {
            /** @var \League\OAuth2\Client\Provider\googleUser $user */
            $user = $client->fetchUser();
            $session->set('googleuser',$user);
            return $this->redirectToRoute('user_new');

        }

        catch (IdentityProviderException $e) {
            return $this->redirectToRoute('user_new');
        }

    }
}
