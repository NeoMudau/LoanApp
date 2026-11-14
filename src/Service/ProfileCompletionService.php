<?php

namespace App\Service;

use App\Entity\Customer;

final class ProfileCompletionService
{
    /**
     * Check whether the customer's profile is complete.
     */
    public function isComplete(Customer $customer): bool
    {
        return empty($this->getMissingSections($customer));
    }

    /**
     * Return the profile sections that are incomplete.
     */
    public function getMissingSections(Customer $customer): array
    {
        $missingSections = [];

        if (!$customer->getAddress()) {
            $missingSections[] = 'Address';
        }

        if (!$customer->getEmploymentDetails()) {
            $missingSections[] = 'Employment Details';
        }

        return $missingSections;
    }
}
