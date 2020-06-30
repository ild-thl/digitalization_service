<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ElmoKeysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $keys = [
            "Modul_Name_Deutsch",
            "Modul_Name_Englisch",
            "Modul_Identifier",
            "Modul_subjectArea",
            "Modul_iscedCode",
            "Modul_Url",
            "Modul_Beschreibung_Deutsch",
            "Modul_Beschreibung_Englisch",
            "Modul_Beschreibung_HTML_Deutsch",
            "Modul_Beschreibung_HTML_Englisch",
            "Kurs_Identifier",
            "Kurs_Name_Deutsch",
            "Kurs_Name_Englisch",
            "Kurs_subjectArea",
            "Kurs_iscedCode",
            "Kurs_Url",
            "Kurs_Beschreibung_Deutsch",
            "Kurs_Beschreibung_Englisch",
            "Kurs_Beschreibung_HTML_Deutsch",
            "Kurs_Beschreibung_HTML_Englisch",
            "Modul_ECTS_Punkte",
            "Modul_Niveau",
            "Modul_Sprache",
            "Kurs_ECTS_Punkte",
            "Kurs_Sprache",
            "Kurs_Arbeitsaufwand_Stunden",
        ];

        foreach($keys as $key) {
            DB::table('elmo_keys')->insert([
                'title' => $key,
                'created_at' => date("Y-m-d H:i:s")
            ]);
        }
    }
}
