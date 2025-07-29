<?php
$linksFile = '../data/links.json';
$links = [];
if (isset($_POST['links']) && isset($_POST['titles'])) {
  foreach ($_POST['links'] as $i => $url) {
    $links[] = [
      'url' => $url,
      'title' => $_POST['titles'][$i]
    ];
  }
}
file_put_contents($linksFile, json_encode($links, JSON_PRETTY_PRINT));
header('Location: index.php');
?>
