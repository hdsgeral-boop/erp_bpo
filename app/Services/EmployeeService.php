<?php

namespace App\Services;

use App\Repositories\Contracts\EmployeeRepositoryInterface;
use Exception;

class EmployeeService
{
    protected $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepository)
    {
        $this->employeeRepository = $employeeRepository;
    }

    public function getAllPaginated($perPage = 15, $search = null, $departmentId = null, $isActive = null)
    {
        return $this->employeeRepository->paginate($perPage, $search, $departmentId, $isActive);
    }

    public function getById($id)
    {
        return $this->employeeRepository->find($id);
    }

    public function createEmployee(array $data)
    {
        if (!empty($data['nif'])) {
            $existing = $this->employeeRepository->findByNif($data['nif'], $data['company_id']);
            if ($existing) {
                throw new Exception("Já existe um colaborador com este NIF ({$data['nif']}) nesta empresa.");
            }
        }

        $data = $this->formatFinancialData($data);
        
        $data['is_active'] = $data['is_active'] ?? false;
        $data['is_retired'] = $data['is_retired'] ?? false;
        $data['is_master_data'] = $data['is_master_data'] ?? false;

        return $this->employeeRepository->create($data);
    }

    public function updateEmployee($id, array $data)
    {
        if (!empty($data['nif'])) {
            $existing = $this->employeeRepository->findByNif($data['nif'], $data['company_id'], $id);
            if ($existing) {
                throw new Exception("Já existe outro colaborador com este NIF ({$data['nif']}) nesta empresa.");
            }
        }

        $data = $this->formatFinancialData($data);

        $data['is_active'] = $data['is_active'] ?? false;
        $data['is_retired'] = $data['is_retired'] ?? false;
        $data['is_master_data'] = $data['is_master_data'] ?? false;

        return $this->employeeRepository->update($id, $data);
    }

    public function deleteEmployee($id)
    {
        return $this->employeeRepository->delete($id);
    }

    /**
     * Helper to ensure financial values are safe to save.
     */
    protected function formatFinancialData(array $data)
    {
        if (isset($data['base_salary'])) {
            $data['base_salary'] = max(0, (float) $data['base_salary']);
        }
        if (isset($data['subsidy_meal'])) {
            $data['subsidy_meal'] = max(0, (float) $data['subsidy_meal']);
        }
        if (isset($data['subsidy_transport'])) {
            $data['subsidy_transport'] = max(0, (float) $data['subsidy_transport']);
        }
        return $data;
    }
}
