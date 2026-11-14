<?php

namespace App\Controller;

use App\Entity\Address;
use App\Entity\Customer;
use App\Entity\EmploymentDetails;
use App\Entity\MonthlyExpense;
use App\Form\AddressType;
use App\Form\EmploymentDetailsType;
use App\Form\MonthlyExpenseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {

        // Get the logged-in user
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Find the associated customer
        $customer = $entityManager
            ->getRepository(Customer::class)
            ->findOneBy(['user' => $user]);

        if (!$customer) {
            throw $this->createNotFoundException('Customer profile not found.');
        }

        // Load existing records
        $address = $customer->getAddress() ?? new Address();
        $employmentDetails = $customer->getEmploymentDetails() ?? new EmploymentDetails();

        // Create a new expense record
        $monthlyExpense = new MonthlyExpense();

        // Create forms
        $addressForm = $this->createForm(AddressType::class, $address, [
            'method' => 'POST',
        ]);

        $employmentForm = $this->createForm(EmploymentDetailsType::class, $employmentDetails, [
            'method' => 'POST',
        ]);

        $expenseForm = $this->createForm(MonthlyExpenseType::class, $monthlyExpense, [
            'method' => 'POST',
        ]);

        // Handle submissions
        $addressForm->handleRequest($request);
        $employmentForm->handleRequest($request);
        $expenseForm->handleRequest($request);

        // Save address independently
        if ($addressForm->isSubmitted() && $addressForm->isValid()) {

            $customer->setAddress($address);

            $entityManager->persist($address);
            $entityManager->flush();

            $this->addFlash('success', 'Address details saved successfully.');

            return $this->redirectToRoute('app_profile');
        }

        // Save employment independently
        if ($employmentForm->isSubmitted() && $employmentForm->isValid()) {

            $customer->setEmploymentDetails($employmentDetails);

            $entityManager->persist($employmentDetails);
            $entityManager->flush();

            $this->addFlash('success', 'Employment details saved successfully.');

            return $this->redirectToRoute('app_profile');
        }

        // Save monthly expense independently
        if ($expenseForm->isSubmitted() && $expenseForm->isValid()) {

            $customer->addMonthlyExpense($monthlyExpense);

            $entityManager->persist($monthlyExpense);
            $entityManager->flush();

            $this->addFlash('success', 'Monthly expense added successfully.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/index.html.twig', [
            'addressForm' => $addressForm->createView(),
            'employmentForm' => $employmentForm->createView(),
            'expenseForm' => $expenseForm->createView(),
            'monthlyExpenses' => $customer->getMonthlyExpenses(),
        ]);
    }
}
