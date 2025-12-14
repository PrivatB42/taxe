<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu de Paiement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }

        .print-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .retour-button {
            background: #4d4e4eff;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            text-decoration: none;
        }

        .print-button:hover {
            background: #0056b3;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .no-print {
                display: none;
            }

            #print {
                border: none;
                box-shadow: none;
            }
        }
    </style>
</head>

<body style="font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 40px 20px;">

    <!-- Bouton d'impression (masqué à l'impression) -->
    <div class="no-print">
        <button class="print-button" onclick="printer()">
            🖨️ Imprimer le Reçu
        </button>
        <a href="{{ route('contribuables.index') }}" class="retour-button">
            ⬅️ Retour
        </a>
    </div>

    <div style="max-width: 800px; margin: 0 auto; background: white; border: 2px solid #000; padding: 40px;" id="print">

        <!-- En-tête -->
        <div style="text-align: center; margin-bottom: 40px; border-bottom: 3px solid #000; padding-bottom: 20px;">
            <h1 style="margin: 0; font-size: 28px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px;">Reçu de Paiement</h1>
            <p style="margin: 10px 0 0 0; font-size: 12px; color: #333;">{{ $paiement?->reference }}</p>
            <!-- $paiement?->contribuable?->commune?->nom -->
        </div>

        <!-- Informations du contribuable -->
        <div style="margin-bottom: 30px;">
            <h2 style="margin: 0 0 15px 0; font-size: 16px; font-weight: bold; text-transform: uppercase; border-bottom: 2px solid #000; padding-bottom: 5px;">Informations du Contribuable</h2>
            <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
                <tr>
                    <td style="padding: 9px; border: 1px solid #000; font-weight: bold; width: 40%; background: #f9f9f9;">Nom du contribuable</td>
                    <td style="padding: 9px; border: 1px solid #000;">{{ $paiement?->contribuable?->personne?->nom_complet }}</td>
                </tr>
                <tr>
                    <td style="padding: 9px; border: 1px solid #000; font-weight: bold; background: #f9f9f9;">Taxe</td>
                    <td style="padding: 9px; border: 1px solid #000;">{{ $paiement?->taxe?->nom }}</td>
                </tr>
                <tr>
                    <td style="padding: 9px; border: 1px solid #000; font-weight: bold; background: #f9f9f9;">Année de paiement</td>
                    <td style="padding: 9px; border: 1px solid #000;">{{ $paiement?->contribuableTaxe?->exercice?->slug }}</td>
                </tr>
                <tr>
                    <td style="padding: 9px; border: 1px solid #000; font-weight: bold; background: #f9f9f9;">Date de paiement</td>
                    <td style="padding: 9px; border: 1px solid #000;">{{ $paiement?->date_paiement }}</td>
                </tr>
            </table>
        </div>

        <!-- Détails financiers -->
        <div style="margin-bottom: 30px;">
            <h2 style="margin: 0 0 15px 0; font-size: 16px; font-weight: bold; text-transform: uppercase; border-bottom: 2px solid #000; padding-bottom: 5px;">Détails Financiers</h2>
            <table style="width: 100%; border-collapse: collapse; border: 2px solid #000;">
                <thead>
                    <tr>
                        <th style="padding: 9px; border: 1px solid #000; text-align: left; font-weight: bold; background: #e0e0e0;">Description</th>
                        <th style="padding: 9px; border: 1px solid #000; text-align: right; font-weight: bold; background: #e0e0e0;">Montant (FCFA)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="padding: 9px; border: 1px solid #000;">Montant total à payer</td>
                        <td style="padding: 9px; border: 1px solid #000; text-align: right; font-weight: bold;">{{ $paiement?->contribuableTaxe?->montant_a_payer }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 9px; border: 1px solid #000; background: #f9f9f9;">Montant payé</td>
                        <td style="padding: 9px; border: 1px solid #000; text-align: right; font-weight: bold; background: #f9f9f9;">{{ $paiement?->contribuableTaxe?->montant_paye }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 9px; border: 1px solid #000;">Montant restant</td>
                        <td style="padding: 9px; border: 1px solid #000; text-align: right; font-weight: bold;">{{ $paiement?->contribuableTaxe?->montant_a_payer - $paiement?->contribuableTaxe?->montant_paye }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 9px; border: 1px solid #000; background: #f9f9f9;">Montant de paiement</td>
                        <td style="padding: 9px; border: 1px solid #000; text-align: right; font-weight: bold; background: #f9f9f9;">{{ $paiement?->montant }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 9px; border: 1px solid #000;">Montant encaissé</td>
                        <td style="padding: 9px; border: 1px solid #000; text-align: right; font-weight: bold;">{{ $paiement?->montant_encaisse }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 9px; border: 2px solid #000; font-weight: bold; background: #e0e0e0;">Montant rendu</td>
                        <td style="padding: 9px; border: 2px solid #000; text-align: right; font-weight: bold; font-size: 18px; background: #e0e0e0;">{{ $paiement?->montant_rendu }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Signatures -->
        <div style="margin-top: 50px; padding-top: 30px; border-top: 2px solid #000;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <!-- <td style="width: 50%; text-align: center; padding: 20px;">
                        <p style="margin: 0 0 60px 0; font-weight: bold;">Signature du contribuable</p>
                        <div style="border-top: 2px solid #000; width: 200px; margin: 0 auto;"></div>
                    </td> -->
                    <td style="width: 50%; text-align: center; padding: 20px;">
                        <p style="margin: 0 0 60px 0; font-weight: bold;">Cachet et signature du caissier</p>
                        <div style="border-top: 2px solid #000; width: 200px; margin: 0 auto;"></div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Pied de page -->
        <div style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #000; text-align: center;">
            <p style="margin: 0; font-size: 11px; color: #333;">Ce reçu fait foi de paiement • Document généré le 10/12/2025</p>
            <p style="margin: 5px 0 0 0; font-size: 10px; color: #666;">Pour toute réclamation, veuillez conserver ce document</p>
        </div>

    </div>

    <script>
        // Impression automatique au chargement de la page
        // window.addEventListener('load', function() {
        //     // Petit délai pour s'assurer que tout est chargé
        //     setTimeout(function() {
        //         window.print();
        //     }, 500);
        // });

        window.addEventListener('load', function() {
            setTimeout(function() {
                // Sauvegarder le HTML original
                const originalContent = document.body.innerHTML;

                // Récupérer le contenu de la div à imprimer
                const printContent = document.getElementById('print').innerHTML;

                // Remplacer tout le body par le contenu à imprimer
                document.body.innerHTML = printContent;

                // Imprimer
                window.print();

                // Restaurer le contenu original
                document.body.innerHTML = originalContent;

                // Recharger les événements si nécessaire
                //window.location.reload();
            }, 500);
        });


        function printer() {
            setTimeout(function() {
                // Sauvegarder le HTML original
                const originalContent = document.body.innerHTML;

                // Récupérer le contenu de la div à imprimer
                const printContent = document.getElementById('print').innerHTML;

                // Remplacer tout le body par le contenu à imprimer
                document.body.innerHTML = printContent;

                // Imprimer
                window.print();

                // Restaurer le contenu original
                document.body.innerHTML = originalContent;

                // Recharger les événements si nécessaire
                //window.location.reload();
            }, 500);
        }
    </script>

</body>

</html>