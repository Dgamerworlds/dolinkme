<?php
$settings = file_exists('data/settings.json') ? json_decode(file_get_contents('data/settings.json'), true) : [];
$links = file_exists('data/links.json') ? json_decode(file_get_contents('data/links.json'), true) : [];
$design = $settings['design'] ?? 'default';
$effects = isset($settings['effects']) ? $settings['effects'] : [];
$profilePic = (!empty($settings['profilePic']) && file_exists($settings['profilePic'])) ? $settings['profilePic'] : 'assets/icon.png';
$tabName = $settings['tabName'] ?? ($settings['name'] ?? '');
$tabIcon = (!empty($settings['tabIcon']) && file_exists($settings['tabIcon']))
  ? $settings['tabIcon']
  : ((!empty($settings['profilePic']) && file_exists($settings['profilePic'])) ? $settings['profilePic'] : 'assets/icon.png');
$hasLinks = !empty($links);
$hasProfile = !empty($settings['name']) || !empty($settings['bio']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo htmlspecialchars($tabName); ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" type="image/png" href="<?php echo $tabIcon; ?>">
  <link rel="stylesheet" href="designs/<?php echo $design; ?>.css">
  <?php foreach ($effects as $effect): ?>
    <?php if ($effect !== 'none'): ?>
      <link rel="stylesheet" href="effects/<?php echo $effect; ?>.css">
    <?php endif; ?>
  <?php endforeach; ?>
</head>
<body>
  <div id="profile">
    <img src="<?php echo $profilePic; ?>" alt="Profile Picture" id="profile-pic">
    <?php if ($hasProfile): ?>
      <h1><?php echo htmlspecialchars($settings['name'] ?? ''); ?></h1>
      <p><?php echo htmlspecialchars($settings['bio'] ?? ''); ?></p>
    <?php endif; ?>
    <?php if ($hasLinks): ?>
      <div id="links">
        <?php foreach ($links as $link): ?>
          <a href="<?php echo htmlspecialchars($link['url']); ?>" target="_blank"><?php echo htmlspecialchars($link['title']); ?></a>
        <?php endforeach; ?>
      </div>
      <div id="social">
        <?php
        $socials = explode(',', $settings['socialLinks'] ?? '');
        foreach ($socials as $social) {
          $social = trim($social);
          if ($social) echo "<span>$social</span> ";
        }
        ?>
      </div>
    <?php else: ?>
      <div class="empty-content">
        <img src="assets/icon.png" alt="No Content">
        <div>No links or profile info available.</div>
      </div>
    <?php endif; ?>
  </div>
  <?php foreach ($effects as $effect): ?>
    <?php if ($effect !== 'none' && file_exists("effects/$effect.js")): ?>
      <script src="effects/<?php echo $effect; ?>.js"></script>
    <?php endif; ?>
  <?php endforeach; ?>
</body>
</html>
