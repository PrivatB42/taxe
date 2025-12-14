<?php

namespace Modules\Paiement\Services;

use App\Helpers\Constantes;
use App\Services\BaseService;
use Exception;
use Modules\Paiement\Models\Paiement;
use Modules\User\Models\ContribuableTaxe;
use Modules\User\Models\Gestionnaire;

class PaiementService extends BaseService
{

    protected ContribuableTaxe $contribuableTaxe;
    protected Gestionnaire $gestionnaire;
    protected array $datatableConfig = [
        'searchable_columns' => ['date_paiement', 'caisse.nom', 'taxe.nom', 'reference'],
        'sortable_columns'   => ['date_paiement'],
        'default_order'      => ['column' => 'id', 'dir' => 'desc'],
        'filterable_columns' => [],
        'relations'          => ['caisse', 'caissier.personne', 'contribuable.personne', 'taxe'],
    ];

    public function __construct(Paiement $model, ContribuableTaxe $contribuableTaxe, Gestionnaire $gestionnaire)
    {
        parent::__construct($model);
        $this->contribuableTaxe = $contribuableTaxe;
        $this->gestionnaire = $gestionnaire;
    }

    public function rules($id = null): array
    {
        return [
            'contribuable_taxe_id' => 'required|integer|exists:user_contribuables_taxes,id',
            'montant_paie' => 'required|numeric|min:100',
        ];
    }

    public function beforeStore(array $data)
    {
        $gestionnaire = $this->gestionnaire->find(session('user.gestionnaire_id'));

        if (!$gestionnaire) {
            throw new Exception('Gestionnaire introuvable');
        }

        if ($gestionnaire->role != Constantes::ROLE_CAISSIER) {
            throw new Exception('Vous n\'etes pas un(e) caissier(e)');
        }

        $caisse = $gestionnaire->caisses()->first();

        if (!$caisse) {
            throw new Exception('Vous n\'avez pas accès a une caisse');
        }

        if ($caisse->statut != Constantes::STATUT_OUVERT || !$caisse->is_active) {
            throw new Exception('La caisse est fermée ou desactivée il n\'est pas possible de faire de paiement');
        }

        $contribuable_taxe_id = $data['contribuable_taxe_id'];
        $contribuable_taxe = $this->contribuableTaxe->find($contribuable_taxe_id);

        if ($contribuable_taxe->statut == Constantes::STATUT_PAYE) {
            throw new Exception('Taxe totalement payé');
        }

        $montant_encaisse = $data['montant_paie'];
        $multiple = $contribuable_taxe->taxe->formule ? 100 : $contribuable_taxe->montant;

        if ($montant_encaisse < $multiple) {
            throw new Exception('Le montant de paiement doit etre au moins ' . $multiple);
        }

        //$montant_encaisse = floor($montant_paie / $multiple) * $multiple;
        $montant = floor($montant_encaisse / $multiple) * $multiple;
        $montant_rendu = $montant_encaisse - $montant;

        $contribuable_taxe->montant_paye += $montant;

        if ($contribuable_taxe->montant_a_payer <= $contribuable_taxe->montant_paye) {
            $contribuable_taxe->statut = Constantes::STATUT_PAYE;
        }
        $contribuable_taxe->save();

        return [
            'reference' => strtoupper(uniqid()),
            'contribuable_taxe_id' => $contribuable_taxe_id,
            'caisse_id' => session('user.caisse')?->id,
            'caissier_id' => session('user.gestionnaire_id'),
            'montant' => $montant,
            'montant_encaisse' => $montant_encaisse,
            'montant_rendu' => $montant_rendu,
            'date_paiement' => now(),
        ];
    }


    public function activerPaiement($paiement)
    {
        $paiement->date_activement = now();
        $paiement->save();
    }

    public function sum(array $options = [])
    {

        //$query = $this->model->selectRaw('sum(montant) as total, count(*) as count');

        $query = $this->model::query();

        if (isset($options['caisse_id'])) {
            $query->where('caisse_id', $options['caisse_id']);
        }

        if (isset($options['caisses_ids'])) {
            $query->whereIn('caisse_id', $options['caisses_ids']);
        }

        if (isset($options['caissier_id'])) {
            $query->where('caissier_id', $options['caissier_id']);
        }

        if (isset($options['date_activement_not_null'])) {
            $query->whereNotNull('date_activement');
        }

        if (isset($options['date_activement'])) {
            $query->whereDate('date_activement', $options['date_activement']);
        }

        if (isset($options['date_paiement'])) {
            $query->whereDate('date_paiement', $options['date_paiement']);
        }


        if (isset($options['taxe_id'])) {
            $query->whereHas('contribuableTaxe', function ($q) use ($options) {
                $q->where('taxe_id', $options['taxe_id']);
            });
        }

        if (isset($option['taxes_ids'])) {
            $query->whereHas('contribuableTaxe', function ($q) use ($options) {
                $q->whereIn('taxe_id', $options['taxes_ids']);
            });
        }

        if (isset($options['annee']) && is_numeric($options['annee'])) {
            $query->whereYear('date_activement', $options['annee']);
        }

        if (isset($options['mois']) && is_numeric($options['mois'])) {
            $query->whereMonth('date_activement', $options['mois']);
        }

        if (isset($options['semaine']) && is_numeric($options['semaine'])) {
            $query->whereRaw('WEEK(date_activement, 3) = ?', [$options['semaine']]);
        }

        if (isset($options['mode']) && $options['mode'] == 'get') {
            return $query->get();
        } else {
            $sum = $query->sum('montant');
            return number_format($sum, 0, ' ', ' ');
        }
    }
}
