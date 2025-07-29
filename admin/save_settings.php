<?php
$settingsFile = '../data/settings.json';
$data = [
  'tabName' => $_POST['tabName'] ?? ($_POST['name'] ?? ''),
  'name' => $_POST['name'] ?? '',
  'bio' => $_POST['bio'] ?? '',
  'socialLinks' => $_POST['socialLinks'] ?? '',
  'design' => $_POST['design'] ?? 'default',
  'effects' => isset($_POST['effects']) ? (array)$_POST['effects'] : [],
  'adminPassword' => $_POST['adminPassword'] ?? ''
];

// Tab Icon
if (isset($_FILES['tabIcon']) && $_FILES['tabIcon']['error'] == 0) {
  $targetTab = '../data/tabicon.png';
  if (file_exists($targetTab)) unlink($targetTab); // Überschreibe alte Datei
  if (move_uploaded_file($_FILES['tabIcon']['tmp_name'], $targetTab)) {
    $data['tabIcon'] = 'data/tabicon.png';
  }
} else if (file_exists($settingsFile)) {
  $old = json_decode(file_get_contents($settingsFile), true);
  if (isset($old['tabIcon'])) $data['tabIcon'] = $old['tabIcon'];
  else if (isset($data['profilePic'])) $data['tabIcon'] = $data['profilePic'];
  else $data['tabIcon'] = 'assets/icon.png';
}

// Profile Picture
if (isset($_FILES['profilePic']) && $_FILES['profilePic']['error'] == 0) {
  $target = '../data/profile.jpg';
  if (file_exists($target)) unlink($target); // Überschreibe alte Datei
  if (move_uploaded_file($_FILES['profilePic']['tmp_name'], $target)) {
    $data['profilePic'] = 'data/profile.jpg';
  }
} else if (file_exists($settingsFile)) {
  $old = json_decode(file_get_contents($settingsFile), true);
  if (isset($old['profilePic'])) $data['profilePic'] = $old['profilePic'];
}

file_put_contents($settingsFile, json_encode($data, JSON_PRETTY_PRINT));
header('Location: index.php');
?>
