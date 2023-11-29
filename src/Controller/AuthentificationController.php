<?php

namespace App\Controller;

use App\Repository\UserRepository; // Adjust this line to the correct namespace
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Routing\Annotation\Route;

class AuthentificationController extends AbstractController
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/login", name="app_login", methods={"POST"})
     */
    public function apiLogin(Request $request, UserPasswordEncoderInterface $passwordEncoder, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Email and password are required.'], 400);
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new BadCredentialsException('Invalid email or password.');
        }

        // Vérifiez si le mot de passe est correct
        if (!$passwordEncoder->isPasswordValid($user, $password)) {
            throw new BadCredentialsException('Invalid email or password.');
        }

        // Générez un token JWT
        $token = $jwtManager->create($user);

        // Retournez le token dans la réponse JSON
        return new JsonResponse(['token' => $token]);
    }
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
