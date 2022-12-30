<?php

namespace App\Controller;

use App\Form\ResetPasswordFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/connexion', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    #[Route(path: '/deconnexion', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/oubli-pass', name: 'app_forgotten_password')]
    public function forgottenPassword(
        Request $request,
        UserRepository $userRepository,
        TokenGeneratorInterface $tokenGeneratorInterface,
        EntityManagerInterface $entityManager,
        SendMailService $sendMailService
    ): Response {
        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //On vérifie si l'utilisateur correspon bien à l'email
            $user = $userRepository->findOneByEmail($form->get('email')->getData());

            if ($user) {
                //On génère un token
                $token = $tokenGeneratorInterface->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                //On génère l'url de réinitialisation de mot de passe
                $url = $this->generateUrl('app_reset_password', [
                    'token' => $token
                ], UrlGeneratorInterface::ABSOLUTE_URL);

                //On créer les données du mail
                $context = [
                    'user' => $user,
                    'url' => $url
                ];

                //On envoie le mail
                $sendMailService->send(
                    'no-reply@laboutique-dynamique.fr',
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    'password_reset',
                    $context
                );

                $this->addFlash('success', 'Mail envoyé avec succès, veuillez suivre les indications que vous avez reçus par mail');
                return $this->redirectToRoute('app_login');
            }

            //$user est null
            $this->addFlash('danger', 'Un problème est survenu, l\'e-mail est peut être incorrect, veuillez réessayer');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'requestPassForm' => $form->createView()
        ]);
    }

    #[Route(path: '/oubli-pass/{token}', name: 'app_reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        //On vérifie si on a ce token dans la base de données
        $user = $userRepository->findOneByResetToken($token);

        if ($user) {
            $form = $this->createForm(ResetPasswordFormType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe modifié avec succès');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/reset_password.html.twig', [
                'passForm' => $form->createView()
            ]);
        }
        $this->addFlash('danger', 'Un problème est survenu, le lien est peut être incorrect, veuillez réessayer');
    }
}
