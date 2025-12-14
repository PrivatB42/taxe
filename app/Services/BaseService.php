<?php 

namespace App\Services;

use App\Helpers\FileManager;
use App\Traits\ServiceTrait;
use Illuminate\Database\Eloquent\Model;

abstract class BaseService
{

    use ServiceTrait;
    protected $fileManager;
    
    protected Model $model;

    public function __construct($model)
    {
        $this->model = $model;
        $this->fileManager = new FileManager();
    }

    // Méthodes optionnelles pour hooks
    protected function beforeStore(array $data) { return $data; }
    protected function afterStore($entity, array $data) {}
    protected function beforeUpdate($entity, array $data) { return $data; }
    protected function afterUpdate($entity, array $data) {}
    protected function beforeDelete($entity) {}
    protected function afterDelete($entity) {}
    protected function beforeToggleActive($entity) {}
    protected function afterToggleActive($entity) {}
    

    public function rules($id = null): array { return []; }
}