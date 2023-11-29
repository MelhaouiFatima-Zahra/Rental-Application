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
        // Decode the JSON content from the request
        $data = json_decode($request->getContent(), true);

        // Extract email and password from the decoded data
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        // Check if email and password are provided
        if (!$email || !$password) {
            return new JsonResponse(['error' => 'Email and password are required.'], 400);
        }

        // Find the user by email in the UserRepository
        $user = $this->userRepository->findOneBy(['email' => $email]);

        // If the user is not found, throw a BadCredentialsException
        if (!$user) {
            throw new BadCredentialsException('Invalid email or password.');
        }

        // Check if the provided password is valid for the user
        if (!$passwordEncoder->isPasswordValid($user, $password)) {
            throw new BadCredentialsException('Invalid email or password.');
        }

        // Generate a JWT token for the authenticated user
        $token = $jwtManager->create($user);

        // Return the JWT token in the JSON response
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
