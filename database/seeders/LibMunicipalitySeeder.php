<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\LibMunicipality;
use Illuminate\Support\Facades\DB;

class LibMunicipalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('lib_municipality_type_prices')->truncate();
        DB::table('lib_municipalities')->truncate();

        $typeIds = DB::table('lib_types')->pluck('id')->all();

        $data = [
            // --- REGION IX: ZAMBOANGA PENINSULA ---
            // Zamboanga del Norte
            ['region' => 'Region IX', 'province' => 'Zamboanga del Norte', 'municipality' => 'Dipolog City', 'price' => 500],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Norte', 'municipality' => 'Dapitan City', 'price' => 550],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Norte', 'municipality' => 'Katipunan', 'price' => 450],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Norte', 'municipality' => 'Manukan', 'price' => 450],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Norte', 'municipality' => 'Sindangan', 'price' => 500],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Norte', 'municipality' => 'Liloy', 'price' => 550],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Norte', 'municipality' => 'Labason', 'price' => 550],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Norte', 'municipality' => 'Siocon', 'price' => 600],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Norte', 'municipality' => 'Sirawai', 'price' => 650],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Norte', 'municipality' => 'Sibuco', 'price' => 700],
            // Zamboanga del Sur
            ['region' => 'Region IX', 'province' => 'Zamboanga del Sur', 'municipality' => 'Pagadian City', 'price' => 600],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Sur', 'municipality' => 'Zamboanga City', 'price' => 700],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Sur', 'municipality' => 'Aurora', 'price' => 500],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Sur', 'municipality' => 'Molave', 'price' => 500],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Sur', 'municipality' => 'Mahayag', 'price' => 500],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Sur', 'municipality' => 'Dumingag', 'price' => 550],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Sur', 'municipality' => 'Guipos', 'price' => 500],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Sur', 'municipality' => 'San Miguel', 'price' => 500],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Sur', 'municipality' => 'Tukuran', 'price' => 500],
            ['region' => 'Region IX', 'province' => 'Zamboanga del Sur', 'municipality' => 'Vincenzo A. Sagun', 'price' => 550],
            // Zamboanga Sibugay
            ['region' => 'Region IX', 'province' => 'Zamboanga Sibugay', 'municipality' => 'Ipil', 'price' => 450],
            ['region' => 'Region IX', 'province' => 'Zamboanga Sibugay', 'municipality' => 'Titay', 'price' => 450],
            ['region' => 'Region IX', 'province' => 'Zamboanga Sibugay', 'municipality' => 'Naga', 'price' => 450],
            ['region' => 'Region IX', 'province' => 'Zamboanga Sibugay', 'municipality' => 'Kabasalan', 'price' => 450],
            ['region' => 'Region IX', 'province' => 'Zamboanga Sibugay', 'municipality' => 'Siay', 'price' => 450],
            ['region' => 'Region IX', 'province' => 'Zamboanga Sibugay', 'municipality' => 'Imelda', 'price' => 450],
            ['region' => 'Region IX', 'province' => 'Zamboanga Sibugay', 'municipality' => 'Payao', 'price' => 500],
            ['region' => 'Region IX', 'province' => 'Zamboanga Sibugay', 'municipality' => 'Alicia', 'price' => 500],
            ['region' => 'Region IX', 'province' => 'Zamboanga Sibugay', 'municipality' => 'Olutanga', 'price' => 550],
            ['region' => 'Region IX', 'province' => 'Zamboanga Sibugay', 'municipality' => 'Mabuhay', 'price' => 550],

            // --- REGION X: NORTHERN MINDANAO ---
            // Bukidnon
            ['region' => 'Region X', 'province' => 'Bukidnon', 'municipality' => 'Malaybalay City', 'price' => 800],
            ['region' => 'Region X', 'province' => 'Bukidnon', 'municipality' => 'Valencia City', 'price' => 850],
            ['region' => 'Region X', 'province' => 'Bukidnon', 'municipality' => 'Manolo Fortich', 'price' => 750],
            ['region' => 'Region X', 'province' => 'Bukidnon', 'municipality' => 'Maramag', 'price' => 800],
            ['region' => 'Region X', 'province' => 'Bukidnon', 'municipality' => 'Quezon', 'price' => 850],
            ['region' => 'Region X', 'province' => 'Bukidnon', 'municipality' => 'Don Carlos', 'price' => 800],
            ['region' => 'Region X', 'province' => 'Bukidnon', 'municipality' => 'Lantapan', 'price' => 850],
            ['region' => 'Region X', 'province' => 'Bukidnon', 'municipality' => 'Pangantucan', 'price' => 900],
            ['region' => 'Region X', 'province' => 'Bukidnon', 'municipality' => 'Kibawe', 'price' => 950],
            ['region' => 'Region X', 'province' => 'Bukidnon', 'municipality' => 'Talakag', 'price' => 850],
            // Camiguin
            ['region' => 'Region X', 'province' => 'Camiguin', 'municipality' => 'Mambajao', 'price' => 1200],
            ['region' => 'Region X', 'province' => 'Camiguin', 'municipality' => 'Mahinog', 'price' => 1200],
            ['region' => 'Region X', 'province' => 'Camiguin', 'municipality' => 'Guinsiliban', 'price' => 1200],
            ['region' => 'Region X', 'province' => 'Camiguin', 'municipality' => 'Sagay', 'price' => 1200],
            ['region' => 'Region X', 'province' => 'Camiguin', 'municipality' => 'Catarman', 'price' => 1200],
            // Lanao del Norte
            ['region' => 'Region X', 'province' => 'Lanao del Norte', 'municipality' => 'Iligan City', 'price' => 650],
            ['region' => 'Region X', 'province' => 'Lanao del Norte', 'municipality' => 'Tubod', 'price' => 600],
            ['region' => 'Region X', 'province' => 'Lanao del Norte', 'municipality' => 'Baroy', 'price' => 600],
            ['region' => 'Region X', 'province' => 'Lanao del Norte', 'municipality' => 'Lala', 'price' => 600],
            ['region' => 'Region X', 'province' => 'Lanao del Norte', 'municipality' => 'Kapatagan', 'price' => 600],
            ['region' => 'Region X', 'province' => 'Lanao del Norte', 'municipality' => 'Maigo', 'price' => 600],
            ['region' => 'Region X', 'province' => 'Lanao del Norte', 'municipality' => 'Kolambugan', 'price' => 600],
            ['region' => 'Region X', 'province' => 'Lanao del Norte', 'municipality' => 'Bacolod', 'price' => 600],
            ['region' => 'Region X', 'province' => 'Lanao del Norte', 'municipality' => 'Linamon', 'price' => 600],
            ['region' => 'Region X', 'province' => 'Lanao del Norte', 'municipality' => 'Kauswagan', 'price' => 600],
            // Misamis Occidental
            ['region' => 'Region X', 'province' => 'Misamis Occidental', 'municipality' => 'Ozamiz City', 'price' => 550],
            ['region' => 'Region X', 'province' => 'Misamis Occidental', 'municipality' => 'Oroquieta City', 'price' => 550],
            ['region' => 'Region X', 'province' => 'Misamis Occidental', 'municipality' => 'Tangub City', 'price' => 550],
            ['region' => 'Region X', 'province' => 'Misamis Occidental', 'municipality' => 'Jimenez', 'price' => 500],
            ['region' => 'Region X', 'province' => 'Misamis Occidental', 'municipality' => 'Plaridel', 'price' => 500],
            ['region' => 'Region X', 'province' => 'Misamis Occidental', 'municipality' => 'Calamba', 'price' => 500],
            ['region' => 'Region X', 'province' => 'Misamis Occidental', 'municipality' => 'Lopez Jaena', 'price' => 500],
            ['region' => 'Region X', 'province' => 'Misamis Occidental', 'municipality' => 'Sinacaban', 'price' => 500],
            ['region' => 'Region X', 'province' => 'Misamis Occidental', 'municipality' => 'Tudela', 'price' => 500],
            ['region' => 'Region X', 'province' => 'Misamis Occidental', 'municipality' => 'Clarin', 'price' => 500],
            // Misamis Oriental
            ['region' => 'Region X', 'province' => 'Misamis Oriental', 'municipality' => 'Cagayan de Oro City', 'price' => 900],
            ['region' => 'Region X', 'province' => 'Misamis Oriental', 'municipality' => 'Gingoog City', 'price' => 850],
            ['region' => 'Region X', 'province' => 'Misamis Oriental', 'municipality' => 'El Salvador City', 'price' => 850],
            ['region' => 'Region X', 'province' => 'Misamis Oriental', 'municipality' => 'Tagoloan', 'price' => 800],
            ['region' => 'Region X', 'province' => 'Misamis Oriental', 'municipality' => 'Villanueva', 'price' => 800],
            ['region' => 'Region X', 'province' => 'Misamis Oriental', 'municipality' => 'Jasaan', 'price' => 800],
            ['region' => 'Region X', 'province' => 'Misamis Oriental', 'municipality' => 'Balingasag', 'price' => 800],
            ['region' => 'Region X', 'province' => 'Misamis Oriental', 'municipality' => 'Opol', 'price' => 800],
            ['region' => 'Region X', 'province' => 'Misamis Oriental', 'municipality' => 'Alubijid', 'price' => 800],
            ['region' => 'Region X', 'province' => 'Misamis Oriental', 'municipality' => 'Laguindingan', 'price' => 800],

            // --- REGION XI: DAVAO REGION ---
            // Davao de Oro
            ['region' => 'Region XI', 'province' => 'Davao de Oro', 'municipality' => 'Nabunturan', 'price' => 750],
            ['region' => 'Region XI', 'province' => 'Davao de Oro', 'municipality' => 'Compostela', 'price' => 750],
            ['region' => 'Region XI', 'province' => 'Davao de Oro', 'municipality' => 'Monkayo', 'price' => 800],
            ['region' => 'Region XI', 'province' => 'Davao de Oro', 'municipality' => 'Maragusan', 'price' => 850],
            ['region' => 'Region XI', 'province' => 'Davao de Oro', 'municipality' => 'Pantukan', 'price' => 750],
            ['region' => 'Region XI', 'province' => 'Davao de Oro', 'municipality' => 'Mabini', 'price' => 750],
            ['region' => 'Region XI', 'province' => 'Davao de Oro', 'municipality' => 'Maco', 'price' => 750],
            ['region' => 'Region XI', 'province' => 'Davao de Oro', 'municipality' => 'Mawab', 'price' => 750],
            ['region' => 'Region XI', 'province' => 'Davao de Oro', 'municipality' => 'New Bataan', 'price' => 800],
            ['region' => 'Region XI', 'province' => 'Davao de Oro', 'municipality' => 'Laak', 'price' => 850],
            // Davao del Norte
            ['region' => 'Region XI', 'province' => 'Davao del Norte', 'municipality' => 'Tagum City', 'price' => 800],
            ['region' => 'Region XI', 'province' => 'Davao del Norte', 'municipality' => 'Panabo City', 'price' => 800],
            ['region' => 'Region XI', 'province' => 'Davao del Norte', 'municipality' => 'Samal City', 'price' => 1200],
            ['region' => 'Region XI', 'province' => 'Davao del Norte', 'municipality' => 'Carmen', 'price' => 750],
            ['region' => 'Region XI', 'province' => 'Davao del Norte', 'municipality' => 'Sto. Tomas', 'price' => 750],
            ['region' => 'Region XI', 'province' => 'Davao del Norte', 'municipality' => 'Kapalong', 'price' => 800],
            ['region' => 'Region XI', 'province' => 'Davao del Norte', 'municipality' => 'Asuncion', 'price' => 800],
            ['region' => 'Region XI', 'province' => 'Davao del Norte', 'municipality' => 'New Corella', 'price' => 800],
            ['region' => 'Region XI', 'province' => 'Davao del Norte', 'municipality' => 'Talaingod', 'price' => 900],
            ['region' => 'Region XI', 'province' => 'Davao del Norte', 'municipality' => 'San Isidro', 'price' => 800],
            // Davao del Sur
            ['region' => 'Region XI', 'province' => 'Davao del Sur', 'municipality' => 'Davao City', 'price' => 1000],
            ['region' => 'Region XI', 'province' => 'Davao del Sur', 'municipality' => 'Digos City', 'price' => 850],
            ['region' => 'Region XI', 'province' => 'Davao del Sur', 'municipality' => 'Sta. Cruz', 'price' => 800],
            ['region' => 'Region XI', 'province' => 'Davao del Sur', 'municipality' => 'Bansalan', 'price' => 850],
            ['region' => 'Region XI', 'province' => 'Davao del Sur', 'municipality' => 'Matanao', 'price' => 850],
            ['region' => 'Region XI', 'province' => 'Davao del Sur', 'municipality' => 'Magsaysay', 'price' => 850],
            ['region' => 'Region XI', 'province' => 'Davao del Sur', 'municipality' => 'Hagonoy', 'price' => 800],
            ['region' => 'Region XI', 'province' => 'Davao del Sur', 'municipality' => 'Padada', 'price' => 800],
            ['region' => 'Region XI', 'province' => 'Davao del Sur', 'municipality' => 'Sulop', 'price' => 850],
            ['region' => 'Region XI', 'province' => 'Davao del Sur', 'municipality' => 'Malalag', 'price' => 850],
            // Davao Occidental
            ['region' => 'Region XI', 'province' => 'Davao Occidental', 'municipality' => 'Malita', 'price' => 950],
            ['region' => 'Region XI', 'province' => 'Davao Occidental', 'municipality' => 'Sta. Maria', 'price' => 900],
            ['region' => 'Region XI', 'province' => 'Davao Occidental', 'municipality' => 'Don Marcelino', 'price' => 1000],
            ['region' => 'Region XI', 'province' => 'Davao Occidental', 'municipality' => 'Jose Abad Santos', 'price' => 1100],
            ['region' => 'Region XI', 'province' => 'Davao Occidental', 'municipality' => 'Sarangani (Davao Occ.)', 'price' => 1500],
            // Davao Oriental
            ['region' => 'Region XI', 'province' => 'Davao Oriental', 'municipality' => 'Mati City', 'price' => 900],
            ['region' => 'Region XI', 'province' => 'Davao Oriental', 'municipality' => 'Lupon', 'price' => 850],
            ['region' => 'Region XI', 'province' => 'Davao Oriental', 'municipality' => 'Banaybanay', 'price' => 850],
            ['region' => 'Region XI', 'province' => 'Davao Oriental', 'municipality' => 'San Isidro (Davao Or.)', 'price' => 850],
            ['region' => 'Region XI', 'province' => 'Davao Oriental', 'municipality' => 'Governor Generoso', 'price' => 950],
            ['region' => 'Region XI', 'province' => 'Davao Oriental', 'municipality' => 'Tarragona', 'price' => 950],
            ['region' => 'Region XI', 'province' => 'Davao Oriental', 'municipality' => 'Manay', 'price' => 1000],
            ['region' => 'Region XI', 'province' => 'Davao Oriental', 'municipality' => 'Caraga', 'price' => 1000],
            ['region' => 'Region XI', 'province' => 'Davao Oriental', 'municipality' => 'Baganga', 'price' => 1100],
            ['region' => 'Region XI', 'province' => 'Davao Oriental', 'municipality' => 'Cateel', 'price' => 1200],

            // --- REGION XII: SOCCSKSARGEN ---
            // Cotabato (North)
            ['region' => 'Region XII', 'province' => 'Cotabato', 'municipality' => 'Kidapawan City', 'price' => 700],
            ['region' => 'Region XII', 'province' => 'Cotabato', 'municipality' => 'Makilala', 'price' => 700],
            ['region' => 'Region XII', 'province' => 'Cotabato', 'municipality' => 'Mlang', 'price' => 750],
            ['region' => 'Region XII', 'province' => 'Cotabato', 'municipality' => 'Tulunan', 'price' => 750],
            ['region' => 'Region XII', 'province' => 'Cotabato', 'municipality' => 'Kabacan', 'price' => 700],
            ['region' => 'Region XII', 'province' => 'Cotabato', 'municipality' => 'Carmen (Cotabato)', 'price' => 750],
            ['region' => 'Region XII', 'province' => 'Cotabato', 'municipality' => 'Matalam', 'price' => 750],
            ['region' => 'Region XII', 'province' => 'Cotabato', 'municipality' => 'Pigcawayan', 'price' => 700],
            ['region' => 'Region XII', 'province' => 'Cotabato', 'municipality' => 'Pikit', 'price' => 750],
            ['region' => 'Region XII', 'province' => 'Cotabato', 'municipality' => 'Libungan', 'price' => 700],
            // Sarangani
            ['region' => 'Region XII', 'province' => 'Sarangani', 'municipality' => 'Alabel', 'price' => 750],
            ['region' => 'Region XII', 'province' => 'Sarangani', 'municipality' => 'Malungon', 'price' => 750],
            ['region' => 'Region XII', 'province' => 'Sarangani', 'municipality' => 'Glan', 'price' => 850],
            ['region' => 'Region XII', 'province' => 'Sarangani', 'municipality' => 'Maasim', 'price' => 800],
            ['region' => 'Region XII', 'province' => 'Sarangani', 'municipality' => 'Kiamba', 'price' => 850],
            ['region' => 'Region XII', 'province' => 'Sarangani', 'municipality' => 'Maitum', 'price' => 900],
            ['region' => 'Region XII', 'province' => 'Sarangani', 'municipality' => 'Malapatan', 'price' => 800],
            // South Cotabato
            ['region' => 'Region XII', 'province' => 'South Cotabato', 'municipality' => 'General Santos City', 'price' => 850],
            ['region' => 'Region XII', 'province' => 'South Cotabato', 'municipality' => 'Koronadal City', 'price' => 750],
            ['region' => 'Region XII', 'province' => 'South Cotabato', 'municipality' => 'Polomolok', 'price' => 750],
            ['region' => 'Region XII', 'province' => 'South Cotabato', 'municipality' => 'Tupi', 'price' => 750],
            ['region' => 'Region XII', 'province' => 'South Cotabato', 'municipality' => 'Surallah', 'price' => 800],
            ['region' => 'Region XII', 'province' => 'South Cotabato', 'municipality' => 'Banga', 'price' => 800],
            ['region' => 'Region XII', 'province' => 'South Cotabato', 'municipality' => 'Norala', 'price' => 800],
            ['region' => 'Region XII', 'province' => 'South Cotabato', 'municipality' => 'Sto. Niño', 'price' => 800],
            ['region' => 'Region XII', 'province' => 'South Cotabato', 'municipality' => 'Lake Sebu', 'price' => 1000],
            ['region' => 'Region XII', 'province' => 'South Cotabato', 'municipality' => 'T\'Boli', 'price' => 950],
            // Sultan Kudarat
            ['region' => 'Region XII', 'province' => 'Sultan Kudarat', 'municipality' => 'Isulan', 'price' => 650],
            ['region' => 'Region XII', 'province' => 'Sultan Kudarat', 'municipality' => 'Tacurong City', 'price' => 700],
            ['region' => 'Region XII', 'province' => 'Sultan Kudarat', 'municipality' => 'Esperanza', 'price' => 700],
            ['region' => 'Region XII', 'province' => 'Sultan Kudarat', 'municipality' => 'Bagumbayan', 'price' => 750],
            ['region' => 'Region XII', 'province' => 'Sultan Kudarat', 'municipality' => 'Senator Ninoy Aquino', 'price' => 850],
            ['region' => 'Region XII', 'province' => 'Sultan Kudarat', 'municipality' => 'Lebak', 'price' => 900],
            ['region' => 'Region XII', 'province' => 'Sultan Kudarat', 'municipality' => 'Kalamansig', 'price' => 950],
            ['region' => 'Region XII', 'province' => 'Sultan Kudarat', 'municipality' => 'Palimbang', 'price' => 1000],
            ['region' => 'Region XII', 'province' => 'Sultan Kudarat', 'municipality' => 'Lambayong', 'price' => 700],
            ['region' => 'Region XII', 'province' => 'Sultan Kudarat', 'municipality' => 'Lutayan', 'price' => 700],

            // --- REGION XIII: CARAGA ---
            // Agusan del Norte
            ['region' => 'Region XIII', 'province' => 'Agusan del Norte', 'municipality' => 'Butuan City', 'price' => 800],
            ['region' => 'Region XIII', 'province' => 'Agusan del Norte', 'municipality' => 'Cabadbaran City', 'price' => 750],
            ['region' => 'Region XIII', 'province' => 'Agusan del Norte', 'municipality' => 'Buenavista', 'price' => 700],
            ['region' => 'Region XIII', 'province' => 'Agusan del Norte', 'municipality' => 'Nasipit', 'price' => 700],
            ['region' => 'Region XIII', 'province' => 'Agusan del Norte', 'municipality' => 'Carmen (Agusan N.)', 'price' => 700],
            ['region' => 'Region XIII', 'province' => 'Agusan del Norte', 'municipality' => 'Magallanes', 'price' => 700],
            ['region' => 'Region XIII', 'province' => 'Agusan del Norte', 'municipality' => 'Remedios T. Romualdez', 'price' => 700],
            ['region' => 'Region XIII', 'province' => 'Agusan del Norte', 'municipality' => 'Tubay', 'price' => 750],
            ['region' => 'Region XIII', 'province' => 'Agusan del Norte', 'municipality' => 'Santiago', 'price' => 750],
            ['region' => 'Region XIII', 'province' => 'Agusan del Norte', 'municipality' => 'Jabonga', 'price' => 800],
            ['region' => 'Region XIII', 'province' => 'Agusan del Norte', 'municipality' => 'Kitcharao', 'price' => 800],
            // Agusan del Sur
            ['region' => 'Region XIII', 'province' => 'Agusan del Sur', 'municipality' => 'Prosperidad', 'price' => 700],
            ['region' => 'Region XIII', 'province' => 'Agusan del Sur', 'municipality' => 'Bayugan City', 'price' => 750],
            ['region' => 'Region XIII', 'province' => 'Agusan del Sur', 'municipality' => 'San Francisco', 'price' => 750],
            ['region' => 'Region XIII', 'province' => 'Agusan del Sur', 'municipality' => 'Bunawan', 'price' => 800],
            ['region' => 'Region XIII', 'province' => 'Agusan del Sur', 'municipality' => 'Trento', 'price' => 850],
            ['region' => 'Region XIII', 'province' => 'Agusan del Sur', 'municipality' => 'Rosario', 'price' => 800],
            ['region' => 'Region XIII', 'province' => 'Agusan del Sur', 'municipality' => 'Esperanza (Agusan S.)', 'price' => 750],
            ['region' => 'Region XIII', 'province' => 'Agusan del Sur', 'municipality' => 'Talacogon', 'price' => 800],
            ['region' => 'Region XIII', 'province' => 'Agusan del Sur', 'municipality' => 'La Paz', 'price' => 850],
            ['region' => 'Region XIII', 'province' => 'Agusan del Sur', 'municipality' => 'Loreto', 'price' => 950],
            // Dinagat Islands
            ['region' => 'Region XIII', 'province' => 'Dinagat Islands', 'municipality' => 'San Jose', 'price' => 1100],
            ['region' => 'Region XIII', 'province' => 'Dinagat Islands', 'municipality' => 'Basilisa', 'price' => 1200],
            ['region' => 'Region XIII', 'province' => 'Dinagat Islands', 'municipality' => 'Cagdianao', 'price' => 1200],
            ['region' => 'Region XIII', 'province' => 'Dinagat Islands', 'municipality' => 'Dinagat', 'price' => 1200],
            ['region' => 'Region XIII', 'province' => 'Dinagat Islands', 'municipality' => 'Libjo', 'price' => 1200],
            ['region' => 'Region XIII', 'province' => 'Dinagat Islands', 'municipality' => 'Loreto (Dinagat)', 'price' => 1300],
            ['region' => 'Region XIII', 'province' => 'Dinagat Islands', 'municipality' => 'Tubajon', 'price' => 1300],
            // Surigao del Norte
            ['region' => 'Region XIII', 'province' => 'Surigao del Norte', 'municipality' => 'Surigao City', 'price' => 900],
            ['region' => 'Region XIII', 'province' => 'Surigao del Norte', 'municipality' => 'Placer', 'price' => 850],
            ['region' => 'Region XIII', 'province' => 'Surigao del Norte', 'municipality' => 'Mainit', 'price' => 900],
            ['region' => 'Region XIII', 'province' => 'Surigao del Norte', 'municipality' => 'Claver', 'price' => 950],
            ['region' => 'Region XIII', 'province' => 'Surigao del Norte', 'municipality' => 'Dapa (Siargao)', 'price' => 1500],
            ['region' => 'Region XIII', 'province' => 'Surigao del Norte', 'municipality' => 'General Luna (Siargao)', 'price' => 1600],
            ['region' => 'Region XIII', 'province' => 'Surigao del Norte', 'municipality' => 'Del Carmen (Siargao)', 'price' => 1500],
            ['region' => 'Region XIII', 'province' => 'Surigao del Norte', 'municipality' => 'Pilar (Siargao)', 'price' => 1500],
            ['region' => 'Region XIII', 'province' => 'Surigao del Norte', 'municipality' => 'San Isidro (Siargao)', 'price' => 1500],
            ['region' => 'Region XIII', 'province' => 'Surigao del Norte', 'municipality' => 'Socorro (Siargao)', 'price' => 1800],
            // Surigao del Sur
            ['region' => 'Region XIII', 'province' => 'Surigao del Sur', 'municipality' => 'Tandag City', 'price' => 850],
            ['region' => 'Region XIII', 'province' => 'Surigao del Sur', 'municipality' => 'Bislig City', 'price' => 950],
            ['region' => 'Region XIII', 'province' => 'Surigao del Sur', 'municipality' => 'Cantilan', 'price' => 900],
            ['region' => 'Region XIII', 'province' => 'Surigao del Sur', 'municipality' => 'Madrid', 'price' => 900],
            ['region' => 'Region XIII', 'province' => 'Surigao del Sur', 'municipality' => 'Carmen (Surigao S.)', 'price' => 900],
            ['region' => 'Region XIII', 'province' => 'Surigao del Sur', 'municipality' => 'Lanuza', 'price' => 900],
            ['region' => 'Region XIII', 'province' => 'Surigao del Sur', 'municipality' => 'Cortes', 'price' => 850],
            ['region' => 'Region XIII', 'province' => 'Surigao del Sur', 'municipality' => 'San Agustin', 'price' => 950],
            ['region' => 'Region XIII', 'province' => 'Surigao del Sur', 'municipality' => 'Marihatag', 'price' => 950],
            ['region' => 'Region XIII', 'province' => 'Surigao del Sur', 'municipality' => 'Cagwait', 'price' => 900],

            // --- BARMM: BANGSAMORO ---
            // Basilan
            ['region' => 'BARMM', 'province' => 'Basilan', 'municipality' => 'Isabela City', 'price' => 1300],
            ['region' => 'BARMM', 'province' => 'Basilan', 'municipality' => 'Lamitan City', 'price' => 1300],
            ['region' => 'BARMM', 'province' => 'Basilan', 'municipality' => 'Maluso', 'price' => 1400],
            ['region' => 'BARMM', 'province' => 'Basilan', 'municipality' => 'Lantawan', 'price' => 1400],
            ['region' => 'BARMM', 'province' => 'Basilan', 'municipality' => 'Sumisip', 'price' => 1500],
            ['region' => 'BARMM', 'province' => 'Basilan', 'municipality' => 'Tipo-Tipo', 'price' => 1500],
            // Lanao del Sur
            ['region' => 'BARMM', 'province' => 'Lanao del Sur', 'municipality' => 'Marawi City', 'price' => 1200],
            ['region' => 'BARMM', 'province' => 'Lanao del Sur', 'municipality' => 'Balindong', 'price' => 1100],
            ['region' => 'BARMM', 'province' => 'Lanao del Sur', 'municipality' => 'Tugaya', 'price' => 1100],
            ['region' => 'BARMM', 'province' => 'Lanao del Sur', 'municipality' => 'Bacolod-Kalawi', 'price' => 1100],
            ['region' => 'BARMM', 'province' => 'Lanao del Sur', 'municipality' => 'Madamba', 'price' => 1200],
            ['region' => 'BARMM', 'province' => 'Lanao del Sur', 'municipality' => 'Ganassi', 'price' => 1200],
            ['region' => 'BARMM', 'province' => 'Lanao del Sur', 'municipality' => 'Malabang', 'price' => 1100],
            ['region' => 'BARMM', 'province' => 'Lanao del Sur', 'municipality' => 'Wao', 'price' => 1000],
            ['region' => 'BARMM', 'province' => 'Lanao del Sur', 'municipality' => 'Saguaran', 'price' => 1100],
            ['region' => 'BARMM', 'province' => 'Lanao del Sur', 'municipality' => 'Piagapo', 'price' => 1200],
            // Maguindanao del Norte
            ['region' => 'BARMM', 'province' => 'Maguindanao del Norte', 'municipality' => 'Cotabato City', 'price' => 800],
            ['region' => 'BARMM', 'province' => 'Maguindanao del Norte', 'municipality' => 'Datu Odin Sinsuat', 'price' => 850],
            ['region' => 'BARMM', 'province' => 'Maguindanao del Norte', 'municipality' => 'Sultan Kudarat (Maguindanao)', 'price' => 850],
            ['region' => 'BARMM', 'province' => 'Maguindanao del Norte', 'municipality' => 'Upi', 'price' => 900],
            ['region' => 'BARMM', 'province' => 'Maguindanao del Norte', 'municipality' => 'Parang', 'price' => 850],
            ['region' => 'BARMM', 'province' => 'Maguindanao del Norte', 'municipality' => 'Matanog', 'price' => 900],
            ['region' => 'BARMM', 'province' => 'Maguindanao del Norte', 'municipality' => 'Barira', 'price' => 900],
            ['region' => 'BARMM', 'province' => 'Maguindanao del Norte', 'municipality' => 'Buldon', 'price' => 900],
            // Maguindanao del Sur
            ['region' => 'BARMM', 'province' => 'Maguindanao del Sur', 'municipality' => 'Buluan', 'price' => 850],
            ['region' => 'BARMM', 'province' => 'Maguindanao del Sur', 'municipality' => 'Datu Paglas', 'price' => 900],
            ['region' => 'BARMM', 'province' => 'Maguindanao del Sur', 'municipality' => 'Shariff Aguak', 'price' => 900],
            ['region' => 'BARMM', 'province' => 'Maguindanao del Sur', 'municipality' => 'Mamasapano', 'price' => 950],
            ['region' => 'BARMM', 'province' => 'Maguindanao del Sur', 'municipality' => 'Ampatuan', 'price' => 900],
            ['region' => 'BARMM', 'province' => 'Maguindanao del Sur', 'municipality' => 'Esperanza (Maguindanao)', 'price' => 900],
            ['region' => 'BARMM', 'province' => 'Maguindanao del Sur', 'municipality' => 'South Upi', 'price' => 1000],
            ['region' => 'BARMM', 'province' => 'Maguindanao del Sur', 'municipality' => 'Paglat', 'price' => 900],
            // Sulu
            ['region' => 'BARMM', 'province' => 'Sulu', 'municipality' => 'Jolo', 'price' => 1500],
            ['region' => 'BARMM', 'province' => 'Sulu', 'municipality' => 'Indanan', 'price' => 1500],
            ['region' => 'BARMM', 'province' => 'Sulu', 'municipality' => 'Patikul', 'price' => 1500],
            ['region' => 'BARMM', 'province' => 'Sulu', 'municipality' => 'Siasi', 'price' => 1800],
            ['region' => 'BARMM', 'province' => 'Sulu', 'municipality' => 'Maimbung', 'price' => 1600],
            ['region' => 'BARMM', 'province' => 'Sulu', 'municipality' => 'Parang (Sulu)', 'price' => 1600],
            ['region' => 'BARMM', 'province' => 'Sulu', 'municipality' => 'Talipao', 'price' => 1600],
            ['region' => 'BARMM', 'province' => 'Sulu', 'municipality' => 'Pangutaran', 'price' => 2000],
            // Tawi-Tawi
            ['region' => 'BARMM', 'province' => 'Tawi-Tawi', 'municipality' => 'Bongao', 'price' => 1600],
            ['region' => 'BARMM', 'province' => 'Tawi-Tawi', 'municipality' => 'Languyan', 'price' => 1800],
            ['region' => 'BARMM', 'province' => 'Tawi-Tawi', 'municipality' => 'Panglima Sugala', 'price' => 1700],
            ['region' => 'BARMM', 'province' => 'Tawi-Tawi', 'municipality' => 'Sapa-Sapa', 'price' => 1700],
            ['region' => 'BARMM', 'province' => 'Tawi-Tawi', 'municipality' => 'Simunul', 'price' => 1900],
            ['region' => 'BARMM', 'province' => 'Tawi-Tawi', 'municipality' => 'Sitangkai', 'price' => 2500],
            ['region' => 'BARMM', 'province' => 'Tawi-Tawi', 'municipality' => 'Turtle Islands', 'price' => 3000],
        ];

        foreach ($data as $item) {
            $price = $item['price'];
            unset($item['price']);

            $municipality = LibMunicipality::create($item);

            if (count($typeIds) > 0) {
                $rows = [];
                foreach ($typeIds as $typeId) {
                    $rows[] = [
                        'lib_municipality_id' => $municipality->id,
                        'lib_type_id' => $typeId,
                        'price' => $price,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                DB::table('lib_municipality_type_prices')->insert($rows);
            }
        }
    }
}
