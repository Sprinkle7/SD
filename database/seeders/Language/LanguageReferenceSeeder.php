<?php

namespace Database\Seeders\Language;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageReferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * nooooot in use
     * @return void
     */
    public function run()
    {
        $languages =
            [['title'=>'English','code'=> 'en'],
                ['title'=>'Afar','code'=> 'aa'],
                ['title'=>'Abkhazian','code'=> 'ab'],
                ['title'=>'Afrikaans','code'=> 'af'],
                ['title'=>'Amharic','code'=> 'am'],
                ['title'=>'Arabic','code'=> 'ar'],
                ['title'=>'Assamese','code'=> 'as'],
                ['title'=>'Aymara','code'=> 'ay'],
                ['title'=>'Azerbaijani','code'=> 'az'],
                ['title'=>'Bashkir','code'=> 'ba'],
                ['title'=>'Belarusian','code'=> 'be'],
                ['title'=>'Bulgarian','code'=> 'bg'],
                ['title'=>'Bihari','code'=> 'bh'],
                ['title'=>'Bislama','code'=> 'bi'],
                ['title'=>'Bengali/Bangla','code'=> 'bn'],
                ['title'=>'Tibetan','code'=> 'bo'],
                ['title'=>'Breton','code'=> 'br'],
                ['title'=>'Catalan','code'=> 'ca'],
                ['title'=>'Corsican','code'=> 'co'],
                ['title'=>'Czech','code'=> 'cs'],
                ['title'=>'Welsh','code'=> 'cy'],
                ['title'=>'Danish','code'=> 'da'],
                ['title'=>'German','code'=> 'de'],
                ['title'=>'Bhutani','code'=> 'dz'],
                ['title'=>'Greek','code'=> 'el'],
                ['title'=>'Esperanto','code'=> 'eo'],
                ['title'=>'Spanish','code'=> 'es'],
                ['title'=>'Estonian','code'=> 'et'],
                ['title'=>'Basque','code'=> 'eu'],
                ['title'=>'Persian','code'=> 'fa'],
                ['title'=>'Finnish','code'=> 'fi'],
                ['title'=>'Fiji','code'=> 'fj'],
                ['title'=>'Faeroese','code'=> 'fo'],
                ['title'=>'French','code'=> 'fr'],
                ['title'=>'Frisian','code'=> 'fy'],
                ['title'=>'Irish','code'=> 'ga'],
                ['title'=>'Scots/Gaelic','code'=> 'gd'],
                ['title'=>'Galician','code'=> 'gl'],
                ['title'=>'Guarani','code'=> 'gn'],
                ['title'=>'Gujarati','code'=> 'gu'],
                ['title'=>'Hausa','code'=> 'ha'],
                ['title'=>'Hindi','code'=> 'hi'],
                ['title'=>'Croatian','code'=> 'hr'],
                ['title'=>'Hungarian','code'=> 'hu'],
                ['title'=>'Armenian','code'=> 'hy'],
                ['title'=>'Interlingua','code'=> 'ia'],
                ['title'=>'Interlingue','code'=> 'ie'],
                ['title'=>'Inupiak','code'=> 'ik'],
                ['title'=>'Indonesian','code'=> 'in'],
                ['title'=>'Icelandic','code'=> 'is'],
                ['title'=>'Italian','code'=> 'it'],
                ['title'=>'Hebrew','code'=> 'iw'],
                ['title'=>'Japanese','code'=> 'ja'],
                ['title'=>'Yiddish','code'=> 'ji'],
                ['title'=>'Javanese','code'=> 'jw'],
                ['title'=>'Georgian','code'=> 'ka'],
                ['title'=>'Kazakh','code'=> 'kk'],
                ['title'=>'Greenlandic','code'=> 'kl'],
                ['title'=>'Cambodian','code'=> 'km'],
                ['title'=>'Kannada','code'=> 'kn'],
                ['title'=>'Korean','code'=> 'ko'],
                ['title'=>'Kashmiri','code'=> 'ks'],
                ['title'=>'Kurdish','code'=> 'ku'],
                ['title'=>'Kirghiz','code'=> 'ky'],
                ['title'=>'Latin','code'=> 'la'],
                ['title'=>'Lingala','code'=> 'ln'],
                ['title'=>'Laothian','code'=> 'lo'],
                ['title'=>'Lithuanian','code'=> 'lt'],
                ['title'=>'Latvian/Lettish','code'=> 'lv'],
                ['title'=>'Malagasy','code'=> 'mg'],
                ['title'=>'Maori','code'=> 'mi'],
                ['title'=>'Macedonian','code'=> 'mk'],
                ['title'=>'Malayalam','code'=> 'ml'],
                ['title'=>'Mongolian','code'=> 'mn'],
                ['title'=>'Moldavian','code'=> 'mo'],
                ['title'=>'Marathi','code'=> 'mr'],
                ['title'=>'Malay','code'=> 'ms'],
                ['title'=>'Maltese','code'=> 'mt'],
                ['title'=>'Burmese','code'=> 'my'],
                ['title'=>'Nauru','code'=> 'na'],
                ['title'=>'Nepali','code'=> 'ne'],
                ['title'=>'Dutch','code'=> 'nl'],
                ['title'=>'Norwegian','code'=> 'no'],
                ['title'=>'Occitan','code'=> 'oc'],
                ['title'=>'Punjabi','code'=> 'pa'],
                ['title'=>'Polish','code'=> 'pl'],
                ['title'=>'Pashto/Pushto','code'=> 'ps'],
                ['title'=>'Portuguese','code'=> 'pt'],
                ['title'=>'Quechua','code'=> 'qu'],
                ['title'=>'Rhaeto-Romance','code'=> 'rm'],
                ['title'=>'Kirundi','code'=> 'rn'],
                ['title'=>'Romanian','code'=> 'ro'],
                ['title'=>'Russian','code'=> 'ru'],
                ['title'=>'Kinyarwanda','code'=> 'rw'],
                ['title'=>'Sanskrit','code'=> 'sa'],
                ['title'=>'Sindhi','code'=> 'sd'],
                ['title'=>'Sangro','code'=> 'sg'],
                ['title'=>'Serbo-Croatian','code'=> 'sh'],
                ['title'=>'Singhalese','code'=> 'si'],
                ['title'=>'Slovak','code'=> 'sk'],
                ['title'=>'Slovenian','code'=> 'sl'],
                ['title'=>'Samoan','code'=> 'sm'],
                ['title'=>'Shona','code'=> 'sn'],
                ['title'=>'Somali','code'=> 'so'],
                ['title'=>'Albanian','code'=> 'sq'],
                ['title'=>'Serbian','code'=> 'sr'],
                ['title'=>'Siswati','code'=> 'ss'],
                ['title'=>'Sesotho','code'=> 'st'],
                ['title'=>'Sundanese','code'=> 'su'],
                ['title'=>'Swedish','code'=> 'sv'],
                ['title'=>'Swahili','code'=> 'sw'],
                ['title'=>'Tamil','code'=> 'ta'],
                ['title'=>'Telugu','code'=> 'te'],
                ['title'=>'Tajik','code'=> 'tg'],
                ['title'=>'Thai','code'=> 'th'],
                ['title'=>'Tigrinya','code'=> 'ti'],
                ['title'=>'Turkmen','code'=> 'tk'],
                ['title'=>'Tagalog','code'=> 'tl'],
                ['title'=>'Setswana','code'=> 'tn'],
                ['title'=>'Tonga','code'=> 'to'],
                ['title'=>'Turkish','code'=> 'tr'],
                ['title'=>'Tsonga','code'=> 'ts'],
                ['title'=>'Tatar','code'=> 'tt'],
                ['title'=>'Twi','code'=> 'tw'],
                ['title'=>'Ukrainian','code'=> 'uk'],
                ['title'=>'Urdu','code'=> 'ur'],
                ['title'=>'Uzbek','code'=> 'uz'],
                ['title'=>'Vietnamese','code'=> 'vi'],
                ['title'=>'Volapuk','code'=> 'vo'],
                ['title'=>'Wolof','code'=> 'wo'],
                ['title'=>'Xhosa','code'=> 'xh'],
                ['title'=>'Yoruba','code'=> 'yo'],
                ['title'=>'Chinese','code'=> 'zh'],
                ['title'=>'Zulu','code'=> 'zu']];
        DB::table('language_references')->insert(
            $languages
        );
    }
}
