<?php

namespace Database\Seeders;

use App\Models\Consultation;
use App\Models\Favori;
use App\Models\Manuel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ConsultationSeeder extends Seeder
{
    public function run(): void
    {
        $eleves = User::query()->whereHas('role', fn ($q) => $q->where('libelle', 'eleve'))
            ->where('actif', true)
            ->get();

        $manuelsPublies = Manuel::query()->publies()->with('niveaux')->get();

        foreach ($eleves as $eleve) {
            $manuelsVisibles = $manuelsPublies->filter(function (Manuel $manuel) use ($eleve) {
                return $manuel->est_commun || $manuel->niveaux->pluck('id')->contains($eleve->niveau_id);
            });

            if ($manuelsVisibles->isEmpty()) {
                continue;
            }

            $nbConsultations = random_int(3, 12);

            for ($i = 0; $i < $nbConsultations; $i++) {
                $manuel = $manuelsVisibles->random();
                $date = Carbon::now()->subDays(random_int(0, 59))->subMinutes(random_int(0, 1439));

                Consultation::query()->create([
                    'user_id' => $eleve->id,
                    'manuel_id' => $manuel->id,
                    'date_ouverture' => $date,
                    'duree_secondes' => random_int(45, 2100),
                    'derniere_page' => random_int(1, 40),
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);
            }

            // Quelques favoris realistes.
            foreach ($manuelsVisibles->random(min(2, $manuelsVisibles->count())) as $manuelFavori) {
                Favori::query()->firstOrCreate([
                    'user_id' => $eleve->id,
                    'manuel_id' => is_object($manuelFavori) ? $manuelFavori->id : $manuelFavori,
                ]);
            }
        }
    }
}
