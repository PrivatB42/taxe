<?php

namespace Modules\Auth\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Models\Compte;

class CompteService extends BaseService
{


    public function __construct(Compte $model)
    {
        parent::__construct($model);
    }

    protected function buildNumeroCompte(?string $typeCompte = null)
    {
        $prefix = Constantes::COMPTES_PREFIX[$typeCompte] ?? null;

        if (!$prefix) {
            throw new \Exception("Type de compte inconnue");
        }

        return $prefix . $this->model::max('id') . '.' . date('ydmh');
    }

    public function create(int $userId, string $typeCompte, string $password)
    {
        $password = Hash::make($password);
        $numeroCompte = $this->buildNumeroCompte($typeCompte);

        return $this->model::create([
            'personne_id' => $userId,
            'numero_compte' => $numeroCompte,
            'type_compte' => $typeCompte,
            'password' => $password
        ]);
    }
}
