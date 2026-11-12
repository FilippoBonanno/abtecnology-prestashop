<?php
// Uso: php tests/render-miniature.php /percorso/prestashop  (serve vendor/smarty di PrestaShop 9)
$root = $argv[1] ?? __DIR__ . '/../../prestashop';
require $root . '/vendor/smarty/smarty/libs/Smarty.class.php';
$s = new Smarty();
$s->setTemplateDir([__DIR__ . '/../theme/templates', __DIR__ . '/stubs']); // stubs: include del tema non necessari al test $s->setCompileDir(sys_get_temp_dir() . '/ab-miniature-test'); $s->escape_html = true;
$s->registerPlugin('function', 'l', function ($p) { return $p['s']; });
$s->registerPlugin('function', 'hook', function ($p) { return ''; });
$img = ['url' => 'x.jpg', 'width' => 320, 'height' => 320, 'sources' => []];
$cover = ['legend' => 'leg', 'bySize' => ['default_xs' => $img, 'default_m' => $img, 'default_md' => $img, 'product_main' => $img, 'home_default' => $img]];
$names = [
 ['Sottospazzola con setole compatibile per HD35 per Vorwerk Folletto VK135/136', 'available', 'cart'],
 ['Confezione 5 sacchetti microfibra Vorwerk Folletto VB100 Compatibile', 'last_remaining_items', 'cart'],
 ['Interruttore rosso 4 contatti DIDIESSE Frog Originale', 'available', 'cart'],
 ['Alloggio portacialda per BABYFROG DIDESSE Originale', 'available', 'cart'],
 ['Boccale Originale Bimby TM31 Vorwerk: Ricambio Acciaio Inox 2 Litri', 'unavailable', null],
 ['Spazzola Folletto VK 120/21/22 ricambio', 'available', 'cart'],
 ['Sacchetti per Folletto VK7s', 'available', 'cart'],
 ['spazzola snodabile HD35 - Compatibile Vorwerk Folletto vk135/136', 'available', 'cart'],
 ['Termometro Digitale da Incasso -50°C/+70°C Bianco 46×26mm', 'available', null],
];
$s->assign(['urls' => ['pages' => ['cart' => '/carrello'], 'no_picture_image' => $cover], 'static_token' => 'tok']);
foreach ($names as $n) {
  $s->assign('product', ['id_product' => 1, 'id_product_attribute' => 0, 'url' => '/p', 'cover' => $cover, 'name' => $n[0], 'main_variants' => [], 'show_price' => true, 'price' => '7,00 €', 'has_discount' => false, 'regular_price' => '', 'add_to_cart_url' => $n[2], 'availability' => $n[1], 'minimal_quantity' => 1]);
  $h = $s->fetch('catalog/_partials/miniatures/product.tpl');
  preg_match('/__model">([^<]*)</', $h, $m); preg_match('/__kind">([^<]*)</', $h, $k);
  $btn = strpos($h, '__cart-btn') ? 'CART' : (strpos($h, '__notify--icon') ? 'AVVISAMI' : (strpos($h, '__notify') ? 'DETTAGLI' : '?'));
  printf("%-70s | %-22s | %-12s | %s%s\n", substr($n[0], 0, 70), $m[1] ?? '-', $k[1] ?? '-', $btn, strpos($h, 'Esaurito') ? ' +Esaurito' : '');
}
