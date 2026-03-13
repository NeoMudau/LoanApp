<?php

namespace App\Command;

use App\Repository\LoanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:loan:rollover',
    description: 'Monthly rollover: recalculates all active loan balances on the 1st of each month',
)]
class LoanRolloverCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LoanRepository $loanRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $today = new \DateTimeImmutable();

        // Safety check: only run on the 1st of the month
        if ($today->format('j') !== '1') {
            $io->warning('This command should only run on the 1st of the month. Aborting.');
            return Command::FAILURE;
        }

        $loans = $this->loanRepository->createQueryBuilder('l')
            ->where('l.status IN (:statuses)')
            ->setParameter('statuses', ['ACTIVE', 'OVERDUE'])
            ->getQuery()
            ->getResult();

        $io->info(sprintf('Processing %d active/overdue loans...', count($loans)));

        foreach ($loans as $loan) {
            $result = $loan->rolloverMonth();

            if ($result['skipped'] ?? false) {
                continue;
            }

            $io->writeln(sprintf(
                'Loan #%d | Case: %s | Paid: R%s | New Balance: R%s %s',
                $result['loan_id'],
                $result['case'],
                $result['paid'],
                $loan->getBalanceRemaining(),
                $result['refund'] > 0 ? "| ⚠️  REFUND: R{$result['refund']}" : ''
            ));
        }

        $this->em->flush();
        $io->success('Rollover complete.');

        return Command::SUCCESS;
    }
}