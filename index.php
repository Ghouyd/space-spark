<?php
session_start();

$pdo = new PDO(
    "mysql:host=localhost;dbname=game;charset=utf8",
    "root",
    ""
);

// إذا ضغط Start Game
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);

    if ($username !== "") {

        $check = $pdo->prepare("SELECT * FROM users WHERE username = ?");
        $check->execute([$username]);

        $user = $check->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $insert = $pdo->prepare("INSERT INTO users(username, points) VALUES (?, 0)");
            $insert->execute([$username]);
        }

        $_SESSION["username"] = $username;

        header("Location: index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Space Park</title>
  <link rel="stylesheet" href="space.css" />
</head>
<body>
<?php if (!isset($_SESSION["username"])): ?>
<div class="login-overlay">
    <div class="login-card">
        <h2>Enter Username 🚀</h2>

        <form method="POST" class="login-form">
            <input
                type="text"
                name="username"
                placeholder="Enter Username"
                class="login-input"
                required
            >

            <button type="submit" class="login-btn">
                Start Game
            </button>
        </form>
    </div>
</div>
<?php endif; ?>
<!-- Splash Screen -->
<section id="splash" class="splash">
  <div class="splash-overlay"></div>
  <div class="splash-content">
    <div class="brand-mark">✦</div>
   <h1 class="splash-logo" style=" font-size:100px;">
 <span class="spark-word">S</span>pace
  <span class="spark-word">S</span>park

 
  <span class="twinkle t1">✦</span>
  <span class="twinkle t2">✦</span>
  <span class="twinkle t3">✦</span>
</h1>
    <p class="splash-tagline">Learn • Explore • Grow</p>
  </div>
</section>


  <!-- Main Page -->
  <main id="mainPage" class="page hidden">
    <div class="page-overlay"></div>

    <header class="topbar">
        <div class="logo-text">
          <h1>SpaceSpark</h1>
          <p>Learn • Explore • Grow</p>
        </div>
      

      <nav class="nav">
        <a href="#" class="active"> Home</a>
        
        <a href="Profile.html"> Profile</a>
      </nav>

      
    </header>

    <section class="hero">
      <h2>Let’s Learn and Explore!</h2>
      <p class="subtitle">Choose a planet and start your adventure</p>
    </section>

    <section class="content-section">
      <div class="moveRight"></div>

      <div class="planets-grid">
        <article class="planet-card">
          <div class="planet-image-wrap">
           <a href="Levels.html"><img src="numbers.png" alt="Numbers Planet" /> </a> 
          </div>
          <div class="planet-btn blue">Numbers Planet</div>
          <p>Learn numbers, counting, and have fun with math!</p>
        </article>

        <article class="planet-card">
          <div class="planet-image-wrap">
            <img src="letters.png" alt="Letters Planet" />
          </div>
          <div class="planet-btn purple">Letters Planet</div>
          <p>Discover letters, words, and build your vocabulary!</p>
        </article>

        <article class="planet-card">
          <div class="planet-image-wrap">
            <img src="animals2.png" alt="Animals Planet" />
          </div>
          <div class="planet-btn green">Animals Planet</div>
          <p>Meet amazing animals and learn about their world!</p>
        </article>
      </div>
    </section>
  </main>

  <script>
    const splash = document.getElementById("splash");
    const mainPage = document.getElementById("mainPage");

    window.addEventListener("load", () => {
      setTimeout(() => {
        splash.classList.add("fade-out");
        mainPage.classList.remove("hidden");
        mainPage.classList.add("show");

        setTimeout(() => {
          splash.style.display = "none";
        }, 900);
      }, 2000);
    });
  </script>
</body>
</html>
