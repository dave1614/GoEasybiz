<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        \DB::table('countries')->delete();

        \DB::table('countries')->insert(array(
            0 =>
            array(
                'id' => 1,
                'name' => 'Andorra',
                'code' => 'ad',
                'phone_code' => '+376',
            ),
            1 =>
            array(
                'id' => 2,
                'name' => 'United Arab Emirates',
                'code' => 'ae',
                'phone_code' => '+971',
            ),
            2 =>
            array(
                'id' => 3,
                'name' => 'Afghanistan',
                'code' => 'af',
                'phone_code' => '+93',
            ),
            3 =>
            array(
                'id' => 4,
                'name' => 'Antigua and Barbuda',
                'code' => 'ag',
                'phone_code' => '+1268',
            ),
            4 =>
            array(
                'id' => 5,
                'name' => 'Anguilla',
                'code' => 'ai',
                'phone_code' => '+1264',
            ),
            5 =>
            array(
                'id' => 6,
                'name' => 'Albania',
                'code' => 'al',
                'phone_code' => '+355',
            ),
            6 =>
            array(
                'id' => 7,
                'name' => 'Armenia',
                'code' => 'am',
                'phone_code' => '+374',
            ),
            7 =>
            array(
                'id' => 8,
                'name' => 'Netherlands Antilles',
                'code' => 'an',
                'phone_code' => '+599',
            ),
            8 =>
            array(
                'id' => 9,
                'name' => 'Angola',
                'code' => 'ao',
                'phone_code' => '+244',
            ),
            9 =>
            array(
                'id' => 10,
                'name' => 'Argentina',
                'code' => 'ar',
                'phone_code' => '+54',
            ),
            10 =>
            array(
                'id' => 11,
                'name' => 'Austria',
                'code' => 'at',
                'phone_code' => '+43',
            ),
            11 =>
            array(
                'id' => 12,
                'name' => 'Australia',
                'code' => 'au',
                'phone_code' => '+61',
            ),
            12 =>
            array(
                'id' => 13,
                'name' => 'Aruba',
                'code' => 'aw',
                'phone_code' => '+297',
            ),
            13 =>
            array(
                'id' => 14,
                'name' => 'Azerbaijan',
                'code' => 'az',
                'phone_code' => '+994',
            ),
            14 =>
            array(
                'id' => 15,
                'name' => 'Bosnia and Herzegovina',
                'code' => 'ba',
                'phone_code' => '+387',
            ),
            15 =>
            array(
                'id' => 16,
                'name' => 'Barbados',
                'code' => 'bb',
                'phone_code' => '+1246',
            ),
            16 =>
            array(
                'id' => 17,
                'name' => 'Bangladesh',
                'code' => 'bd',
                'phone_code' => '+880',
            ),
            17 =>
            array(
                'id' => 18,
                'name' => 'Belgium',
                'code' => 'be',
                'phone_code' => '+32',
            ),
            18 =>
            array(
                'id' => 19,
                'name' => 'Burkina Faso',
                'code' => 'bf',
                'phone_code' => '+226',
            ),
            19 =>
            array(
                'id' => 20,
                'name' => 'Bulgaria',
                'code' => 'bg',
                'phone_code' => '+359',
            ),
            20 =>
            array(
                'id' => 21,
                'name' => 'Bahrain',
                'code' => 'bh',
                'phone_code' => '+973',
            ),
            21 =>
            array(
                'id' => 22,
                'name' => 'Burundi',
                'code' => 'bi',
                'phone_code' => '+257',
            ),
            22 =>
            array(
                'id' => 23,
                'name' => 'Benin',
                'code' => 'bj',
                'phone_code' => '+229',
            ),
            23 =>
            array(
                'id' => 24,
                'name' => 'Bermuda',
                'code' => 'bm',
                'phone_code' => '+1441',
            ),
            24 =>
            array(
                'id' => 25,
                'name' => 'Brunei Darussalam',
                'code' => 'bn',
                'phone_code' => '+673',
            ),
            25 =>
            array(
                'id' => 26,
                'name' => 'Bolivia',
                'code' => 'bo',
                'phone_code' => '+591',
            ),
            26 =>
            array(
                'id' => 27,
                'name' => 'Brazil',
                'code' => 'br',
                'phone_code' => '+55',
            ),
            27 =>
            array(
                'id' => 28,
                'name' => 'Bahamas',
                'code' => 'bs',
                'phone_code' => '+1242',
            ),
            28 =>
            array(
                'id' => 29,
                'name' => 'Bhutan',
                'code' => 'bt',
                'phone_code' => '+975',
            ),
            29 =>
            array(
                'id' => 30,
                'name' => 'Botswana',
                'code' => 'bw',
                'phone_code' => '+267',
            ),
            30 =>
            array(
                'id' => 31,
                'name' => 'Belarus',
                'code' => 'by',
                'phone_code' => '+375',
            ),
            31 =>
            array(
                'id' => 32,
                'name' => 'Belize',
                'code' => 'bz',
                'phone_code' => '+501',
            ),
            32 =>
            array(
                'id' => 33,
                'name' => 'Canada',
                'code' => 'ca',
                'phone_code' => '+1',
            ),
            33 =>
            array(
                'id' => 34,
                'name' => 'Cocos (Keeling) Islands',
                'code' => 'cc',
                'phone_code' => '+61',
            ),
            34 =>
            array(
                'id' => 35,
                'name' => 'Democratic Republic of the Congo',
                'code' => 'cd',
                'phone_code' => '+243',
            ),
            35 =>
            array(
                'id' => 36,
                'name' => 'Central African Republic',
                'code' => 'cf',
                'phone_code' => '+236',
            ),
            36 =>
            array(
                'id' => 37,
                'name' => 'Congo',
                'code' => 'cg',
                'phone_code' => '+242',
            ),
            37 =>
            array(
                'id' => 38,
                'name' => 'Switzerland',
                'code' => 'ch',
                'phone_code' => '+41',
            ),
            38 =>
            array(
                'id' => 39,
                'name' => 'Cote D\'Ivoire (Ivory Coast)',
                'code' => 'ci',
                'phone_code' => '+225',
            ),
            39 =>
            array(
                'id' => 40,
                'name' => 'Cook Islands',
                'code' => 'ck',
                'phone_code' => '+682',
            ),
            40 =>
            array(
                'id' => 41,
                'name' => 'Chile',
                'code' => 'cl',
                'phone_code' => '+56',
            ),
            41 =>
            array(
                'id' => 42,
                'name' => 'Cameroon',
                'code' => 'cm',
                'phone_code' => '+237',
            ),
            42 =>
            array(
                'id' => 43,
                'name' => 'China',
                'code' => 'cn',
                'phone_code' => '+86',
            ),
            43 =>
            array(
                'id' => 44,
                'name' => 'Colombia',
                'code' => 'co',
                'phone_code' => '+57',
            ),
            44 =>
            array(
                'id' => 45,
                'name' => 'Costa Rica',
                'code' => 'cr',
                'phone_code' => '+506',
            ),
            45 =>
            array(
                'id' => 46,
                'name' => 'Cuba',
                'code' => 'cu',
                'phone_code' => '+53',
            ),
            46 =>
            array(
                'id' => 47,
                'name' => 'Cape Verde',
                'code' => 'cv',
                'phone_code' => '+238',
            ),
            47 =>
            array(
                'id' => 48,
                'name' => 'Christmas Island',
                'code' => 'cx',
                'phone_code' => '+61',
            ),
            48 =>
            array(
                'id' => 49,
                'name' => 'Cyprus',
                'code' => 'cy',
                'phone_code' => '+357',
            ),
            49 =>
            array(
                'id' => 50,
                'name' => 'Czech Republic',
                'code' => 'cz',
                'phone_code' => '+420',
            ),
            50 =>
            array(
                'id' => 51,
                'name' => 'Germany',
                'code' => 'de',
                'phone_code' => '+49',
            ),
            51 =>
            array(
                'id' => 52,
                'name' => 'Djibouti',
                'code' => 'dj',
                'phone_code' => '+253',
            ),
            52 =>
            array(
                'id' => 53,
                'name' => 'Denmark',
                'code' => 'dk',
                'phone_code' => '+45',
            ),
            53 =>
            array(
                'id' => 54,
                'name' => 'Dominica',
                'code' => 'dm',
                'phone_code' => '+1767',
            ),
            54 =>
            array(
                'id' => 55,
                'name' => 'Dominican Republic',
                'code' => 'do',
                'phone_code' => '+1809',
            ),
            55 =>
            array(
                'id' => 56,
                'name' => 'Algeria',
                'code' => 'dz',
                'phone_code' => '+213',
            ),
            56 =>
            array(
                'id' => 57,
                'name' => 'Ecuador',
                'code' => 'ec',
                'phone_code' => '+593',
            ),
            57 =>
            array(
                'id' => 58,
                'name' => 'Estonia',
                'code' => 'ee',
                'phone_code' => '+372',
            ),
            58 =>
            array(
                'id' => 59,
                'name' => 'Egypt',
                'code' => 'eg',
                'phone_code' => '+20',
            ),
            59 =>
            array(
                'id' => 60,
                'name' => 'Western Sahara',
                'code' => 'eh',
                'phone_code' => '+2125288',
            ),
            60 =>
            array(
                'id' => 61,
                'name' => 'Eritrea',
                'code' => 'er',
                'phone_code' => '+291',
            ),
            61 =>
            array(
                'id' => 62,
                'name' => 'Spain',
                'code' => 'es',
                'phone_code' => '+34',
            ),
            62 =>
            array(
                'id' => 63,
                'name' => 'Ethiopia',
                'code' => 'et',
                'phone_code' => '+251',
            ),
            63 =>
            array(
                'id' => 64,
                'name' => 'Finland',
                'code' => 'fi',
                'phone_code' => '+358',
            ),
            64 =>
            array(
                'id' => 65,
                'name' => 'Fiji',
                'code' => 'fj',
                'phone_code' => '+679',
            ),
            65 =>
            array(
                'id' => 66,
                'name' => 'Falkland Islands (Malvinas)',
                'code' => 'fk',
                'phone_code' => '+500',
            ),
            66 =>
            array(
                'id' => 67,
                'name' => 'Federated States of Micronesia',
                'code' => 'fm',
                'phone_code' => '+691',
            ),
            67 =>
            array(
                'id' => 68,
                'name' => 'Faroe Islands',
                'code' => 'fo',
                'phone_code' => '+298',
            ),
            68 =>
            array(
                'id' => 69,
                'name' => 'France',
                'code' => 'fr',
                'phone_code' => '+33',
            ),
            69 =>
            array(
                'id' => 70,
                'name' => 'Gabon',
                'code' => 'ga',
                'phone_code' => '+241',
            ),
            70 =>
            array(
                'id' => 71,
                'name' => 'Great Britain (UK)',
                'code' => 'gb',
                'phone_code' => '+44',
            ),
            71 =>
            array(
                'id' => 72,
                'name' => 'Grenada',
                'code' => 'gd',
                'phone_code' => '+1473',
            ),
            72 =>
            array(
                'id' => 73,
                'name' => 'Georgia',
                'code' => 'ge',
                'phone_code' => '+995',
            ),
            73 =>
            array(
                'id' => 74,
                'name' => 'French Guiana',
                'code' => 'gf',
                'phone_code' => '+594',
            ),
            74 =>
            array(
                'id' => 75,
                'name' => 'NULL',
                'code' => 'gg',
                'phone_code' => '+44',
            ),
            75 =>
            array(
                'id' => 76,
                'name' => 'Ghana',
                'code' => 'gh',
                'phone_code' => '+233',
            ),
            76 =>
            array(
                'id' => 77,
                'name' => 'Gibraltar',
                'code' => 'gi',
                'phone_code' => '+350',
            ),
            77 =>
            array(
                'id' => 78,
                'name' => 'Greenland',
                'code' => 'gl',
                'phone_code' => '+299',
            ),
            78 =>
            array(
                'id' => 79,
                'name' => 'Gambia',
                'code' => 'gm',
                'phone_code' => '+220',
            ),
            79 =>
            array(
                'id' => 80,
                'name' => 'Guinea',
                'code' => 'gn',
                'phone_code' => '+224',
            ),
            80 =>
            array(
                'id' => 81,
                'name' => 'Guadeloupe',
                'code' => 'gp',
                'phone_code' => '+590',
            ),
            81 =>
            array(
                'id' => 82,
                'name' => 'Equatorial Guinea',
                'code' => 'gq',
                'phone_code' => '+240',
            ),
            82 =>
            array(
                'id' => 83,
                'name' => 'Greece',
                'code' => 'gr',
                'phone_code' => '+30',
            ),
            83 =>
            array(
                'id' => 84,
                'name' => 'S. Georgia and S. Sandwich Islands',
                'code' => 'gs',
                'phone_code' => '+500',
            ),
            84 =>
            array(
                'id' => 85,
                'name' => 'Guatemala',
                'code' => 'gt',
                'phone_code' => '+502',
            ),
            85 =>
            array(
                'id' => 86,
                'name' => 'Guinea-Bissau',
                'code' => 'gw',
                'phone_code' => '+245',
            ),
            86 =>
            array(
                'id' => 87,
                'name' => 'Guyana',
                'code' => 'gy',
                'phone_code' => '+592',
            ),
            87 =>
            array(
                'id' => 88,
                'name' => 'Hong Kong',
                'code' => 'hk',
                'phone_code' => '+852',
            ),
            88 =>
            array(
                'id' => 89,
                'name' => 'Honduras',
                'code' => 'hn',
                'phone_code' => '+504',
            ),
            89 =>
            array(
                'id' => 90,
                'name' => 'Croatia (Hrvatska)',
                'code' => 'hr',
                'phone_code' => '+385',
            ),
            90 =>
            array(
                'id' => 91,
                'name' => 'Haiti',
                'code' => 'ht',
                'phone_code' => '+509',
            ),
            91 =>
            array(
                'id' => 92,
                'name' => 'Hungary',
                'code' => 'hu',
                'phone_code' => '+36',
            ),
            92 =>
            array(
                'id' => 93,
                'name' => 'Indonesia',
                'code' => 'id',
                'phone_code' => '+62',
            ),
            93 =>
            array(
                'id' => 94,
                'name' => 'Ireland',
                'code' => 'ie',
                'phone_code' => '+353',
            ),
            94 =>
            array(
                'id' => 95,
                'name' => 'Israel',
                'code' => 'il',
                'phone_code' => '+972',
            ),
            95 =>
            array(
                'id' => 96,
                'name' => 'India',
                'code' => 'in',
                'phone_code' => '+91',
            ),
            96 =>
            array(
                'id' => 97,
                'name' => 'Iraq',
                'code' => 'iq',
                'phone_code' => '+964',
            ),
            97 =>
            array(
                'id' => 98,
                'name' => 'Iran',
                'code' => 'ir',
                'phone_code' => '+98',
            ),
            98 =>
            array(
                'id' => 99,
                'name' => 'Iceland',
                'code' => 'is',
                'phone_code' => '+354',
            ),
            99 =>
            array(
                'id' => 100,
                'name' => 'Italy',
                'code' => 'it',
                'phone_code' => '+39',
            ),
            100 =>
            array(
                'id' => 101,
                'name' => 'Jamaica',
                'code' => 'jm',
                'phone_code' => '+1876',
            ),
            101 =>
            array(
                'id' => 102,
                'name' => 'Jordan',
                'code' => 'jo',
                'phone_code' => '+962',
            ),
            102 =>
            array(
                'id' => 103,
                'name' => 'Japan',
                'code' => 'jp',
                'phone_code' => '+81',
            ),
            103 =>
            array(
                'id' => 104,
                'name' => 'Kenya',
                'code' => 'ke',
                'phone_code' => '+254',
            ),
            104 =>
            array(
                'id' => 105,
                'name' => 'Kyrgyzstan',
                'code' => 'kg',
                'phone_code' => '+996',
            ),
            105 =>
            array(
                'id' => 106,
                'name' => 'Cambodia',
                'code' => 'kh',
                'phone_code' => '+855',
            ),
            106 =>
            array(
                'id' => 107,
                'name' => 'Kiribati',
                'code' => 'ki',
                'phone_code' => '+686',
            ),
            107 =>
            array(
                'id' => 108,
                'name' => 'Comoros',
                'code' => 'km',
                'phone_code' => '+269',
            ),
            108 =>
            array(
                'id' => 109,
                'name' => 'Saint Kitts and Nevis',
                'code' => 'kn',
                'phone_code' => '+1869',
            ),
            109 =>
            array(
                'id' => 110,
                'name' => 'Korea (North)',
                'code' => 'kp',
                'phone_code' => '+850',
            ),
            110 =>
            array(
                'id' => 111,
                'name' => 'Korea (South)',
                'code' => 'kr',
                'phone_code' => '+82',
            ),
            111 =>
            array(
                'id' => 112,
                'name' => 'Kuwait',
                'code' => 'kw',
                'phone_code' => '+965',
            ),
            112 =>
            array(
                'id' => 113,
                'name' => 'Cayman Islands',
                'code' => 'ky',
                'phone_code' => '+1345',
            ),
            113 =>
            array(
                'id' => 114,
                'name' => 'Kazakhstan',
                'code' => 'kz',
                'phone_code' => '+76',
            ),
            114 =>
            array(
                'id' => 115,
                'name' => 'Laos',
                'code' => 'la',
                'phone_code' => '+856',
            ),
            115 =>
            array(
                'id' => 116,
                'name' => 'Lebanon',
                'code' => 'lb',
                'phone_code' => '+961',
            ),
            116 =>
            array(
                'id' => 117,
                'name' => 'Saint Lucia',
                'code' => 'lc',
                'phone_code' => '+1758',
            ),
            117 =>
            array(
                'id' => 118,
                'name' => 'Liechtenstein',
                'code' => 'li',
                'phone_code' => '+423',
            ),
            118 =>
            array(
                'id' => 119,
                'name' => 'Sri Lanka',
                'code' => 'lk',
                'phone_code' => '+94',
            ),
            119 =>
            array(
                'id' => 120,
                'name' => 'Liberia',
                'code' => 'lr',
                'phone_code' => '+231',
            ),
            120 =>
            array(
                'id' => 121,
                'name' => 'Lesotho',
                'code' => 'ls',
                'phone_code' => '+266',
            ),
            121 =>
            array(
                'id' => 122,
                'name' => 'Lithuania',
                'code' => 'lt',
                'phone_code' => '+370',
            ),
            122 =>
            array(
                'id' => 123,
                'name' => 'Luxembourg',
                'code' => 'lu',
                'phone_code' => '+352',
            ),
            123 =>
            array(
                'id' => 124,
                'name' => 'Latvia',
                'code' => 'lv',
                'phone_code' => '+371',
            ),
            124 =>
            array(
                'id' => 125,
                'name' => 'Libya',
                'code' => 'ly',
                'phone_code' => '+218',
            ),
            125 =>
            array(
                'id' => 126,
                'name' => 'Morocco',
                'code' => 'ma',
                'phone_code' => '+212',
            ),
            126 =>
            array(
                'id' => 127,
                'name' => 'Monaco',
                'code' => 'mc',
                'phone_code' => '+377',
            ),
            127 =>
            array(
                'id' => 128,
                'name' => 'Moldova',
                'code' => 'md',
                'phone_code' => '+373',
            ),
            128 =>
            array(
                'id' => 129,
                'name' => 'Madagascar',
                'code' => 'mg',
                'phone_code' => '+261',
            ),
            129 =>
            array(
                'id' => 130,
                'name' => 'Marshall Islands',
                'code' => 'mh',
                'phone_code' => '+692',
            ),
            130 =>
            array(
                'id' => 131,
                'name' => 'Macedonia',
                'code' => 'mk',
                'phone_code' => '+389',
            ),
            131 =>
            array(
                'id' => 132,
                'name' => 'Mali',
                'code' => 'ml',
                'phone_code' => '+223',
            ),
            132 =>
            array(
                'id' => 133,
                'name' => 'Myanmar',
                'code' => 'mm',
                'phone_code' => '+95',
            ),
            133 =>
            array(
                'id' => 134,
                'name' => 'Mongolia',
                'code' => 'mn',
                'phone_code' => '+976',
            ),
            134 =>
            array(
                'id' => 135,
                'name' => 'Macao',
                'code' => 'mo',
                'phone_code' => '+853',
            ),
            135 =>
            array(
                'id' => 136,
                'name' => 'Northern Mariana Islands',
                'code' => 'mp',
                'phone_code' => '+1670',
            ),
            136 =>
            array(
                'id' => 137,
                'name' => 'Martinique',
                'code' => 'mq',
                'phone_code' => '+596',
            ),
            137 =>
            array(
                'id' => 138,
                'name' => 'Mauritania',
                'code' => 'mr',
                'phone_code' => '+222',
            ),
            138 =>
            array(
                'id' => 139,
                'name' => 'Montserrat',
                'code' => 'ms',
                'phone_code' => '+1664',
            ),
            139 =>
            array(
                'id' => 140,
                'name' => 'Malta',
                'code' => 'mt',
                'phone_code' => '+356',
            ),
            140 =>
            array(
                'id' => 141,
                'name' => 'Mauritius',
                'code' => 'mu',
                'phone_code' => '+230',
            ),
            141 =>
            array(
                'id' => 142,
                'name' => 'Maldives',
                'code' => 'mv',
                'phone_code' => '+960',
            ),
            142 =>
            array(
                'id' => 143,
                'name' => 'Malawi',
                'code' => 'mw',
                'phone_code' => '+265',
            ),
            143 =>
            array(
                'id' => 144,
                'name' => 'Mexico',
                'code' => 'mx',
                'phone_code' => '+52',
            ),
            144 =>
            array(
                'id' => 145,
                'name' => 'Malaysia',
                'code' => 'my',
                'phone_code' => '+60',
            ),
            145 =>
            array(
                'id' => 146,
                'name' => 'Mozambique',
                'code' => 'mz',
                'phone_code' => '+258',
            ),
            146 =>
            array(
                'id' => 147,
                'name' => 'Namibia',
                'code' => 'na',
                'phone_code' => '+264',
            ),
            147 =>
            array(
                'id' => 148,
                'name' => 'New Caledonia',
                'code' => 'nc',
                'phone_code' => '+687',
            ),
            148 =>
            array(
                'id' => 149,
                'name' => 'Niger',
                'code' => 'ne',
                'phone_code' => '+227',
            ),
            149 =>
            array(
                'id' => 150,
                'name' => 'Norfolk Island',
                'code' => 'nf',
                'phone_code' => '+672',
            ),
            150 =>
            array(
                'id' => 151,
                'name' => 'Nigeria',
                'code' => 'ng',
                'phone_code' => '+234',
            ),
            151 =>
            array(
                'id' => 152,
                'name' => 'Nicaragua',
                'code' => 'ni',
                'phone_code' => '+505',
            ),
            152 =>
            array(
                'id' => 153,
                'name' => 'Netherlands',
                'code' => 'nl',
                'phone_code' => '+31',
            ),
            153 =>
            array(
                'id' => 154,
                'name' => 'Norway',
                'code' => 'no',
                'phone_code' => '+47',
            ),
            154 =>
            array(
                'id' => 155,
                'name' => 'Nepal',
                'code' => 'np',
                'phone_code' => '+977',
            ),
            155 =>
            array(
                'id' => 156,
                'name' => 'Nauru',
                'code' => 'nr',
                'phone_code' => '+674',
            ),
            156 =>
            array(
                'id' => 157,
                'name' => 'Niue',
                'code' => 'nu',
                'phone_code' => '+683',
            ),
            157 =>
            array(
                'id' => 158,
                'name' => 'New Zealand (Aotearoa)',
                'code' => 'nz',
                'phone_code' => '+64',
            ),
            158 =>
            array(
                'id' => 159,
                'name' => 'Oman',
                'code' => 'om',
                'phone_code' => '+968',
            ),
            159 =>
            array(
                'id' => 160,
                'name' => 'Panama',
                'code' => 'pa',
                'phone_code' => '+507',
            ),
            160 =>
            array(
                'id' => 161,
                'name' => 'Peru',
                'code' => 'pe',
                'phone_code' => '+51',
            ),
            161 =>
            array(
                'id' => 162,
                'name' => 'French Polynesia',
                'code' => 'pf',
                'phone_code' => '+689',
            ),
            162 =>
            array(
                'id' => 163,
                'name' => 'Papua New Guinea',
                'code' => 'pg',
                'phone_code' => '+675',
            ),
            163 =>
            array(
                'id' => 164,
                'name' => 'Philippines',
                'code' => 'ph',
                'phone_code' => '+63',
            ),
            164 =>
            array(
                'id' => 165,
                'name' => 'Pakistan',
                'code' => 'pk',
                'phone_code' => '+92',
            ),
            165 =>
            array(
                'id' => 166,
                'name' => 'Poland',
                'code' => 'pl',
                'phone_code' => '+48',
            ),
            166 =>
            array(
                'id' => 167,
                'name' => 'Saint Pierre and Miquelon',
                'code' => 'pm',
                'phone_code' => '+508',
            ),
            167 =>
            array(
                'id' => 168,
                'name' => 'Pitcairn',
                'code' => 'pn',
                'phone_code' => '+64',
            ),
            168 =>
            array(
                'id' => 169,
                'name' => 'Palestinian Territory',
                'code' => 'ps',
                'phone_code' => '+970',
            ),
            169 =>
            array(
                'id' => 170,
                'name' => 'Portugal',
                'code' => 'pt',
                'phone_code' => '+351',
            ),
            170 =>
            array(
                'id' => 171,
                'name' => 'Palau',
                'code' => 'pw',
                'phone_code' => '+680',
            ),
            171 =>
            array(
                'id' => 172,
                'name' => 'Paraguay',
                'code' => 'py',
                'phone_code' => '+595',
            ),
            172 =>
            array(
                'id' => 173,
                'name' => 'Qatar',
                'code' => 'qa',
                'phone_code' => '+974',
            ),
            173 =>
            array(
                'id' => 174,
                'name' => 'Reunion',
                'code' => 're',
                'phone_code' => '+262',
            ),
            174 =>
            array(
                'id' => 175,
                'name' => 'Romania',
                'code' => 'ro',
                'phone_code' => '+40',
            ),
            175 =>
            array(
                'id' => 176,
                'name' => 'Russian Federation',
                'code' => 'ru',
                'phone_code' => '+73',
            ),
            176 =>
            array(
                'id' => 177,
                'name' => 'Rwanda',
                'code' => 'rw',
                'phone_code' => '+250',
            ),
            177 =>
            array(
                'id' => 178,
                'name' => 'Saudi Arabia',
                'code' => 'sa',
                'phone_code' => '+966',
            ),
            178 =>
            array(
                'id' => 179,
                'name' => 'Solomon Islands',
                'code' => 'sb',
                'phone_code' => '+677',
            ),
            179 =>
            array(
                'id' => 180,
                'name' => 'Seychelles',
                'code' => 'sc',
                'phone_code' => '+248',
            ),
            180 =>
            array(
                'id' => 181,
                'name' => 'Sudan',
                'code' => 'sd',
                'phone_code' => '+249',
            ),
            181 =>
            array(
                'id' => 182,
                'name' => 'Sweden',
                'code' => 'se',
                'phone_code' => '+46',
            ),
            182 =>
            array(
                'id' => 183,
                'name' => 'Singapore',
                'code' => 'sg',
                'phone_code' => '+65',
            ),
            183 =>
            array(
                'id' => 184,
                'name' => 'Saint Helena',
                'code' => 'sh',
                'phone_code' => '+290',
            ),
            184 =>
            array(
                'id' => 185,
                'name' => 'Slovenia',
                'code' => 'si',
                'phone_code' => '+386',
            ),
            185 =>
            array(
                'id' => 186,
                'name' => 'Svalbard and Jan Mayen',
                'code' => 'sj',
                'phone_code' => '+4779',
            ),
            186 =>
            array(
                'id' => 187,
                'name' => 'Slovakia',
                'code' => 'sk',
                'phone_code' => '+421',
            ),
            187 =>
            array(
                'id' => 188,
                'name' => 'Sierra Leone',
                'code' => 'sl',
                'phone_code' => '+232',
            ),
            188 =>
            array(
                'id' => 189,
                'name' => 'San Marino',
                'code' => 'sm',
                'phone_code' => '+378',
            ),
            189 =>
            array(
                'id' => 190,
                'name' => 'Senegal',
                'code' => 'sn',
                'phone_code' => '+221',
            ),
            190 =>
            array(
                'id' => 191,
                'name' => 'Somalia',
                'code' => 'so',
                'phone_code' => '+252',
            ),
            191 =>
            array(
                'id' => 192,
                'name' => 'Suriname',
                'code' => 'sr',
                'phone_code' => '+597',
            ),
            192 =>
            array(
                'id' => 193,
                'name' => 'Sao Tome and Principe',
                'code' => 'st',
                'phone_code' => '+239',
            ),
            193 =>
            array(
                'id' => 194,
                'name' => 'El Salvador',
                'code' => 'sv',
                'phone_code' => '+503',
            ),
            194 =>
            array(
                'id' => 195,
                'name' => 'Syria',
                'code' => 'sy',
                'phone_code' => '+963',
            ),
            195 =>
            array(
                'id' => 196,
                'name' => 'Swaziland',
                'code' => 'sz',
                'phone_code' => '+268',
            ),
            196 =>
            array(
                'id' => 197,
                'name' => 'Turks and Caicos Islands',
                'code' => 'tc',
                'phone_code' => '+1649',
            ),
            197 =>
            array(
                'id' => 198,
                'name' => 'Chad',
                'code' => 'td',
                'phone_code' => '+235',
            ),
            198 =>
            array(
                'id' => 199,
                'name' => 'French Southern Territories',
                'code' => 'tf',
                'phone_code' => '+262',
            ),
            199 =>
            array(
                'id' => 200,
                'name' => 'Togo',
                'code' => 'tg',
                'phone_code' => '+228',
            ),
            200 =>
            array(
                'id' => 201,
                'name' => 'Thailand',
                'code' => 'th',
                'phone_code' => '+66',
            ),
            201 =>
            array(
                'id' => 202,
                'name' => 'Tajikistan',
                'code' => 'tj',
                'phone_code' => '+992',
            ),
            202 =>
            array(
                'id' => 203,
                'name' => 'Tokelau',
                'code' => 'tk',
                'phone_code' => '+690',
            ),
            203 =>
            array(
                'id' => 204,
                'name' => 'Turkmenistan',
                'code' => 'tm',
                'phone_code' => '+993',
            ),
            204 =>
            array(
                'id' => 205,
                'name' => 'Tunisia',
                'code' => 'tn',
                'phone_code' => '+216',
            ),
            205 =>
            array(
                'id' => 206,
                'name' => 'Tonga',
                'code' => 'to',
                'phone_code' => '+676',
            ),
            206 =>
            array(
                'id' => 207,
                'name' => 'Turkey',
                'code' => 'tr',
                'phone_code' => '+90',
            ),
            207 =>
            array(
                'id' => 208,
                'name' => 'Trinidad and Tobago',
                'code' => 'tt',
                'phone_code' => '+1868',
            ),
            208 =>
            array(
                'id' => 209,
                'name' => 'Tuvalu',
                'code' => 'tv',
                'phone_code' => '+688',
            ),
            209 =>
            array(
                'id' => 210,
                'name' => 'Taiwan',
                'code' => 'tw',
                'phone_code' => '+886',
            ),
            210 =>
            array(
                'id' => 211,
                'name' => 'Tanzania',
                'code' => 'tz',
                'phone_code' => '+255',
            ),
            211 =>
            array(
                'id' => 212,
                'name' => 'Ukraine',
                'code' => 'ua',
                'phone_code' => '+380',
            ),
            212 =>
            array(
                'id' => 213,
                'name' => 'Uganda',
                'code' => 'ug',
                'phone_code' => '+256',
            ),
            213 =>
            array(
                'id' => 214,
                'name' => 'Uruguay',
                'code' => 'uy',
                'phone_code' => '+598',
            ),
            214 =>
            array(
                'id' => 215,
                'name' => 'Uzbekistan',
                'code' => 'uz',
                'phone_code' => '+998',
            ),
            215 =>
            array(
                'id' => 216,
                'name' => 'Saint Vincent and the Grenadines',
                'code' => 'vc',
                'phone_code' => '+1784',
            ),
            216 =>
            array(
                'id' => 217,
                'name' => 'Venezuela',
                'code' => 've',
                'phone_code' => '+58',
            ),
            217 =>
            array(
                'id' => 218,
                'name' => 'Virgin Islands (British)',
                'code' => 'vg',
                'phone_code' => '+1284',
            ),
            218 =>
            array(
                'id' => 219,
                'name' => 'Virgin Islands (U.S.)',
                'code' => 'vi',
                'phone_code' => '+1340',
            ),
            219 =>
            array(
                'id' => 220,
                'name' => 'Viet Nam',
                'code' => 'vn',
                'phone_code' => '+84',
            ),
            220 =>
            array(
                'id' => 221,
                'name' => 'Vanuatu',
                'code' => 'vu',
                'phone_code' => '+678',
            ),
            221 =>
            array(
                'id' => 222,
                'name' => 'Wallis and Futuna',
                'code' => 'wf',
                'phone_code' => '+681',
            ),
            222 =>
            array(
                'id' => 223,
                'name' => 'Samoa',
                'code' => 'ws',
                'phone_code' => '+685',
            ),
            223 =>
            array(
                'id' => 224,
                'name' => 'Yemen',
                'code' => 'ye',
                'phone_code' => '+967',
            ),
            224 =>
            array(
                'id' => 225,
                'name' => 'Mayotte',
                'code' => 'yt',
                'phone_code' => '+262',
            ),
            225 =>
            array(
                'id' => 226,
                'name' => 'South Africa',
                'code' => 'za',
                'phone_code' => '+27',
            ),
            226 =>
            array(
                'id' => 227,
                'name' => 'Zambia',
                'code' => 'zm',
                'phone_code' => '+260',
            ),
            227 =>
            array(
                'id' => 229,
                'name' => 'Zimbabwe',
                'code' => 'zw',
                'phone_code' => '+263',
            ),
            228 =>
            array(
                'id' => 230,
                'name' => 'United States of America',
                'code' => 'us',
                'phone_code' => '+1',
            ),
        ));
    }
}
