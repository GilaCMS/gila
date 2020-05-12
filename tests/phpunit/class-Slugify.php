<?php
chdir(__DIR__.'/../../');
include __DIR__.'/../../vendor/autoload.php';
include __DIR__.'/../../src/core/classes/Slugify.php';

use PHPUnit\Framework\TestCase;

class ClassSlugify extends TestCase
{
	public function test_slugify()
	{
		$text = [
			"“Our work on remdesivir absolutely would not have moved forward” without it.",
			"Αυτή η κατηγορία έχει τις ακόλουθες 5 υποκατηγορίες, από 5 συνολικά. ",
			"esta página NO es un oráculo, ni un servicio de respuestas,",
			"Bu başlığı diğer sayfalarda arayabilir, ilgili kayıtları arayabilir, ya da bu sayfayı oluşturabilirsiniz",
			"E se quiser fazer testes, faça-os na Zona de testes",
			"هذه الصفحة خالية حاليا. يمكنك البحث عن عنوانها في الصفحات الأخرى أو البحث في السجلات (لتعرف إن كانت قد حُذِفَت)، أو إنشاؤها",
			"La chaîne Transantarctique, ou monts Transantarctiques, est une longue chaîne de montagnes située en Antarctique",
		];
		$slugs = [
			"our-work-on-remdesivir-absolutely-would-not-have-moved-forward-without-it",
			"auti-i-katigoria-echi-tis-akolouthes-5-ipokatigories-apo-5-sinolika",
			"esta-pagina-no-es-un-oraculo-ni-un-servicio-de-respuestas",
			"bu-basligi-diger-sayfalarda-arayabilir-ilgili-kayitlari-arayabilir-ya-da-bu-sayfayi-olusturabilirsiniz",
			"e-se-quiser-fazer-testes-faca-os-na-zona-de-testes",
			"hthh-lsfh-kh-ly-h-ly-ymknk-lbhth-aan-aano-nh-fy-lsfh-t-lakhr-ao-lbhth-fy-lsgl-t-ltaarf-n-k-nt-kd-h-th-f-t-ao-nsh-h",
			"la-chaine-transantarctique-ou-monts-transantarctiques-est-une-longue-chaine-de-montagnes-situee-en-antarctique"
		];

		foreach($text as $key => $slug) {
			echo Slugify::text($slug);
			$this->assertEquals($slugs[$key], Slugify::text($slug));
		}

	}
}
