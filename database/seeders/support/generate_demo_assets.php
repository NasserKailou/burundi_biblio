<?php

/**
 * Genere les fichiers de demo (PDF, EPUB, couvertures JPG) utilises par
 * ManuelSeeder. Script autonome (n'utilise pas le bootstrap Laravel),
 * a relancer manuellement si les fixtures doivent etre regenerees :
 *
 *   php database/seeders/support/generate_demo_assets.php
 */

require __DIR__.'/../../../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$baseDir = __DIR__.'/../assets';
$manuelsDir = $baseDir.'/manuels';
$couverturesDir = $baseDir.'/couvertures';

@mkdir($manuelsDir, 0777, true);
@mkdir($couverturesDir, 0777, true);

$manuels = [
    ['slug' => 'mathematiques-6e', 'titre' => 'Mathematiques 6e', 'matiere' => 'Mathematiques', 'type' => 'pdf', 'couleur' => [37, 99, 235]],
    ['slug' => 'francais-6e', 'titre' => 'Francais 6e - Grammaire et Conjugaison', 'matiere' => 'Francais', 'type' => 'pdf', 'couleur' => [220, 38, 38]],
    ['slug' => 'histoire-geo-5e', 'titre' => 'Histoire-Geographie 5e', 'matiere' => 'Histoire-Geographie', 'type' => 'epub', 'couleur' => [180, 83, 9]],
    ['slug' => 'svt-4e', 'titre' => 'Sciences de la Vie et de la Terre 4e', 'matiere' => 'SVT', 'type' => 'pdf', 'couleur' => [22, 163, 74]],
    ['slug' => 'physique-chimie-3e', 'titre' => 'Physique-Chimie 3e', 'matiere' => 'Physique-Chimie', 'type' => 'pdf', 'couleur' => [124, 58, 237]],
    ['slug' => 'anglais-2nde', 'titre' => 'Anglais 2nde', 'matiere' => 'Anglais', 'type' => 'epub', 'couleur' => [8, 145, 178]],
    ['slug' => 'mathematiques-terminale', 'titre' => 'Mathematiques Terminale', 'matiere' => 'Mathematiques', 'type' => 'pdf', 'couleur' => [30, 64, 175]],
    ['slug' => 'dictionnaire-francais', 'titre' => 'Dictionnaire Francais (ressource commune)', 'matiere' => 'Francais', 'type' => 'pdf', 'couleur' => [190, 24, 93]],
    ['slug' => 'atlas-histoire-geo', 'titre' => 'Atlas Histoire-Geographie (ressource commune)', 'matiere' => 'Histoire-Geographie', 'type' => 'epub', 'couleur' => [217, 119, 6]],
    ['slug' => 'guide-methodologie', 'titre' => 'Guide Methodologique - Reussir ses etudes (ressource commune)', 'matiere' => 'Education Civique', 'type' => 'pdf', 'couleur' => [5, 150, 105]],
];

function generatePdf(string $titre, string $matiere, string $outPath): void
{
    $options = new Options();
    $options->set('isRemoteEnabled', false);
    $dompdf = new Dompdf($options);

    $pages = '';
    for ($i = 1; $i <= 5; $i++) {
        $pages .= '<div style="page-break-after: always; padding: 60px 40px; font-family: sans-serif;">';
        if ($i === 1) {
            $pages .= '<h1 style="font-size: 32px;">'.htmlspecialchars($titre).'</h1>';
            $pages .= '<p style="font-size: 16px; color: #444;">Matiere : '.htmlspecialchars($matiere).'</p>';
            $pages .= '<p style="font-size: 13px; color: #888; margin-top: 300px;">Document de demonstration genere automatiquement pour la Bibliotheque Numerique Scolaire (BNS). Contenu factice, sans valeur pedagogique.</p>';
        } else {
            $pages .= '<h2>Chapitre '.($i - 1).'</h2>';
            $pages .= str_repeat('<p>Contenu de demonstration pour la page '.$i.' du manuel "'.htmlspecialchars($titre).'". Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>', 3);
        }
        $pages .= '</div>';
    }

    $dompdf->loadHtml('<html><body>'.$pages.'</body></html>');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    file_put_contents($outPath, $dompdf->output());
}

