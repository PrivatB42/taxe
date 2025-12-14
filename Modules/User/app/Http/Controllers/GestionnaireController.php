<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Modules\User\Models\Role;
use Modules\User\Services\GestionnaireService;

class GestionnaireController extends BaseController
{

    public function __construct(GestionnaireService $service)
    {
        parent::__construct(
            $service,
            'Gestionnaire',
            'user::pages.gestionnaire.index'
        );
    }

    public function index()
    {
        $roles = Role::where('is_active', true)->orderBy('nom')->get()->map(function($role) {
            return ['id' => $role->code, 'nom' => $role->nom];
        })->toArray();
        
        return view('user::pages.gestionnaire.index', compact('roles'));
    }

    public function search(Request $request)
    {
        $search = $request->get('q');
        $query = $this->service->getModel();


        $query->whereHas('personne', function ($q) use ($search, $request) {

            if ($search) {
                $q->where('nom_complet', 'like', '%' . $search . '%');
            }
        });

        if ($request->role) {
           $query = $query->where('role', $request->role);
        }

        if ($request->is_active && $request->is_active == 'true') {
            $query->where('is_active', true);
        }


        $gestionnaires = $query->limit(15)->get();

        return $gestionnaires->map(function ($gestionnaire) {
            return [
                'id' => $gestionnaire->id,
                'nom_complet' => $gestionnaire->personne?->nom_complet,
            ];
        });
    }
}
