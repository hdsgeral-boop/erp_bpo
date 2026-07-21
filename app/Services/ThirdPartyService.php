<?php

namespace App\Services;

use App\Repositories\Contracts\ThirdPartyRepositoryInterface;
use Exception;

class ThirdPartyService
{
    protected $thirdPartyRepository;

    public function __construct(ThirdPartyRepositoryInterface $thirdPartyRepository)
    {
        $this->thirdPartyRepository = $thirdPartyRepository;
    }

    public function getAllPaginated($perPage = 15, $search = null, $type = null)
    {
        return $this->thirdPartyRepository->paginate($perPage, $search, $type);
    }

    public function getById($id)
    {
        return $this->thirdPartyRepository->find($id);
    }

    public function createThirdParty(array $data)
    {
        // Validação de negócio: NIF único por empresa (exceto se vazio)
        if (!empty($data['nif'])) {
            $existing = $this->thirdPartyRepository->findByNif($data['nif'], $data['company_id']);
            if ($existing) {
                throw new Exception("Já existe uma entidade com este NIF ({$data['nif']}) nesta empresa.");
            }
        }

        // Lógica de Conta SNC automática caso esteja vazia
        if (empty($data['account_code'])) {
            $data['account_code'] = $this->generateAccountCode($data);
        }

        // Garantir que as checkboxes não enviadas sejam tratadas como false (o FormRequest já deve tratar disto, mas é uma dupla segurança)
        $data['is_customer'] = $data['is_customer'] ?? false;
        $data['is_supplier'] = $data['is_supplier'] ?? false;
        $data['is_active'] = $data['is_active'] ?? false;

        // Se nenhum for selecionado, podemos lançar erro ou forçar um (neste caso, a validação no FormRequest lidará com isso)

        return $this->thirdPartyRepository->create($data);
    }

    public function updateThirdParty($id, array $data)
    {
        // Validação de NIF único
        if (!empty($data['nif'])) {
            $existing = $this->thirdPartyRepository->findByNif($data['nif'], $data['company_id'], $id);
            if ($existing) {
                throw new Exception("Já existe outra entidade com este NIF ({$data['nif']}) nesta empresa.");
            }
        }

        $data['is_customer'] = $data['is_customer'] ?? false;
        $data['is_supplier'] = $data['is_supplier'] ?? false;
        $data['is_active'] = $data['is_active'] ?? false;

        return $this->thirdPartyRepository->update($id, $data);
    }

    public function deleteThirdParty($id)
    {
        // Aqui deve-se validar se a entidade tem documentos associados (faturas, recibos)
        // Se tiver, não se pode apagar, deve-se inativar.
        // Implementar futuramente quando houver tabelas de documentos.
        
        return $this->thirdPartyRepository->delete($id);
    }

    protected function generateAccountCode(array $data)
    {
        // Exemplo simplificado de geração de código SNC
        if (!empty($data['is_customer']) && !empty($data['is_supplier'])) {
            return '21/22-AUTO'; 
        } elseif (!empty($data['is_customer'])) {
            return '21.1-AUTO';
        } elseif (!empty($data['is_supplier'])) {
            return '22.1-AUTO';
        }
        
        return null;
    }
}
