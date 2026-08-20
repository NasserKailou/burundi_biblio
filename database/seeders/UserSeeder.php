<?php

namespace Database\Seeders;

use App\Models\Niveau;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Identifiants de demo (mot de passe commun par role, a usage de
     * demonstration uniquement - voir README section "Comptes de demo").
     */
    public const PASSWORD_ADMIN = 'Admin@2026!';

    public const PASSWORD_ENSEIGNANT = 'Enseignant@2026!';

    public const PASSWORD_ELEVE = 'Eleve@2026!';

    public function run(): void
    {
        $roleAdmin = Role::query()->where('libelle', 'admin')->firstOrFail();
        $roleEnseignant = Role::query()->where('libelle', 'enseignant')->firstOrFail();
        $roleEleve = Role::query()->where('libelle', 'eleve')->firstOrFail();

        $niveau = fn (string $libelle) => Niveau::query()->where('libelle', $libelle)->firstOrFail();

        User::query()->updateOrCreate(
            ['identifiant' => 'admin'],
            [
                'nom' => 'Nkurunziza',
                'prenom' => 'Alice',
                'email' => 'admin@bns.local',
                'password' => Hash::make(self::PASSWORD_ADMIN),
                'role_id' => $roleAdmin->id,
                'niveau_id' => null,
                'actif' => true,
            ]
        );

        $enseignants = [
            [
                'identifiant' => 'enseignant1',
                'nom' => 'Ndayishimiye',
                'prenom' => 'Jean',
                'niveau_principal' => '6eme',
                'niveaux_additionnels' => ['5eme', '2nde'],
            ],
            [
                'identifiant' => 'enseignant2',
                'nom' => 'Niyonzima',
                'prenom' => 'Claudine',
                'niveau_principal' => '5eme',
                'niveaux_additionnels' => ['4eme'],
            ],
            [
                'identifiant' => 'enseignant3',
                'nom' => 'Hakizimana',
                'prenom' => 'Eric',
                'niveau_principal' => '3eme',
                'niveaux_additionnels' => ['Terminale'],
            ],
        ];

        foreach ($enseignants as $e) {
            $user = User::query()->updateOrCreate(
                ['identifiant' => $e['identifiant']],
                [
                    'nom' => $e['nom'],
                    'prenom' => $e['prenom'],
                    'email' => $e['identifiant'].'@bns.local',
                    'password' => Hash::make(self::PASSWORD_ENSEIGNANT),
                    'role_id' => $roleEnseignant->id,
                    'niveau_id' => $niveau($e['niveau_principal'])->id,
                    'actif' => true,
                    'peut_publier_commun' => true,
                ]
            );

            $idsAdditionnels = collect($e['niveaux_additionnels'])->map(fn ($l) => $niveau($l)->id);
            $user->niveauxEnseignes()->sync($idsAdditionnels);
        }

        $eleves = [
            ['identifiant' => 'eleve1', 'nom' => 'Irakoze', 'prenom' => 'Divine', 'niveau' => '6eme', 'classe' => '6emeA', 'actif' => true],
            ['identifiant' => 'eleve2', 'nom' => 'Bigirimana', 'prenom' => 'Aime', 'niveau' => '6eme', 'classe' => '6emeB', 'actif' => true],
            ['identifiant' => 'eleve3', 'nom' => 'Nizigiyimana', 'prenom' => 'Chantal', 'niveau' => '5eme', 'classe' => '5emeA', 'actif' => true],
            ['identifiant' => 'eleve4', 'nom' => 'Manirakiza', 'prenom' => 'Fabrice', 'niveau' => '5eme', 'classe' => '5emeA', 'actif' => false],
            ['identifiant' => 'eleve5', 'nom' => 'Ntakirutimana', 'prenom' => 'Grace', 'niveau' => '4eme', 'classe' => '4emeA', 'actif' => true],
            ['identifiant' => 'eleve6', 'nom' => 'Havyarimana', 'prenom' => 'Sonia', 'niveau' => '4eme', 'classe' => '4emeB', 'actif' => true],
            ['identifiant' => 'eleve7', 'nom' => 'Ndikumana', 'prenom' => 'Elvis', 'niveau' => '3eme', 'classe' => '3emeA', 'actif' => true],
            ['identifiant' => 'eleve8', 'nom' => 'Nshimirimana', 'prenom' => 'Belyse', 'niveau' => '3eme', 'classe' => '3emeA', 'actif' => true],
            ['identifiant' => 'eleve9', 'nom' => 'Sindayigaya', 'prenom' => 'Herve', 'niveau' => '2nde', 'classe' => '2ndeA', 'actif' => true],
            ['identifiant' => 'eleve10', 'nom' => 'Ntahonkuriye', 'prenom' => 'Sandra', 'niveau' => '1ere', 'classe' => '1ereA', 'actif' => true],
            ['identifiant' => 'eleve11', 'nom' => 'Bukuru', 'prenom' => 'Patrick', 'niveau' => 'Terminale', 'classe' => 'TermA', 'actif' => true],
            ['identifiant' => 'eleve12', 'nom' => 'Iradukunda', 'prenom' => 'Nadia', 'niveau' => 'Terminale', 'classe' => 'TermA', 'actif' => false],
        ];

        foreach ($eleves as $el) {
            User::query()->updateOrCreate(
                ['identifiant' => $el['identifiant']],
                [
                    'nom' => $el['nom'],
                    'prenom' => $el['prenom'],
                    'email' => null,
                    'password' => Hash::make(self::PASSWORD_ELEVE),
                    'role_id' => $roleEleve->id,
                    'niveau_id' => $niveau($el['niveau'])->id,
                    'classe' => $el['classe'],
                    'actif' => $el['actif'],
                ]
            );
        }
    }
}
