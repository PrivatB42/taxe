<?php

namespace App\Traits;

use App\Helpers\FileManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

trait ServiceTrait
{
    protected Model $model;
    protected array $datatableConfig = [];
    private array $defaultDatatableConfig = [
        'searchable_columns' => [],
        'sortable_columns'   => [],
        'default_order'      => ['column' => 'id', 'dir' => 'desc'],
        'filterable_columns' => [],
        'relations'          => [],
        'return_type'       => 'json', // json, array
        'resource'        => null, // Resource pour la transformation des données
    ];

    protected array $searchColumns = ['nom'];

    protected array $makeSlug = [];

    public function getDataConfig(): array
    {
        return array_merge($this->defaultDatatableConfig, $this->datatableConfig);
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function isExist($id): ?bool
    {
        return $this->model->where('id', $id)->exists();
    }

    public function find(int $id, $callback = null)
    {
        $query = $this->model->query();
        if ($callback) {
            $callback($query);
        }
        return $query->find($id);
    }

    public function getAll($callback = null)
    {
        $query = $this->model->query();
        if ($callback) {
            $callback($query);
        }
        return $query->get();
    }

    public function getPaginate(int $paginate, $callback = null)
    {
        $query = $this->model->query();
        if ($callback) {
            $callback($query);
        }
        return $query->paginate($paginate);
    }

    public function getBy(string $column, $value, string $operator = '=', $callback = null, $get = 'get')
    {
        $query = $this->model->query();

        match ($operator) {
            'in'     => $query->whereIn($column, (array) $value),
            'not in' => $query->whereNotIn($column, (array) $value),
            default  => $query->where($column, $operator, $value),
        };

        if ($callback) $callback($query);

        return $this->resolveGet($query, $get);
    }

    private function resolveGet($query, $get)
    {
        return match (true) {
            $get === 'get'   => $query->get(),
            $get === 'first' => $query->first(),
            is_int($get)     => $query->paginate($get),
            default          => $query->get(),
        };
    }

    public function getWhereIn(string $column, array $value, $callback = null, $get = 'get')
    {
        return $this->getBy($column, $value, 'in', $callback, $get);
    }

    public function getWhere(string $column, mixed $value, $callback = null, $get = 'get') //get, first, int
    {
        return $this->getBy($column, $value, '=', $callback, $get);
    }

    public function getWhereNot(string $column, mixed $value, $callback = null, $get = 'get') //get, first, int
    {
        return $this->getBy($column, $value, '!=', $callback, $get);
    }

    public function getWhereNotIn(string $column, array $value, $callback = null, $get = 'get')
    {
        return $this->getBy($column, $value, 'not in', $callback, $get);
    }


    public function getData($request, $callback = null)
    {
        $query = $this->model->query();

        if (count($this->getDataConfig()['relations']) > 0) {
            $query->with($this->getDataConfig()['relations']);
        }

        if ($callback) {
            $callback($query);
        } 

        return processDataTable(
            $request,
            $query,
            $this->getDataConfig(),
            $this->getDataConfig()['return_type']
        );
    }

    private function buildSlug(array $data): array
    {
        if (count($this->makeSlug) > 0) {
            $data['slug'] = '';
            foreach ($this->makeSlug as $field) {
                if (isset($data[$field])) {
                    $data['slug'] .= str($data[$field])->slug();
                }
            }
        }
        return $data;
    }

    public function store(array $data)
    {


        if (method_exists($this, 'beforeStore')) {
            $data = $this->beforeStore($data);
        }

        $data = $this->buildSlug($data);

        $result = $this->model->create($data);

        if (method_exists($this, 'afterStore')) {
            $this->afterStore($result, $data);
        }

        return $result;
    }

    public function update($entity, array $data)
    {
        
        $data = $this->buildSlug($data);

        if (method_exists($this, 'beforeUpdate')) {
            $data = $this->beforeUpdate($entity, $data);
        }

        $result = $entity->update($data);

        if (method_exists($this, 'afterUpdate')) {
            $this->afterUpdate($entity, $data);
        }

        return $result;
    }

    public function delete($entity)
    {
        if (method_exists($this, 'beforeDelete')) {
            $this->beforeDelete($entity);
        }
        return $entity->delete();

        if (method_exists($this, 'afterDelete')) {
            $this->afterDelete($entity);
        }
    }

    public function toggleActive($entity)
    {
        if (method_exists($this, 'beforeToggleActive')) {
            $this->beforeToggleActive($entity);
        }
        $entity->is_active = !$entity->is_active;
        return $entity->save();

        if (method_exists($this, 'afterToggleActive')) {
            $this->afterToggleActive($entity);
        }
    }

    public function search(string|null $search = null, $callback = null)
    {
        $columns = $this->searchColumns;
        $query = $this->model->query(); 


        if ($search && count($columns) > 0) {
            $query->where(function ($q) use ($search, $columns) {
                foreach ($columns as $column) {
                    $q->orWhere($column, 'like', '%' . $search . '%');
                }
            }); 
        }


        if ($callback) {
            $callback($query);
        }

        return $query->limit(10)->get();
    }

    public static function changeFile(
        Object $Instance,
        string $column,
        UploadedFile $file,
        string|null $existingFilePath,
        string $directory,
        bool $isFullPath = false,
        string  $disk = 'public'
    ) {

        if ($file instanceof UploadedFile) {
            $filePath = FileManager::update($file, $existingFilePath, $directory, $isFullPath, $disk);
        }

        $Instance->$column = $filePath;
        $Instance->save();

        return $Instance;
    }

    public static function changeFileWithName(
        Model $Instance,
        string $column,
        UploadedFile $file,
        string|null $existingFilePath,
        string $filename,
        string $directory,
        bool $isFullPath = false,
        string  $disk = 'public'
    ) {

        if ($file instanceof UploadedFile) {
            $filePath = FileManager::updateWithName($file, $existingFilePath, $filename, $directory, $isFullPath, $disk);
        }

        $Instance->$column = $filePath;
        $Instance->save();

        return $Instance;
    }
}
