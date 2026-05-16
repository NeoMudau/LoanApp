<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\LoanRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ApplicantController extends AbstractController
{
    #[Route('/applicant', name: 'app_applicant')]
    public function index(): Response
    {
        return $this->render('applicant/index.html.twig', [
            'controller_name' => 'ApplicantController',
        ]);
    }

    #[Route('/applicant/loans', name: 'app_my_loans')]
    public function applicantLoans(LoanRepository $loanRepository,CustomerRepository $customerRepository): Response {
        $customer = $customerRepository->findOneBy(['user' => $this->getUser()]);

        if (!$customer) {
            throw $this->createNotFoundException('No customer profile found for this account.');
        }

        $customerId = $customer->getId();

        return $this->render('applicant/loans.html.twig', [
            'loans'       => $loanRepository->findBy(['customer_id' => $customer]),
            'totalLoans'  => $loanRepository->countLoansByCustomer($customerId),
            'activeLoans' => $loanRepository->countActiveLoansByCustomer($customerId),
            'closedLoans' => $loanRepository->countClosedLoansByCustomer($customerId),
            'overdueLoans'=> $loanRepository->countOverdueLoans(),
        ]);
    }

    #[Route('/applicant/apply/loans', name: 'app_loan_apply')]
    public function ApplyLoan(): Response
    {
        return $this->render('applicant/apply_loan.html.twig', [
            'controller_name' => 'ApplicantController',
        ]);
    }
}
