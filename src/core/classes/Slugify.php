<?php

namespace Gila;

class Slugify
{
  public static $rules;

  static function text ($text, $separator = '-') {
    $slug = mb_strtolower($text);
    $slug = strtr($slug, self::$rules);
    $slug = preg_replace('/([^A-Za-z0-9]|-)+/', $separator, $slug);
    return trim($slug, $separator);
  }

}

Slugify::$rules = [
  'أ'=>'a',
  'ب'=>'b',
  'ت'=>'t',
  'ث'=>'th',
  'ج'=>'g',
  'ح'=>'h',
  'خ'=>'kh',
  'د'=>'d',
  'ذ'=>'th',
  'ر'=>'r',
  'ز'=>'z',
  'س'=>'s',
  'ش'=>'sh',
  'ص'=>'s',
  'ض'=>'d',
  'ط'=>'t',
  'ظ'=>'th',
  'ع'=>'aa',
  'غ'=>'gh',
  'ف'=>'f',
  'ق'=>'k',
  'ك'=>'k',
  'ل'=>'l',
  'م'=>'m',
  'ن'=>'n',
  'ه'=>'h',
  'و'=>'o',
  'ي'=>'y',
  'ß'=>'ss',
  'ä'=>'a',
  'ö'=>'o',
  'ü'=>'u',
  'ə'=>'e',
  'ç'=>'c',
  'ğ'=>'g',
  'ı'=>'i',
  'ş'=>'s',
  'а'=>'a',
  'б'=>'b',
  'в'=>'v',
  'г'=>'g',
  'д'=>'d',
  'е'=>'e',
  'ж'=>'zh',
  'з'=>'z',
  'и'=>'i',
  'й'=>'y',
  'к'=>'k',
  'л'=>'l',
  'м'=>'m',
  'н'=>'n',
  'о'=>'o',
  'п'=>'p',
  'р'=>'r',
  'с'=>'s',
  'т'=>'t',
  'у'=>'u',
  'ф'=>'f',
  'х'=>'h',
  'ц'=>'c',
  'ч'=>'ch',
  'ш'=>'sh',
  'щ'=>'shch',
  'ъ'=>'',
  'ь'=>'',
  'ю'=>'yu',
  'я'=>'ya',
  'ия'=>'ia',
  'йо'=>'iо',
  'ьо'=>'io',
  'က'=>'k',
  'ခ'=>'kh',
  'ဂ'=>'g',
  'ဃ'=>'ga',
  'င'=>'ng',
  'စ'=>'s',
  'ဆ'=>'sa',
  'ဇ'=>'z',
  'စျ'=>'za',
  'ည'=>'ny',
  'ဋ'=>'t',
  'ဌ'=>'ta',
  'ဍ'=>'d',
  'ဎ'=>'da',
  'ဏ'=>'na',
  'တ'=>'t',
  'ထ'=>'ta',
  'ဒ'=>'d',
  'ဓ'=>'da',
  'န'=>'n',
  'ပ'=>'p',
  'ဖ'=>'pa',
  'ဗ'=>'b',
  'ဘ'=>'ba',
  'မ'=>'m',
  'ယ'=>'y',
  'ရ'=>'ya',
  'လ'=>'l',
  'ဝ'=>'w',
  'သ'=>'th',
  'ဟ'=>'h',
  'ဠ'=>'la',
  'အ'=>'a',
  'ြ'=>'y',
  'ျ'=>'ya',
  'ွ'=>'w',
  'ြွ'=>'yw',
  'ျွ'=>'ywa',
  'ှ'=>'h',
  'ဧ'=>'e',
  '၏'=>'-e',
  'ဣ'=>'i',
  'ဤ'=>'-i',
  'ဉ'=>'u',
  'ဦ'=>'-u',
  'ဩ'=>'aw',
  'သြော'=>'aw',
  'ဪ'=>'aw',
  '၍'=>'ywae',
  '၌'=>'hnaik',
  '၀'=>'0',
  '၁'=>'1',
  '၂'=>'2',
  '၃'=>'3',
  '၄'=>'4',
  '၅'=>'5',
  '၆'=>'6',
  '၇'=>'7',
  '၈'=>'8',
  '၉'=>'9',
  '္'=>'',
  '့'=>'',
  'း'=>'',
  'ာ'=>'a',
  'ါ'=>'a',
  'ေ'=>'e',
  'ဲ'=>'e',
  'ိ'=>'i',
  'ီ'=>'i',
  'ို'=>'o',
  'ု'=>'u',
  'ူ'=>'u',
  'ေါင်'=>'aung',
  'ော'=>'aw',
  'ော်'=>'aw',
  'ေါ'=>'aw',
  'ေါ်'=>'aw',
  '်'=>'at',
  'က်'=>'et',
  'ိုက်'=>'aik',
  'ောက်'=>'auk',
  'င်'=>'in',
  'ိုင်'=>'aing',
  'ောင်'=>'aung',
  'စ်'=>'it',
  'ည်'=>'i',
  'တ်'=>'at',
  'ိတ်'=>'eik',
  'ုတ်'=>'ok',
  'ွတ်'=>'ut',
  'ေတ်'=>'it',
  'ဒ်'=>'d',
  'ိုဒ်'=>'ok',
  'ုဒ်'=>'ait',
  'န်'=>'an',
  'ာန်'=>'an',
  'ိန်'=>'ein',
  'ုန်'=>'on',
  'ွန်'=>'un',
  'ပ်'=>'at',
  'ိပ်'=>'eik',
  'ုပ်'=>'ok',
  'ွပ်'=>'ut',
  'န်ုပ်'=>'nub',
  'မ်'=>'an',
  'ိမ်'=>'ein',
  'ုမ်'=>'on',
  'ွမ်'=>'un',
  'ယ်'=>'e',
  'ိုလ်'=>'ol',
  'ဉ်'=>'in',
  'ံ'=>'an',
  'ိံ'=>'ein',
  'ုံ'=>'on',
  'č'=>'c',
  'ć'=>'c',
  'ž'=>'z',
  'š'=>'s',
  'đ'=>'d',
  'ď'=>'d',
  'ě'=>'e',
  'ň'=>'n',
  'ř'=>'r',
  'ť'=>'t',
  'ů'=>'u',
  '°'=>'0',
  '¹'=>'1',
  '²'=>'2',
  '³'=>'3',
  '⁴'=>'4',
  '⁵'=>'5',
  '⁶'=>'6',
  '⁷'=>'7',
  '⁸'=>'8',
  '⁹'=>'9',
  '₀'=>'0',
  '₁'=>'1',
  '₂'=>'2',
  '₃'=>'3',
  '₄'=>'4',
  '₅'=>'5',
  '₆'=>'6',
  '₇'=>'7',
  '₈'=>'8',
  '₉'=>'9',
  'æ'=>'ae',
  'ǽ'=>'ae',
  'à'=>'a',
  'á'=>'a',
  'â'=>'a',
  'ã'=>'a',
  'å'=>'a',
  'ǻ'=>'a',
  'ă'=>'a',
  'ǎ'=>'a',
  'ª'=>'a',
  '@'=>'at',
  'ĉ'=>'cx',
  'ċ'=>'c',
  '©'=>'c',
  'ð'=>'dj',
  'è'=>'e',
  'é'=>'e',
  'ê'=>'e',
  'ë'=>'e',
  'ĕ'=>'e',
  'ė'=>'e',
  'ƒ'=>'f',
  'ĝ'=>'gx',
  'ġ'=>'g',
  'ĥ'=>'hx',
  'ħ'=>'h',
  'ì'=>'i',
  'í'=>'i',
  'î'=>'i',
  'ï'=>'i',
  'ĩ'=>'i',
  'ĭ'=>'i',
  'ǐ'=>'i',
  'į'=>'i',
  'ĳ'=>'ij',
  'ĵ'=>'jx',
  'ĺ'=>'l',
  'ľ'=>'l',
  'ŀ'=>'l',
  'ñ'=>'n',
  'ŉ'=>'n',
  'ò'=>'o',
  'ó'=>'o',
  'ô'=>'o',
  'õ'=>'o',
  'ō'=>'o',
  'ŏ'=>'o',
  'ǒ'=>'o',
  'ő'=>'o',
  'ơ'=>'o',
  'ø'=>'oe',
  'ǿ'=>'o',
  'º'=>'o',
  'œ'=>'oe',
  'ŕ'=>'r',
  'ŗ'=>'r',
  'ŝ'=>'sx',
  'ș'=>'s',
  'ſ'=>'s',
  'ţ'=>'t',
  'ț'=>'t',
  'ŧ'=>'t',
  'þ'=>'th',
  'ù'=>'u',
  'ú'=>'u',
  'û'=>'u',
  'ũ'=>'u',
  'ŭ'=>'ux',
  'ű'=>'u',
  'ų'=>'u',
  'ư'=>'u',
  'ǔ'=>'u',
  'ǖ'=>'u',
  'ǘ'=>'u',
  'ǚ'=>'u',
  'ǜ'=>'u',
  'ŵ'=>'w',
  'ý'=>'y',
  'ÿ'=>'y',
  'ŷ'=>'y',
  'ა'=>'a',
  'ბ'=>'b',
  'გ'=>'g',
  'დ'=>'d',
  'ე'=>'e',
  'ვ'=>'v',
  'ზ'=>'z',
  'თ'=>'t',
  'ი'=>'i',
  'კ'=>'k',
  'ლ'=>'l',
  'მ'=>'m',
  'ნ'=>'n',
  'ო'=>'o',
  'პ'=>'p',
  'ჟ'=>'zh',
  'რ'=>'r',
  'ს'=>'s',
  'ტ'=>'t',
  'უ'=>'u',
  'ფ'=>'f',
  'ქ'=>'k',
  'ღ'=>'gh',
  'ყ'=>'q',
  'შ'=>'sh',
  'ჩ'=>'ch',
  'ც'=>'ts',
  'ძ'=>'dz',
  'წ'=>'ts',
  'ჭ'=>'ch',
  'ხ'=>'kh',
  'ჯ'=>'j',
  'ჰ'=>'h',
  'αυ'=>'au',
  'ου'=>'ou',
  'ευ'=>'eu',
  'ει'=>'i',
  'οι'=>'i',
  'υι'=>'i',
  'αύ'=>'au',
  'ού'=>'ou',
  'εύ'=>'eu',
  'εί'=>'i',
  'οί'=>'i',
  'ύι'=>'i',
  'υί'=>'i',
  'ϒ'=>'I',
  'α'=>'a',
  'β'=>'v',
  'γ'=>'g',
  'δ'=>'d',
  'ε'=>'e',
  'ζ'=>'z',
  'η'=>'i',
  'θ'=>'th',
  'ι'=>'i',
  'κ'=>'k',
  'λ'=>'l',
  'μ'=>'m',
  'ν'=>'n',
  'ξ'=>'x',
  'ο'=>'o',
  'π'=>'p',
  'ρ'=>'r',
  'ς'=>'s',
  'σ'=>'s',
  'τ'=>'t',
  'υ'=>'i',
  'φ'=>'f',
  'χ'=>'ch',
  'ψ'=>'ps',
  'ω'=>'o',
  'ά'=>'a',
  'έ'=>'e',
  'ή'=>'i',
  'ί'=>'i',
  'ό'=>'o',
  'ύ'=>'i',
  'ϊ'=>'i',
  'ϋ'=>'i',
  'ΰ'=>'i',
  'ώ'=>'o',
  'ϐ'=>'v',
  'ϑ'=>'th',
  'अ'=>'a',
  'आ'=>'aa',
  'ए'=>'e',
  'ई'=>'ii',
  'ऍ'=>'ei',
  'ऎ'=>'ai',
  'ऐ'=>'ai',
  'इ'=>'i',
  'ओ'=>'o',
  'ऑ'=>'oi',
  'ऒ'=>'oii',
  'ऊ'=>'uu',
  'औ'=>'ou',
  'उ'=>'u',
  'ब'=>'B',
  'भ'=>'Bha',
  'च'=>'Ca',
  'छ'=>'Chha',
  'ड'=>'Da',
  'ढ'=>'Dha',
  'फ'=>'Fa',
  'फ़'=>'Fi',
  'ग'=>'Ga',
  'घ'=>'Gha',
  'ग़'=>'Ghi',
  'ह'=>'Ha',
  'ज'=>'Ja',
  'झ'=>'Jha',
  'क'=>'Ka',
  'ख'=>'Kha',
  'ख़'=>'Khi',
  'ल'=>'L',
  'ळ'=>'Li',
  'ऌ'=>'Li',
  'ऴ'=>'Lii',
  'ॡ'=>'Lii',
  'म'=>'Ma',
  'न'=>'Na',
  'ङ'=>'Na',
  'ञ'=>'Nia',
  'ण'=>'Nae',
  'ऩ'=>'Ni',
  'ॐ'=>'oms',
  'प'=>'Pa',
  'क़'=>'Qi',
  'र'=>'Ra',
  'ऋ'=>'Ri',
  'ॠ'=>'Ri',
  'ऱ'=>'Ri',
  'स'=>'Sa',
  'श'=>'Sha',
  'ष'=>'Shha',
  'ट'=>'Ta',
  'त'=>'Ta',
  'ठ'=>'Tha',
  'द'=>'Tha',
  'थ'=>'Tha',
  'ध'=>'Thha',
  'ड़'=>'ugDha',
  'ढ़'=>'ugDhha',
  'व'=>'Va',
  'य'=>'Ya',
  'य़'=>'Yi',
  'ज़'=>'Za',
  'ā'=>'a',
  'ē'=>'e',
  'ģ'=>'g',
  'ī'=>'i',
  'ķ'=>'k',
  'ļ'=>'l',
  'ņ'=>'n',
  'ū'=>'u',
  'ą'=>'a',
  'ę'=>'e',
  'ł'=>'l',
  'ń'=>'n',
  'ś'=>'s',
  'ź'=>'z',
  'ż'=>'z',
  'ё'=>'e',
  'э'=>'e',
  'ы'=>'y',
  'ґ'=>'g',
  'і'=>'i',
  'ї'=>'ji',
  'є'=>'ye',
  'ạ'=>'a',
  'ả'=>'a',
  'ầ'=>'a',
  'ấ'=>'a',
  'ậ'=>'a',
  'ẩ'=>'a',
  'ẫ'=>'a',
  'ằ'=>'a',
  'ắ'=>'a',
  'ặ'=>'a',
  'ẳ'=>'a',
  'ẵ'=>'a',
  'ẹ'=>'e',
  'ẻ'=>'e',
  'ẽ'=>'e',
  'ề'=>'e',
  'ế'=>'e',
  'ệ'=>'e',
  'ể'=>'e',
  'ễ'=>'e',
  'ị'=>'i',
  'ỉ'=>'i',
  'ọ'=>'o',
  'ỏ'=>'o',
  'ồ'=>'o',
  'ố'=>'o',
  'ộ'=>'o',
  'ổ'=>'o',
  'ỗ'=>'o',
  'ờ'=>'o',
  'ớ'=>'o',
  'ợ'=>'o',
  'ở'=>'o',
  'ỡ'=>'o',
  'ụ'=>'u',
  'ủ'=>'u',
  'ừ'=>'u',
  'ứ'=>'u',
  'ự'=>'u',
  'ử'=>'u',
  'ữ'=>'u',
  'ỳ'=>'y',
  'ỵ'=>'y',
  'ỷ'=>'y',
  'ỹ'=>'y',
];
