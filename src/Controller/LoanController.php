<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\Loan;
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

        // Create the loan application form
        $loan = new Loan();

        $form = $this->createForm(LoanApplicationType::class, $loan);

        $form->handleRequest($request);

        // Process submitted application
        if ($form->isSubmitted() && $form->isValid()) {

            $now = new \DateTimeImmutable();

            // Associate loan with logged-in customer
            $loan->setCustomerId($customer);

            // Set initial loan details
            $loan->setCreatedAt($now);
            $loan->setUpdatedAt($now);
            $loan->setExtension(false);
            $loan->setStatus('PENDING');

            // Persist application
            $entityManager->persist($loan);
            $entityManager->flush();

            $this->addFlash('success', 'Your loan application has been submitted successfully.');

            return $this->redirectToRoute('app_loan_apply');
        }

        return $this->render('applicant/apply_loan.html.twig', [
            'loanForm' => $form->createView(),
        ]);
    }
}
