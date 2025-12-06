<?php

namespace App\Traits;

use App\Helpers\Constantes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait ControllerTrait
{
    protected $service;
    protected string $instanceName;
    protected string $viewPath;

    public function index()
    {
        return view($this->viewPath);
    }

    public function getData(Request $request)
    {
        return $this->service->getData($request);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(
            $this->service->rules(),
            Constantes::VALIDATION_MESSAGES
        );

        try {
            DB::beginTransaction();

            // hook qui peut modifier les données
            $validated = $this->beforeStore($request, $validated);
            $entity = $this->service->store($validated);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionResponse($e);
        }

        $this->afterStore($request, $entity, $validated);

        if (method_exists($this, 'storeRedirect')) {
            return $this->storeRedirect($request, $entity, $validated);
        }

        if (method_exists($this, 'storeResponseWithData')) {
            return $this->successResponse(
                "{$this->instanceName} créé(e) avec succès",
                $this->storeResponseWithData($request, $entity, $validated)
            );
        }

        return $this->successResponse("{$this->instanceName} créé(e) avec succès");
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate(
            $this->service->rules($id),
            Constantes::VALIDATION_MESSAGES
        );

        if (!$this->service->isExist($id)) {
            return $this->notFoundResponse();
        }


        $entity = $this->service->find($id);

        try {
            DB::beginTransaction();

            $validated = $this->beforeUpdate($request, $entity, $validated);
            $this->service->update($entity, $validated);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionResponse($e);
        }

        $this->afterUpdate($request, $entity, $validated);

        if (method_exists($this, 'updateRedirect')) {
            return $this->updateRedirect($request, $entity, $validated);
        }

        if (method_exists($this, 'updateResponseWithData')) {
            return $this->successResponse(
                "{$this->instanceName} modifié(e) avec succès",
                $this->updateResponseWithData($request, $entity, $validated)
            );
        }

        return $this->successResponse("{$this->instanceName} modifié(e) avec succès");
    }

    public function toggleActive(int $id)
    {
        if (!$this->service->isExist($id)) {
            return $this->notFoundResponse();
        }

        $entity = $this->service->find($id);

        try {
            DB::beginTransaction();

            $this->beforeToggleActive($id, $entity);
            $this->service->toggleActive($entity);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionResponse($e);
        }

        $this->afterToggleActive($id, $entity);

        if (method_exists($this, 'toggleActiveRedirect')) {
            return $this->toggleActiveRedirect($entity);
        }

        if (method_exists($this, 'toggleActiveResponseWithData')) {
            return $this->successResponse(
                'Statut modifié avec succès',
                $this->toggleActiveResponseWithData($entity)
            );
        }

        return $this->successResponse('Statut modifié avec succès');
    }

    public function delete(int $id)
    {
        if (!$this->service->isExist($id)) {
            return $this->notFoundResponse();
        }

        $entity = $this->service->find($id);

        try {
            DB::beginTransaction();

            $this->beforeDelete($id, $entity);
            $this->service->delete($entity); 

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->exceptionResponse($e);
        }

        $this->afterDelete($id, $entity);

        if (method_exists($this, 'deleteRedirect')) {
            return $this->deleteRedirect($entity);
        }

        if (method_exists($this, 'deleteResponseWithData')) {
            return $this->successResponse(
                "{$this->instanceName} supprimé(e) avec succès",
                $this->deleteResponseWithData($entity)
            );
        }

        return $this->successResponse("{$this->instanceName} supprimé(e) avec succès");
    }

    public function search(Request $request)
    {
        $search = $request->get('q');
        return $this->service->search($search);
    }

    // ----------------------
    // Réponses standardisées
    // ----------------------
    protected function successResponse(string $message, array $extra = [])
    {
        return response()->json(array_merge([
            'success' => true,
            'message' => $message,
        ], $extra));
    }

    protected function notFoundResponse()
    {
        return response()->json([
            'success' => false,
            'message' => "{$this->instanceName} non trouvé(e)",
        ], 404);
    }

    protected function errorResponse(string $message, int $status = 400, array $extra = [])
    {
        return response()->json(array_merge([
            'success' => false,
            'message' => $message,
        ], $extra), $status);
    }


    protected function exceptionResponse(\Exception $exception)
    {
        return response()->json([
            'success' => false,
            'message' => $this->buildExeptionMessage($exception),
        ], 500);
    }


    private function buildExeptionMessage(\Exception $exception): string
    {
        $messageModeDeDebug = "Exception: {$exception->getMessage()} in {$exception->getFile()} at line {$exception->getLine()}";
        $messageProduction = "Une erreur est survenue, veuillez réessayer plus tard ou contacter l'administrateur si le problème persiste. : {$exception->getMessage()}";
        return config('app.debug') ? $messageModeDeDebug : $messageProduction;
    }

    // ----------------------
    // Hooks personnalisables
    // ----------------------
    protected function beforeStore(Request $request, array $data): array
    {
        return $data;
    }
    protected function afterStore(Request $request, $entity, array $data): void {}

    protected function beforeUpdate(Request $request, $entity, array $data): array
    {
        return $data;
    }
    protected function afterUpdate(Request $request, $entity, array $data): void {}

    protected function beforeDelete(int $id, $entity): void {}
    protected function afterDelete(int $id, $entity): void {}

    protected function beforeToggleActive(int $id, $entity): void {}
    protected function afterToggleActive(int $id, $entity): void {}
}
