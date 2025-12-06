<?php

namespace Modules\User\Services;


use App\Helpers\Constantes;
use App\Services\BaseService;
use Modules\User\Models\Personne;

class PersonneService extends BaseService
{
    protected Personne $personne;

    public function __construct(Personne $personne)
    {
        parent::__construct($personne);
    }

    protected array $makeSlug = ['nom', 'telephone'];

    public function rules($id = null): array
    {
        return [
            'email' => 'nullable|email|unique:user_personnes,email,' . $id,
            'nom_complet' => 'required|string',
            'telephone' => 'required|numeric|digits:10|unique:user_personnes,telephone,' . $id,
            'password' => 'nullable|string|min:8|confirmed',
            //'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/',
        ];
    }



    public function changePhoto(string $url, $personne)
    {
        $personne->photo = $url;
        $personne->save();
    }



    public function exist_personne($param)
    {
        $personne =  $this->model::with('compte')
            ->where('email', $param)
            ->orWhere('id', $param)
            ->orWhere('telephone', $param)
            ->first();

        if (!$personne) {
            return null;
        }

        return  $personne;
    }
}