function generateEpub(string $titre, string $matiere, string $outPath): void
{
    if (file_exists($outPath)) {
        unlink($outPath);
    }

    $zip = new ZipArchive();
    $zip->open($outPath, ZipArchive::CREATE);

    $zip->addFromString('mimetype', 'application/epub+zip');

    $containerXml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
        .'<container version="1.0" xmlns="urn:oasis:names:tc:opendocument:xmlns:container">'."\n"
        .'  <rootfiles>'."\n"
        .'    <rootfile full-path="OEBPS/content.opf" media-type="application/oebps-package+xml"/>'."\n"
        .'  </rootfiles>'."\n"
        .'</container>';
    $zip->addFromString('META-INF/container.xml', $containerXml);

    $uid = 'bns-demo-'.md5($titre);

    $contentOpf = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
        .'<package xmlns="http://www.idpf.org/2007/opf" unique-identifier="bookid" version="2.0">'."\n"
        .'  <metadata xmlns:dc="http://purl.org/dc/elements/1.1/">'."\n"
        .'    <dc:title>'.$titre.'</dc:title>'."\n"
        .'    <dc:creator>Bibliotheque Numerique Scolaire</dc:creator>'."\n"
        .'    <dc:identifier id="bookid">'.$uid.'</dc:identifier>'."\n"
        .'    <dc:language>fr</dc:language>'."\n"
        .'    <dc:subject>'.$matiere.'</dc:subject>'."\n"
        .'  </metadata>'."\n"
        .'  <manifest>'."\n"
        .'    <item id="ncx" href="toc.ncx" media-type="application/x-dtbncx+xml"/>'."\n"
        .'    <item id="chap1" href="chapter1.xhtml" media-type="application/xhtml+xml"/>'."\n"
        .'    <item id="chap2" href="chapter2.xhtml" media-type="application/xhtml+xml"/>'."\n"
        .'  </manifest>'."\n"
        .'  <spine toc="ncx">'."\n"
        .'    <itemref idref="chap1"/>'."\n"
        .'    <itemref idref="chap2"/>'."\n"
        .'  </spine>'."\n"
        .'</package>';
    $zip->addFromString('OEBPS/content.opf', $contentOpf);

    $tocNcx = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
        .'<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">'."\n"
        .'  <head><meta name="dtb:uid" content="'.$uid.'"/></head>'."\n"
        .'  <docTitle><text>'.$titre.'</text></docTitle>'."\n"
        .'  <navMap>'."\n"
        .'    <navPoint id="np1" playOrder="1"><navLabel><text>Chapitre 1</text></navLabel><content src="chapter1.xhtml"/></navPoint>'."\n"
        .'    <navPoint id="np2" playOrder="2"><navLabel><text>Chapitre 2</text></navLabel><content src="chapter2.xhtml"/></navPoint>'."\n"
        .'  </navMap>'."\n"
        .'</ncx>';
    $zip->addFromString('OEBPS/toc.ncx', $tocNcx);

    foreach ([1, 2] as $n) {
        $chapter = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<!DOCTYPE html>'."\n"
            .'<html xmlns="http://www.w3.org/1999/xhtml">'."\n"
            .'<head><title>Chapitre '.$n.'</title></head>'."\n"
            .'<body>'."\n"
            .'  <h1>'.$titre.' - Chapitre '.$n.'</h1>'."\n"
            .'  <p>Matiere : '.$matiere.'</p>'."\n"
            .'  <p>Contenu de demonstration genere automatiquement pour la Bibliotheque Numerique Scolaire (BNS). Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>'."\n"
            .'</body>'."\n"
            .'</html>';
        $zip->addFromString("OEBPS/chapter{$n}.xhtml", $chapter);
    }

    $zip->close();
}

function generateCouverture(string $titre, array $couleur, string $outPath): void
{
    [$r, $g, $b] = $couleur;
    $w = 400;
    $h = 560;
    $img = imagecreatetruecolor($w, $h);

    $bg = imagecolorallocate($img, $r, $g, $b);
    imagefill($img, 0, 0, $bg);

    $bandColor = imagecolorallocatealpha($img, 255, 255, 255, 100);
    imagefilledrectangle($img, 0, $h - 140, $w, $h, $bandColor);

    $white = imagecolorallocate($img, 255, 255, 255);
    $lines = explode("\n", wordwrap($titre, 18, "\n"));
    $y = 60;
    foreach ($lines as $line) {
        imagestring($img, 5, 24, $y, $line, $white);
        $y += 22;
    }

    imagestring($img, 3, 24, $h - 110, 'Bibliotheque Numerique', $white);
    imagestring($img, 3, 24, $h - 90, 'Scolaire', $white);

    imagejpeg($img, $outPath, 85);
    imagedestroy($img);
}

foreach ($manuels as $m) {
    $ext = $m['type'] === 'pdf' ? 'pdf' : 'epub';
    $fichier = $manuelsDir.'/'.$m['slug'].'.'.$ext;

    if ($m['type'] === 'pdf') {
        generatePdf($m['titre'], $m['matiere'], $fichier);
    } else {
        generateEpub($m['titre'], $m['matiere'], $fichier);
    }

    generateCouverture($m['titre'], $m['couleur'], $couverturesDir.'/'.$m['slug'].'.jpg');

    echo "OK: {$m['slug']}\n";
}

echo "Termine.\n";
