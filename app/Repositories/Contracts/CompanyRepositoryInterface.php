<?php

namespace App\Repositories\Contracts;

interface CompanyRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get the master data company
     */
    public function getMasterCompany();
}
