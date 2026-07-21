<?php

namespace App\Traits;

use Exception;

trait MasterDataProtected
{
    public static function bootMasterDataProtected()
    {
        static::deleting(function ($model) {
            if (isset($model->is_master_data) && $model->is_master_data) {
                throw new Exception("Não é possível eliminar registos de sistema (Master Data).");
            }
        });

        static::updating(function ($model) {
            // Se for master data, talvez bloquear certas alterações. 
            // Para já bloqueamos apenas a remoção da flag.
            if ($model->getOriginal('is_master_data') && !$model->is_master_data) {
                throw new Exception("Não é possível remover a proteção de um registo Master Data.");
            }
        });
    }
}
