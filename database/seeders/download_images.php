<?php

$dir = __DIR__ . '/../../storage/app/public/products';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

$images = [
    'kaju-katli.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/8/85/Kaju_katli.JPG',
    'motichoor-laddu.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/b/b8/Motichoor_ladoo.JPG',
    'mysore-pak.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/f/ff/Mysore_pak.jpg',
    'gulab-jamun.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/6/61/Gulab_Jamun_as_a_Diwali_Sweet.jpg',
    'dharwad-peda.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/0/01/Dharwad_peda.jpg',
    'pootharekulu.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/4/40/Pootharekulu_dry_fruits.jpg',
    'murukku.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/7/7d/Benne_Mucchore.jpg',
    'mixture.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/0/00/Poha_Chivda_-_Homemade_-_Maharashtra_-_The_healthy%2C_tasty_snack.jpg',
    'avakaya-pickle.jpg' => 'https://upload.wikimedia.org/wikipedia/commons/0/08/Andhra_avakaya_recipe.jpg',
];

foreach ($images as $filename => $url) {
    $dest = $dir . '/' . $filename;
    echo "Downloading $filename...\n";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AnandamSweets/1.0');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);
    $data = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($code == 200 && strlen($data) > 1000) {
        file_put_contents($dest, $data);
        echo "  Saved: $dest (" . round(strlen($data) / 1024) . " KB)\n";
    } else {
        echo "  FAILED ($code)\n";
    }
}

echo "Done downloading images!\n";
