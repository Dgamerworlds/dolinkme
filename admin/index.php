<?php
session_start();
$settingsFile = '../data/settings.json';
$linksFile = '../data/links.json';
$settings = file_exists($settingsFile) ? json_decode(file_get_contents($settingsFile), true) : [];
$links = file_exists($linksFile) ? json_decode(file_get_contents($linksFile), true) : [];
$loggedIn = isset($_SESSION['admin']) && $_SESSION['admin'] === true;

// Handle login
if (isset($_POST['password'])) {
    if (isset($settings['adminPassword']) && $_POST['password'] === $settings['adminPassword']) {
        $_SESSION['admin'] = true;
        header("Location: index.php");
        exit;
    } else {
        $error = "Wrong password!";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

if (!$loggedIn):
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="../designs/minimal.css">
</head>
<body>
  <h2>Admin Login</h2>
  <form method="post">
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
  </form>
</body>
</html>
<?php
exit;
endif;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Panel</title>
  <link rel="stylesheet" href="../designs/admin.css">
  <script src="../effects/admin.js"></script>
</head>
<body>
  <h2>Admin Panel</h2>
  <a href="?logout=1" style="float:right;">Logout</a>
  <form id="profile-form" enctype="multipart/form-data" method="post" action="save_settings.php">
    <fieldset>
      <legend>Tab Information</legend>
      <label>Tab Name:</label>
      <input type="text" name="tabName" value="<?php echo htmlspecialchars($settings['tabName'] ?? $settings['name'] ?? ''); ?>"><br>
      <label>Tab Icon:</label>
      <?php
        $tabIcon = !empty($settings['tabIcon']) && file_exists('../'.$settings['tabIcon'])
          ? '../'.$settings['tabIcon']
          : (!empty($settings['profilePic']) && file_exists('../'.$settings['profilePic']) ? '../'.$settings['profilePic'] : '../assets/icon.png');
        echo '<img src="'.$tabIcon.'" alt="Tab Icon" style="width:32px;height:32px;border-radius:50%;margin-bottom:8px;"><br>';
      ?>
      <input type="file" name="tabIcon"><br>
    </fieldset>
    <fieldset>
      <legend>Profile</legend>
      <label>Profile Picture:</label>
      <?php
        $pic = !empty($settings['profilePic']) && file_exists('../'.$settings['profilePic']) ? '../'.$settings['profilePic'] : '../assets/icon.png';
        echo '<img src="'.$pic.'" alt="Current Picture" style="width:64px;height:64px;border-radius:50%;margin-bottom:8px;"><br>';
      ?>
      <input type="file" name="profilePic"><br>
      <label>Name:</label>
      <input type="text" name="name" value="<?php echo htmlspecialchars($settings['name'] ?? ''); ?>"><br>
      <label>Bio:</label>
      <textarea name="bio"><?php echo htmlspecialchars($settings['bio'] ?? ''); ?></textarea><br>
      <label>Social Links (comma separated):</label>
      <input type="text" name="socialLinks" value="<?php echo htmlspecialchars($settings['socialLinks'] ?? ''); ?>"><br>
    </fieldset>
    <fieldset>
      <legend>Design & Effects</legend>
      <label>Design:</label>
      <select name="design">
        <option value="default" <?php if(($settings['design']??'')=='default')echo"selected";?>>Default</option>
        <option value="dark" <?php if(($settings['design']??'')=='dark')echo"selected";?>>Dark</option>
        <option value="gradient" <?php if(($settings['design']??'')=='gradient')echo"selected";?>>Gradient</option>
        <option value="glass" <?php if(($settings['design']??'')=='glass')echo"selected";?>>Glassmorphism</option>
        <option value="minimal" <?php if(($settings['design']??'')=='minimal')echo"selected";?>>Minimal</option>
        <option value="neon" <?php if(($settings['design']??'')=='neon')echo"selected";?>>Neon</option>
        <option value="retro" <?php if(($settings['design']??'')=='retro')echo"selected";?>>Retro</option>
        <option value="terminal" <?php if(($settings['design']??'')=='terminal')echo"selected";?>>Terminal</option>
        <option value="pastel" <?php if(($settings['design']??'')=='pastel')echo"selected";?>>Pastel</option>
        <option value="cyber" <?php if(($settings['design']??'')=='cyber')echo"selected";?>>Cyber</option>
      </select><br>
      <label>Effects:</label>
      <select name="effects[]" multiple size="8" id="effects-select">
        <option value="none" <?php echo (empty($settings['effects']) || in_array('none', $settings['effects'])) ? 'selected' : ''; ?>>None</option>
        <option value="fade" <?php echo (!empty($settings['effects']) && in_array('fade', $settings['effects'])) ? 'selected' : ''; ?>>Fade In</option>
        <option value="slide" <?php echo (!empty($settings['effects']) && in_array('slide', $settings['effects'])) ? 'selected' : ''; ?>>Slide Up</option>
        <option value="zoom" <?php echo (!empty($settings['effects']) && in_array('zoom', $settings['effects'])) ? 'selected' : ''; ?>>Zoom</option>
        <option value="bounce" <?php echo (!empty($settings['effects']) && in_array('bounce', $settings['effects'])) ? 'selected' : ''; ?>>Bounce</option>
        <option value="flip" <?php echo (!empty($settings['effects']) && in_array('flip', $settings['effects'])) ? 'selected' : ''; ?>>Flip</option>
        <option value="rotate" <?php echo (!empty($settings['effects']) && in_array('rotate', $settings['effects'])) ? 'selected' : ''; ?>>Rotate</option>
        <option value="blur" <?php echo (!empty($settings['effects']) && in_array('blur', $settings['effects'])) ? 'selected' : ''; ?>>Blur</option>
      </select><br>
    </fieldset>
    <fieldset>
      <legend>Security</legend>
      <label>Admin Password:</label>
      <input type="password" name="adminPassword" value="<?php echo htmlspecialchars($settings['adminPassword'] ?? ''); ?>"><br>
    </fieldset>
    <fieldset>
      <legend>Export / Import</legend>
      <button type="button" onclick="exportSettings()">Export Settings</button>
      <input type="file" id="importSettings" name="importSettings" accept=".json" onchange="importSettings(this)">
    </fieldset>
    <button type="submit">Save Settings</button>
  </form>
  <hr>
  <form id="links-form" method="post" action="save_links.php">
    <fieldset>
      <legend>Links</legend>
      <div id="links-list">
        <?php
        foreach ($links as $i => $link) {
          echo '<div><input type="text" name="links[]" value="'.htmlspecialchars($link['url']).'" placeholder="Link URL"> <input type="text" name="titles[]" value="'.htmlspecialchars($link['title']).'" placeholder="Title"> <button type="button" onclick="this.parentNode.remove()">Remove</button></div>';
        }
        ?>
      </div>
      <button type="button" onclick="addLink()">Add Link</button>
      <button type="submit">Save Links</button>
    </fieldset>
  </form>
  <hr>
  <fieldset>
    <legend>Preview</legend>
    <iframe src="../index.php" style="width:100%;height:400px;border:1px solid #ccc;"></iframe>
  </fieldset>
  <script>
    function addLink() {
      const container = document.getElementById('links-list');
      const div = document.createElement('div');
      div.innerHTML = '<input type="text" name="links[]" placeholder="Link URL"> <input type="text" name="titles[]" placeholder="Title"> <button type="button" onclick="this.parentNode.remove()">Remove</button>';
      container.appendChild(div);
    }
    function exportSettings() {
      fetch('../data/settings.json').then(r=>r.blob()).then(blob=>{
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'settings.json';
        a.click();
        URL.revokeObjectURL(url);
      });
    }
    function importSettings(input) {
      const file = input.files[0];
      if (!file) return;
      const reader = new FileReader();
      reader.onload = function(e) {
        fetch('save_settings.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: e.target.result
        }).then(()=>location.reload());
      };
      reader.readAsText(file);
    }
    document.getElementById('effects-select').addEventListener('change', function(e) {
      // Wenn "None" gewählt wird, alles andere abwählen
      if (Array.from(this.selectedOptions).some(opt => opt.value === 'none')) {
        Array.from(this.options).forEach(opt => {
          if (opt.value !== 'none') opt.selected = false;
        });
      } else {
        // Wenn etwas anderes gewählt wird, "None" abwählen
        this.options[0].selected = false;
      }
    });
  </script>
</body>
</html>
