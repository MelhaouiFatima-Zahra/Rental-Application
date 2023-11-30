<?php

namespace App\Tests\Service;

use App\Entity\Car;
use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\CarRepository;
use App\Repository\ReservationRepository;
use App\Repository\UserRepository;
use App\Service\ReservationService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;


class ResrationServiceTest extends TestCase
{
    // Mock objects for repositories and entity manager
    private $carRepository;

    private $userRepository;
    private $entityManager;

    private $reservationRepository;

    private $reservationService;
    protected function setUp(): void
    {
        // Create mock objects for dependencies
        $this->carRepository = $this->createMock(CarRepository::class);
        $this->userRepository = $this->createMock(UserRepository::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->reservationRepository= $this->createMock(ReservationRepository::class);
        $this->reservationService = $this->createMock(ReservationService::class);
    }

    public function testCreateReservationSuccess()
    {
        // Arrange
        $carId = 1;
        $userId = 1;
        $startDate = '2023-01-01';
        $endDate = '2023-01-05';

        // Mock Car and User entities
        $car = new Car();
        $user = new User();

        $this->carRepository->expects($this->once())
            ->method('find')
            ->with($carId)
            ->willReturn($car);

        $this->userRepository->expects($this->once())
            ->method('find')
            ->with($userId)
            ->willReturn($user);

        // Mock EntityManager
        $this->entityManager->expects($this->once())
            ->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush');

        // Act
        $reservationService = new ReservationService($this->entityManager, $this->reservationRepository ,  $this->carRepository, $this->userRepository);
        $result = $reservationService->createReservation($userId, $carId, $startDate, $endDate);

        // Assert
        $this->assertEquals('Reservation created successfully', $result);
    }
    public function testCreateReservationCarNotFound()
    {
        // Arrange
        $carId = 1;
        $userId = 1;
        $startDate = '2023-01-01';
        $endDate = '2023-01-05';

        // Mock UserRepository
        $this->carRepository->expects($this->once())
            ->method('find')
            ->with($carId)
            ->willReturn(null);

        // Act
        $reservationService = new ReservationService($this->entityManager, $this->reservationRepository ,  $this->carRepository, $this->userRepository);

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Car not found with the provided ID.');

        $reservationService->createReservation($userId, $carId, $startDate, $endDate);
    }
    public function testCancelReservation()
    {
        // Create a mock for the Reservation class
        $mockedReservation = $this->createMock(Reservation::class);

        // Configure the mock to return the mocked reservation when 'find' is called
        $this->reservationRepository->method('find')->willReturn($mockedReservation);

        // Create a mock for the EntityManagerInterface
        $entityManager = $this->createMock(EntityManagerInterface::class);

        // Create an instance of ReservationService by injecting the mocks
        $reservationService = new ReservationService(
            $entityManager,
            $this->reservationRepository,
            $this->carRepository,
            $this->userRepository
        );

        // Set up the expectation that the 'find' method will be called with the correct ID
        $this->reservationRepository->expects($this->once())
            ->method('find')
            ->with(5)
            ->willReturn($mockedReservation);

        // Set up the expectation that the 'flush' method will be called
        $entityManager->expects($this->once())
            ->method('flush');

        // Call the cancelReservation method with an appropriate ID
        $result = $reservationService->cancelReservation(5);

        // Perform assertions on the result or the expected state
        $this->assertEquals('Reservation canceled successfully', $result);
    }
    public function testGetUserReservations()
    {

        $reservationService = new ReservationService(
            $this->entityManager,
            $this->reservationRepository,
            $this->carRepository,
            $this->userRepository
        );
        // Set up an example user ID
        $userId = 1;

        // Set up the expected result from the repository
        $expectedReservations = [
            // Create some Reservation objects for testing
            $this->createMock(Reservation::class),
            $this->createMock(Reservation::class),

        ];

        // Configure the mock repository to return the expected reservations
        $this->reservationRepository->method('findBy')
            ->with(['user' => $userId])
            ->willReturn($expectedReservations);

        // Call the getUserReservations method
        $userReservations = $reservationService->getUserReservations($userId);

        // Assert that the result matches the expected reservations
        $this->assertEquals($expectedReservations, $userReservations);
    }

}
