<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\LoanApplication;
use App\Form\LoanApplicationType;
use App\Service\ProfileCompletionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LoanController extends AbstractController
{
    #[Route('/applicant/apply/loans', name: 'app_loan_apply')]
    public function applyLoan(
        Request $request,
        EntityManagerInterface $entityManager,
        ProfileCompletionService $profileCompletionService
    ): Response {

        // Get the logged-in user
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Find the customer's profile
        $customer = $entityManager
            ->getRepository(Customer::class)
            ->findOneBy(['user' => $user]);

        if (!$customer) {
            throw $this->createNotFoundException('Customer profile not found.');
        }

        // Check whether the profile is complete
        $missingSections = $profileCompletionService
            ->getMissingSections($customer);

        if ($missingSections !== []) {
            $missing = implode(' and ', $missingSections);

            $this->addFlash(
                'warning',
                'Please complete your ' . $missing . ' before applying for a loan.'
            );

            return $this->redirectToRoute('app_profile');
        }

        // Create a new loan application
        $application = new LoanApplication();

        $form = $this->createForm(LoanApplicationType::class, $application);

        $form->handleRequest($request);

        // Process submitted application
        if ($form->isSubmitted() && $form->isValid()) {

            // Associate application with customer
            $customer->addLoanApplication($application);

            // Set initial status
            $application->setStatus('SUBMITTED');

            // Save application
            $entityManager->persist($application);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Your loan application has been submitted successfully.'
            );

            return $this->redirectToRoute('app_loan_apply');
        }

        return $this->render('applicant/apply_loan.html.twig', [
            'loanForm' => $form->createView(),
        ]);
    }
}