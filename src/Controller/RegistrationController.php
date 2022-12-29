<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use App\Service\JWTService;
use App\Service\SendMailService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        UserAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        SendMailService $mail,
        JWTService $jWTService
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            // On génère le JWT pour l'activation du compte
            $header = [
                'alg' => 'HS256',
                'typ' => 'JWT',
            ];

            $payload = [
                'user_id' => $user->getId(),
                'exp' => time() + 3600,
            ];

            $token = $jWTService->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            // Envoi du mail de confirmation
            $mail->send(
                'no-reply@laboutique-dynamique.fr',
                $user->getEmail(),
                'Activation de votre compte sur laboutique-dynamique',
                'register',
                [
                    'user' => $user,
                    'token' => $token,
                ]
            );

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/activation/{token}', name: 'app_activation')]
    public function activation(
        $token,
        JWTService $jWTService,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response {
        // On vérifie si le token est valide, n'a pas expiré et qu'il n'a pas déjà été modifié
        if ($jWTService->isValid($token) && !$jWTService->isExpired($token) && $jWTService->check($token, $this->getParameter('app.jwtsecret'))) {
            // On récupère le payload
            $payload = $jWTService->getPayload($token);

            // On récupère l'utilisateur
            $user = $userRepository->find($payload['user_id']);

            // On vérifie que l'utilisateur n'est pas déjà activé et qu'il existe
            if (!$user->getIsVerified() && $user) {
                $user->setIsVerified(true);
                $entityManager->flush($user);
                $this->addFlash('success', 'Votre compte a bien été activé');
                return $this->redirectToRoute('profile_index');
            }
        }
        //Si la condition n'est pas valide on redirige vers la page d'accueil
        $this->addFlash('danger', 'Le token n\'est pas valide ou a expiré');
        return $this->redirectToRoute('app_login');
    }

    #[Route('/renvoiverification', name: 'resend_verif')]
    public function resendVerif(
        SendMailService $mail,
        JWTService $jWTService,
        UserRepository $userRepository,
    ): Response {
        // On récupère l'utilisateur connecté
        $user = $this->getUser();

        // On vérifie que l'utilisateur n'est pas déjà activé et qu'il existe
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('app_login');
        }

        // On vérifie que l'utilisateur n'est pas déjà activé
        if ($user->getIsVerified()) {
            $this->addFlash('dangery', 'Votre compte est déjà activé');
            return $this->redirectToRoute('profile_index');
        }

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];

        $payload = [
            'user_id' => $user->getId(),
            'exp' => time() + 3600,
        ];

        $token = $jWTService->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        // Envoi du mail de confirmation
        $mail->send(
            'no-reply@laboutique-dynamique.fr',
            $user->getEmail(),
            'Activation de votre compte sur laboutique-dynamique',
            'register',
            [
                'user' => $user,
                'token' => $token,
            ]
        );

        $this->addFlash('success', 'Email de vérification envoyé');
        return $this->redirectToRoute('profile_index');
    }
}
