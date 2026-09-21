<?php
session_start();

$pdo = new PDO(
    "mysql:host=localhost;dbname=game;charset=utf8",
    "root",
    ""
);

if (!isset($_SESSION["username"])) {
    http_response_code(401);
    exit("No user");
}

$username = $_SESSION["username"];

// زيد 5 نقاط
$stmt = $pdo->prepare("
    UPDATE users
    SET points = points + 5
    WHERE username = ?
");
$stmt->execute([$username]);

// هات النقاط الجديدة
$stmt = $pdo->prepare("
    SELECT points
    FROM users
    WHERE username = ?
");
$stmt->execute([$username]);

$newPoints = (int)$stmt->fetchColumn();

// خزّنها في session
$_SESSION["points"] = $newPoints;

// رجّع الرقم للصفحة
echo $newPoints;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Addition Planet</title>

<style>
* { box-sizing: border-box; }

body {
  margin: 0;
  font-family: "Trebuchet MS", Arial, sans-serif;
  color: white;
  background: #050b25;
  overflow: hidden;
}

.space-page {
  min-height: 100vh;
  position: relative;
  background:
    radial-gradient(circle at 20% 25%, rgba(124, 58, 237, .45), transparent 25%),
    radial-gradient(circle at 85% 20%, rgba(0, 183, 255, .35), transparent 22%),
    linear-gradient(180deg, #05091f 0%, #071943 55%, #170035 100%);
}

.space-page::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(white 1px, transparent 1px),
    radial-gradient(#ffd76b 1.3px, transparent 1.3px),
    radial-gradient(#7dd3fc 1px, transparent 1px);
  background-size: 70px 70px, 130px 130px, 180px 180px;
  opacity: .8;
}

.moon-ground {
  position: absolute;
  bottom: -90px;
  left: -5%;
  width: 110%;
  height: 250px;
  border-radius: 50% 50% 0 0;
  background:
    radial-gradient(circle at 20% 45%, #4b465b 0 18px, transparent 19px),
    radial-gradient(circle at 45% 35%, #5f5870 0 24px, transparent 25px),
    radial-gradient(circle at 70% 50%, #454056 0 20px, transparent 21px),
    radial-gradient(circle at 85% 30%, #696176 0 18px, transparent 19px),
    linear-gradient(180deg, #bcb5c9, #756d86);
  box-shadow: inset 0 25px 45px rgba(255,255,255,.25);
  z-index: 1;
}

#splash {
  position: fixed;
  inset: 0;
  z-index: 20;
  background:
    radial-gradient(circle at 30% 20%, #4c1d95, transparent 25%),
    linear-gradient(180deg, #020617, #071b45);
  display: flex;
  justify-content: center;
  align-items: center;
}

.splash-card {
  text-align: center;
  padding: 50px 80px;
  border-radius: 35px;
  background: rgba(39, 12, 92, .65);
  border: 2px solid #a855f7;
  box-shadow: 0 0 50px rgba(168,85,247,.9);
  animation: pop .8s ease;
}



.splash-card h1 {
  font-size: 58px;
  margin: 0;
}

.splash-card p {
  font-size: 24px;
}

.game-wrap {
  position: relative;
  z-index: 3;
  min-height: 100vh;
  padding: 35px 60px;
}

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo h2 {
  margin: 0;
  font-size: 32px;
}

.logo p {
  margin: 0;
  opacity: .9;
}

.score-pill {
  background: rgba(76, 29, 149, .85);
  border: 2px solid #7c3aed;
  border-radius: 28px;
  padding: 14px 28px;
  font-size: 30px;
  font-weight: 900;
  box-shadow: 0 0 25px rgba(124,58,237,.6);
}

.title {
  text-align: center;
  margin-top: 35px;
}

.title h1 {
  font-size: 76px;
  margin: 0;
  text-shadow: 0 0 20px rgba(255,255,255,.35);
}

.title p {
  font-size: 28px;
  margin: 8px 0;
}

.game-card {
  width: 760px;
  margin: 35px auto 0;
  padding: 28px 36px 35px;
  border-radius: 36px;
  background: rgba(20, 8, 65, .78);
  border: 3px solid #a855f7;
  box-shadow:
    0 0 45px rgba(168,85,247,.75),
    inset 0 0 40px rgba(59,130,246,.16);
}

.level_2 {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 18px;
  font-size: 22px;
  font-weight: 800;
  margin-bottom: 28px;
}

.progress {
  width: 260px;
  height: 18px;
  background: rgba(255,255,255,.2);
  border-radius: 20px;
  overflow: hidden;
  border: 2px solid rgba(255,255,255,.25);
}

.progress-fill {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, #84cc16, #22c55e);
  border-radius: 20px;
  transition: .3s;
}

.question {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 28px;
  margin-bottom: 22px;
}

.number-box {
  width: 135px;
  height: 135px;
  border-radius: 24px;
  background: linear-gradient(180deg, #fff, #e9eefc);
  color: #111827;
  display: grid;
  place-items: center;
  font-size: 78px;
  font-weight: 900;
  box-shadow: 0 10px 0 rgba(255,255,255,.35);
}

#num1 { color: #9333ea; }
#num2 { color: #22c55e; }
.qmark { color: #f97316; }

.operator {
  font-size: 78px;
  font-weight: 900;
}

.instruction {
  font-size: 24px;
  font-weight: 800;
  margin-bottom: 20px;
}

.options {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 18px;
}

.option {
  height: 95px;
  border: none;
  border-radius: 24px;
  background: linear-gradient(180deg, #ffffff, #e9eefc);
  font-size: 52px;
  font-weight: 900;
  cursor: pointer;
  box-shadow: 0 8px 0 rgba(255,255,255,.35);
  transition: .2s;
}

.option:hover {
  transform: translateY(-8px) scale(1.04);
}

.option:nth-child(1) { color: #0ea5e9; }
.option:nth-child(2) { color: #22c55e; }
.option:nth-child(3) { color: #f97316; }
.option:nth-child(4) { color: #9333ea; }

.message {
  min-height: 32px;
  margin-top: 18px;
  font-size: 24px;
  font-weight: 900;
}

#nextLevelBtn {
  margin-top: 15px;
  padding: 14px 28px;
  border-radius: 22px;
  border: none;
  background: linear-gradient(90deg, #22c55e, #4ade80);
  color: white;
  font-size: 22px;
  font-weight: 900;
  cursor: pointer;
  box-shadow: 0 0 25px rgba(34,197,94,.8);
}

.back-btn {
  position: absolute;
  left: 50px;
  top: 120px;
  color: white;
  text-decoration: none;
  background: rgba(76,29,149,.85);
  border: 2px solid #7c3aed;
  padding: 12px 24px;
  border-radius: 24px;
  font-size: 20px;
  font-weight: bold;
  z-index: 5;
}

@keyframes pop {
  from { transform: scale(.85); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
</style>
</head>

<body>

<section id="splash">
  <div class="splash-card">
    
    <h1>Addition Quiz</h1>
    <p>Let’s add and have fun!</p>
  </div>
</section>

<div class="space-page">
  <div class="moon-ground"></div>

  <a href="practiceadd.html" class="back-btn">← Back</a>

  <main class="game-wrap">
    <header class="topbar">
      <div class="logo">
        <h2>SpaceSpark</h2>
        <p>Learn • Explore • Grow</p>
      </div>

      <div class="score-pill">⭐ <span id="score">0</span></div>
    </header>

    <section class="title">
      <p>Numbers Planet</p>
      <h1>Addition</h1>
      <p>Let’s add and have fun!</p>
    </section>

    <section class="game-card">
      <div class="level_2">
        <span>⭐ Level <span id="levelNumber">2</span></span>
        <div class="progress">
          <div id="progressFill" class="progress-fill"></div>
        </div>
        <span id="levelText">0/5</span>
      </div>

      <div class="question">
        <div class="number-box" id="num1"></div>
        <div class="operator">+</div>
        <div class="number-box" id="num2"></div>
        <div class="operator">=</div>
        <div class="number-box qmark">?</div>
      </div>

      <div class="instruction">Choose the correct answer</div>
      <div class="options" id="options"></div>
      <div class="message" id="message"></div>
    </section>
  </main>
</div>

<script>
let num1, num2, correct;
let score = 0;
let level = 2;
let questionCount = 0;
let gameFinished = false;

function generateQuestion() {
  if (gameFinished) return;

  num1 = Math.floor(Math.random() * 9) + 1;
  num2 = Math.floor(Math.random() * 9) + 1;
  correct = num1 + num2;

  document.getElementById("num1").textContent = num1;
  document.getElementById("num2").textContent = num2;

  let answers = [correct];

  while (answers.length < 4) {
    let wrong = Math.floor(Math.random() * 18) + 1;
    if (!answers.includes(wrong)) {
      answers.push(wrong);
    }
  }

  answers.sort(() => Math.random() - 0.5);

  const options = document.getElementById("options");
  options.innerHTML = "";

  answers.forEach(answer => {
    const btn = document.createElement("button");
    btn.className = "option";
    btn.textContent = answer;
    btn.onclick = () => checkAnswer(answer);
    options.appendChild(btn);
  });
}

function checkAnswer(answer) {
  if (gameFinished) return;

  const message = document.getElementById("message");

  if (answer === correct) {
    score += 5;
    questionCount++;

    message.textContent = "Correct! Great job!";
    let correctSound = new Audio("correct.MP4");
    correctSound.play();

    message.style.color = "#22c55e";

    document.getElementById("score").textContent = score;
    document.getElementById("levelText").textContent = questionCount + "/5";
    document.getElementById("progressFill").style.width = (questionCount * 10) + "%";

    fetch("add_points.php", {
      method: "POST"
    })
    .then(res => res.text())
    .then(points => {
      console.log("New total points:", points);
    });

   
if (questionCount === 5) {
  gameFinished = true;

  // نخفي السؤال
  document.querySelector(".question").style.display = "none";

  // نخفي النص (Choose the correct answer)
  document.querySelector(".instruction").style.display = "none";

  // نخفي الخيارات
  document.getElementById("options").style.display = "none";

  // نخلي البار فل
  document.getElementById("progressFill").style.width = "100%";

  // رسالة الفوز
  message.innerHTML = `
    <div style="text-align:center;">
      <h2 style="color:#22c55e;">🎉 You Win! 🎉</h2>
      <p style="color:white;">You can go to the next level now!</p>
      <button id="nextLevelBtn">Next Level</button>
    </div>
  `;

  document.getElementById("nextLevelBtn").onclick = () => {
    window.location.href = "levelsub.html";
  };

  return;
}

if (questionCount < 5) {
    setTimeout(generateQuestion, 650);
}

  } else {
    message.textContent = "Try again!";
    let wrongSound = new Audio("tryagain.MP4");
    wrongSound.play();
    message.style.color = "#f97316";
  }
}

window.onload = () => {
  setTimeout(() => {
    document.getElementById("splash").style.display = "none";
    generateQuestion();
  }, 1800);
};
</script>

</body>
</html>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Numbers Planet - Basic Level</title>

<style>

body {
  text-align: center;
  font-family: Arial;
  background: linear-gradient(#0b1a3a, #1e2a5a);
  color: white;
  margin: 0;
}

.page {
  display: none;
}

.page.active {
  display: block;
}

.grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 15px;
  padding: 20px;
}

.card {
  background: #ffffff20;
  padding: 10px;

  background:
    linear-gradient(rgba(10, 7, 35, 0.25), rgba(10, 7, 35, 0.35)),
    url("background.png") center/cover no-repeat;

  border-radius: 15px;
  transition: 0.3s;
}

.card:hover {
  transform: scale(1.03);
}

.card img {
  height: 250px;
  width: 200px;
}

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

#im {
  padding-top: 20px;
  height: 200px;
  width: 150px;
}

/* اللوقو */
.logo h2 {
  margin: 0;
  font-size: 28px;
  padding-left: 40px;
  padding-top: 25px;
}

.logo p {
  margin: 0;
  font-size: 14px;
  opacity: 0.8;
  padding-left: 40px;
}

/* الأزرار */
.buttons {
  display: flex;
  justify-content: space-between;
  padding: 40px 80px;
}

button{
  padding: 18px 45px;
  border: 3px solid #8b5cf6;
  border-radius: 50px;
  cursor: pointer;

  background: linear-gradient(180deg, #7c3aed, #5b21b6);

  color: white;
  font-size: 28px;
  font-weight: bold;

  box-shadow:
    0 0 15px rgba(139,92,246,0.8),
    inset 0 0 10px rgba(255,255,255,0.2);

  transition: 0.3s;

  margin-top: 40px;
}

button:hover{
  transform: scale(1.05);

  box-shadow:
    0 0 25px rgba(139,92,246,1),
    inset 0 0 15px rgba(255,255,255,0.3);
}

.sound-btn{
  font-size: 22px;
  padding: 10px 18px;
  margin-top: 10px;
}

</style>
</head>

<body>

<header class="topbar">

  <div class="logo">
    <h2>SpaceSpark</h2>
    <p>Learn • Explore • Grow</p>
  </div>

</header>

<h1>🪐 Basic Level</h1>

<!-- الصفحة الأولى -->
<div id="page1" class="page active">

  <div class="grid">

    <div class="card">
      <img src="1.png">
      <p>One</p>
      <button class="sound-btn" onclick="playSound('one.MP4')">🔊</button>
    </div>

    <div class="card">
      <img src="2.png">
      <p>Two</p>
      <button class="sound-btn" onclick="playSound('two.MP4')">🔊</button>
    </div>

    <div class="card">
      <img src="3.png">
      <p>Three</p>
      <button class="sound-btn" onclick="playSound('three.MP4')">🔊</button>
    </div>

    <div class="card">
      <img src="4.png">
      <p>Four</p>
      <button class="sound-btn" onclick="playSound('four.MP4')">🔊</button>
    </div>

    <div class="card">
      <img src="5.png">
      <p>Five</p>
      <button class="sound-btn" onclick="playSound('five.MP4')">🔊</button>
    </div>

  </div>

  <div class="buttons">

    <div></div>

    <button onclick="nextPage()">Next →</button>

  </div>

</div>


<!-- الصفحة الثانية -->
<div id="page2" class="page">

  <div class="grid">

    <div class="card">
      <img src="6.png">
      <p>Six</p>
      <button class="sound-btn" onclick="playSound('six.MP4')">🔊</button>
    </div>

    <div class="card">
      <img src="7.png">
      <p>Seven</p>
      <button class="sound-btn" onclick="playSound('seven.MP4')">🔊</button>
    </div>

    <div class="card">
      <img src="8.png">
      <p>Eight</p>
      <button class="sound-btn" onclick="playSound('eighth.MP4')">🔊</button>
    </div>

    <div class="card">
      <img id="im" src="9.png">
      <p>Nine</p>
      <button class="sound-btn" onclick="playSound('nine.MP4')">🔊</button>
    </div>

    <div class="card">
      <img id="im" src="10.png">
      <p>Ten</p>
      <button class="sound-btn" onclick="playSound('ten.MP4')">🔊</button>
    </div>

  </div>

  <div class="buttons">

    <button onclick="prevPage()">← Back</button>

    <a href="basicquize.html">
      <button>Next →</button>
    </a>

  </div>

</div>


<script>

function nextPage() {

  document.getElementById("page1").classList.remove("active");
  document.getElementById("page2").classList.add("active");

}

function prevPage() {

  document.getElementById("page2").classList.remove("active");
  document.getElementById("page1").classList.add("active");

}

function playSound(file) {

  let audio = new Audio(file);
  audio.play();

}

</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Basic Quiz</title>

<style>

body {
  background: linear-gradient(#0b1a3a, #1e2a5a);
  color: white;
  text-align: center;
  font-family: Arial;
  margin: 0;
}

/* اللوقو */
.logo h2 {
  margin: 0;
  font-size: 38px;
}

.logo p {
  margin: 0;
  font-size: 16px;
  opacity: 0.8;
}

.topbar {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  padding: 20px 40px;
}

/* الكروت */
.cards {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-top: 40px;
}

.card {
  width: 120px;
  height: 150px;
  background: linear-gradient(#1e3a8a, #312e81);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 0 15px #6366f1;
  margin-top: 100px;
  margin-right: 30px;
  cursor: pointer;
  transition: 0.3s;
}

.card:hover {
  transform: scale(1.05);
}

/* الأرقام */
.numbers {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 40px;
}

.num {
  width: 80px;
  height: 80px;
  background: #7c3aed;
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 30px;
  margin-top: 150px;
  transition: 0.3s;
}

.num:hover {
  transform: scale(1.05);
}

.selected {
  border: 3px solid yellow;
}

/* الرسالة */
#message {
  margin-top: 20px;
  font-size: 24px;
  font-weight: bold;
  text-align: center;
}

/* الأزرار */
.buttons {
  display: flex;
  justify-content: space-between;
  padding: 40px 80px;
}

button {
  padding: 18px 45px;
  border: 3px solid #8b5cf6;
  border-radius: 50px;
  cursor: pointer;

  background: linear-gradient(180deg, #7c3aed, #5b21b6);

  color: white;
  font-size: 28px;
  font-weight: bold;

  box-shadow:
    0 0 15px rgba(139,92,246,0.8),
    inset 0 0 10px rgba(255,255,255,0.2);

  transition: 0.3s;
}

button:hover {
  transform: scale(1.05);

  box-shadow:
    0 0 25px rgba(139,92,246,1),
    inset 0 0 15px rgba(255,255,255,0.3);
}

#finishBtn {
  display: none;
}

#im1 {
  width: 100px;
  height: 130px;
}

</style>
</head>

<body>

<header class="topbar">
  <div class="logo">
    <h2>SpaceSpark</h2>
    <p>Learn • Explore • Grow</p>
  </div>
</header>

<div class="game1">

<!-- الكروت -->
<div class="cards">

  <div class="card" data-value="6">
    <img src="6p.png" width="300" height="300">
  </div>

  <div class="card" data-value="9">
    <img src="9p.png" width="280" height="280">
  </div>

  <div class="card" data-value="10">
    <img src="10p.png" width="280" height="280">
  </div>

  <div class="card" data-value="8">
    <img src="8p.png" width="300" height="300">
  </div>

  <div id="im1" class="card" data-value="7">
    <img src="7p.png" width="280" height="280" style="margin-left:20px; margin-top:10px;">
  </div>

</div>

<!-- الأرقام -->
<div class="numbers">

  <div class="num" data-value="6">6</div>
  <div class="num" data-value="7">7</div>
  <div class="num" data-value="8">8</div>
  <div class="num" data-value="9">9</div>
  <div class="num" data-value="10">10</div>

</div>

<p id="message"></p>

</div>

<!-- الأزرار -->
<div class="buttons">

  <a href="basicquize.html">
    <button>← Back</button>
  </a>

  <a href="leveladd.html">
    <button>Done ✓</button>
  </a>

</div>

<script>

// الرقم المختار
let selectedNumber = null;

// عدد الإجابات الصح
let correctCount = 0;


// اختيار الرقم
document.querySelectorAll(".num").forEach(num => {

  num.addEventListener("click", () => {

    // إزالة التحديد
    document.querySelectorAll(".num").forEach(n => {
      n.classList.remove("selected");
    });

    // تحديد الرقم
    num.classList.add("selected");

    // تخزين الرقم
    selectedNumber = num.dataset.value;

  });

});


// اختيار الكرت
document.querySelectorAll(".card").forEach(card => {

  card.addEventListener("click", () => {

    const msg = document.getElementById("message");

    // إذا ما اختار رقم
    if (!selectedNumber) {

      msg.innerText = "Choose a number first";
      msg.style.color = "orange";

      setTimeout(() => {
        msg.innerText = "";
      }, 2000);

      return;
    }


    // إذا الإجابة صح
    if (selectedNumber == card.dataset.value) {

      // صوت الصح
      let correctSound = new Audio("correct.MP4");
      correctSound.play();

      card.innerHTML = "✔️";
      card.style.background = "green";

      msg.innerText = "Great job! ⭐";
      msg.style.color = "#00ff99";

      // زيادة عدد الصح
      correctCount++;

      // إذا خلص كل الكروت
      if (correctCount === 5) {

        // فتح مرحلة Addition
        localStorage.setItem("addition", "unlocked");

        // إظهار زر النكست
        document.getElementById("finishBtn").style.display = "inline-block";

      }

      setTimeout(() => {
        msg.innerText = "";
      }, 2000);

    }

    // إذا غلط
    else {

      // صوت الغلط
      let wrongSound = new Audio("tryagain.MP4");
      wrongSound.play();

      msg.innerText = "Try Again";
      msg.style.color = "red";

      setTimeout(() => {
        msg.innerText = "";
      }, 2000);

    }

  });

});


// الانتقال للـ Levels

document.getElementById("finishBtn").onclick = function () {

  window.location.href = "leveladd.html";

};

</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Basic Quiz</title>

<style>

body {
  background: linear-gradient(#0b1a3a, #1e2a5a);
  color: white;
  text-align: center;
  font-family: Arial;
  margin: 0;
}

/* اللوقو */
.logo h2 {
  margin: 0;
  font-size: 28px;
}

.logo p {
  margin: 0;
  font-size: 14px;
  opacity: 0.8;
}

.topbar {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  padding: 20px 40px;
}

/* الكروت */
.cards {
  display: flex;
  justify-content: center;
  gap: 20px;
  margin-top: 40px;
}

.card {
  width: 120px;
  height: 150px;
  background: linear-gradient(#1e3a8a, #312e81);
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 25px;
  box-shadow: 0 0 15px #6366f1;
  margin-top: 100px;
  margin-right: 30px;
  cursor: pointer;
  transition: 0.3s;
}

.card:hover {
  transform: scale(1.05);
}

/* الأرقام */
.numbers {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-top: 40px;
}

.num {
  width: 80px;
  height: 80px;
  background: #7c3aed;
  border-radius: 15px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 30px;
  margin-top: 150px;
  transition: 0.3s;
}

.num:hover {
  transform: scale(1.05);
}

.selected {
  border: 3px solid yellow;
}

/* الرسالة */
#message {
  margin-top: 20px;
  font-size: 24px;
  font-weight: bold;
  color: #ff4d4d;
  text-align: center;
}

#im1 {
  width: 100px;
  height: 130px;
}

/* الأزرار */
.buttons {
  display: flex;
  justify-content: space-between;
  padding: 40px 80px;
}

button {
  padding: 18px 45px;
  border: 3px solid #8b5cf6;
  border-radius: 50px;
  cursor: pointer;

  background: linear-gradient(180deg, #7c3aed, #5b21b6);

  color: white;
  font-size: 28px;
  font-weight: bold;

  box-shadow:
    0 0 15px rgba(139,92,246,0.8),
    inset 0 0 10px rgba(255,255,255,0.2);

  transition: 0.3s;
}

button:hover {
  transform: scale(1.05);

  box-shadow:
    0 0 25px rgba(139,92,246,1),
    inset 0 0 15px rgba(255,255,255,0.3);
}

</style>
</head>

<body>

<header class="topbar">
  <div class="logo">
    <h2>SpaceSpark</h2>
    <p>Learn • Explore • Grow</p>
  </div>
</header>

<div class="game1 page active">

  <!-- الأوراق -->
  <div class="cards">

    <div class="card" data-value="3">
      <img src="3p.png" alt="" width="300" height="300">
    </div>

    <div class="card" data-value="5">
      <img src="5p.png" alt="" width="300" height="300">
    </div>

    <div class="card" data-value="2">
      <img src="2p.png" alt="" width="300" height="300">
    </div>

    <div class="card" data-value="4">
      <img src="4p.png" alt="" width="280" height="280">
    </div>

    <div class="card" data-value="1">
      <img src="1p.png" alt="" width="280" height="280">
    </div>

  </div>

  <!-- الأرقام -->
  <div class="numbers">

    <div class="num" data-value="1">1</div>
    <div class="num" data-value="2">2</div>
    <div class="num" data-value="3">3</div>
    <div class="num" data-value="4">4</div>
    <div class="num" data-value="5">5</div>

  </div>

  <p id="message"></p>

</div>

<!-- الأزرار -->
<div class="buttons">

  <a href="BasicLevel.html">
    <button>← Back</button>
  </a>

  <a href="basicquiz1.html">
    <button>Next →</button>
  </a>

</div>

<script>

// الرقم المختار
let selectedNumber = null;

// اختيار الرقم
document.querySelectorAll(".num").forEach(num => {

  num.addEventListener("click", () => {

    // إزالة التحديد
    document.querySelectorAll(".num").forEach(n => {
      n.classList.remove("selected");
    });

    // تحديد الرقم
    num.classList.add("selected");

    // تخزين الرقم
    selectedNumber = num.dataset.value;

  });

});


// اختيار الكرت
document.querySelectorAll(".card").forEach(card => {

  card.addEventListener("click", () => {

    const msg = document.getElementById("message");

    // إذا ما اختار رقم
    if (!selectedNumber) {

      msg.innerText = "Choose a number first";
      msg.style.color = "orange";

      setTimeout(() => {
        msg.innerText = "";
      }, 2000);

      return;
    }

    // إذا الإجابة صح
    if (selectedNumber == card.dataset.value) {

      // تشغيل صوت الصح
      let correctSound = new Audio("correct.MP4");
      correctSound.play();

      card.innerHTML = "✔️";
      card.style.background = "green";

      msg.innerText = "Great job! ⭐";
      msg.style.color = "#00ff99";

      setTimeout(() => {
        msg.innerText = "";
      }, 2000);

    }

    // إذا غلط
    else {

      // تشغيل صوت الغلط
      let wrongSound = new Audio("tryagain.MP4");
      wrongSound.play();

      msg.innerText = "Try Again";
      msg.style.color = "red";

      setTimeout(() => {
        msg.innerText = "";
      }, 2000);

    }

  });

});

</script>

</body>
</html>
<?php
session_start();
$totalPoints = $_SESSION["points"] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Division</title>

<style>
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family: "Trebuchet MS", Arial, sans-serif;
  color: white;
  background: #050b25;
  overflow: hidden;
}

/* خلفية فضاء */
.space-page {
  min-height: 100vh;
  position: relative;
  background:
    radial-gradient(circle at 20% 25%, rgba(124, 58, 237, .45), transparent 25%),
    radial-gradient(circle at 85% 20%, rgba(0, 183, 255, .35), transparent 22%),
    linear-gradient(180deg, #05091f 0%, #071943 55%, #170035 100%);
}

/* نجوم */
.space-page::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(white 1px, transparent 1px),
    radial-gradient(#ffd76b 1.3px, transparent 1.3px),
    radial-gradient(#7dd3fc 1px, transparent 1px);
  background-size: 70px 70px, 130px 130px, 180px 180px;
  opacity: .8;
}

/* صورة/سطح فضاء تحت */
.moon-ground {
  position: absolute;
  bottom: -90px;
  left: -5%;
  width: 110%;
  height: 250px;
  border-radius: 50% 50% 0 0;
  background:
    radial-gradient(circle at 20% 45%, #4b465b 0 18px, transparent 19px),
    radial-gradient(circle at 45% 35%, #5f5870 0 24px, transparent 25px),
    radial-gradient(circle at 70% 50%, #454056 0 20px, transparent 21px),
    radial-gradient(circle at 85% 30%, #696176 0 18px, transparent 19px),
    linear-gradient(180deg, #bcb5c9, #756d86);
  box-shadow: inset 0 25px 45px rgba(255,255,255,.25);
  z-index: 1;
}

/* سبلاش */
#splash {
  position: fixed;
  inset: 0;
  z-index: 20;
  background:
    radial-gradient(circle at 30% 20%, #4c1d95, transparent 25%),
    linear-gradient(180deg, #020617, #071b45);
  display: flex;
  justify-content: center;
  align-items: center;
}

.splash-card {
  text-align: center;
  padding: 50px 80px;
  border-radius: 35px;
  background: rgba(39, 12, 92, .65);
  border: 2px solid #a855f7;
  box-shadow: 0 0 50px rgba(168,85,247,.9);
  animation: pop .8s ease;
}


.splash-card h1 {
  font-size: 58px;
  margin: 0;
}

.splash-card p {
  font-size: 24px;
}

/* الصفحة الرئيسية */
.game-wrap {
  position: relative;
  z-index: 3;
  min-height: 100vh;
  padding: 35px 60px;
}

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo h2 {
  margin: 0;
  font-size: 32px;
}

.logo p {
  margin: 0;
  opacity: .9;
}

.score-pill {
  background: rgba(76, 29, 149, .85);
  border: 2px solid #7c3aed;
  border-radius: 28px;
  padding: 14px 28px;
  font-size: 30px;
  font-weight: 900;
  box-shadow: 0 0 25px rgba(124,58,237,.6);
}

.title {
  text-align: center;
  margin-top: 35px;
}

.title h1 {
  font-size: 76px;
  margin: 0;
  text-shadow: 0 0 20px rgba(255,255,255,.35);
}

.title p {
  font-size: 28px;
  margin: 8px 0;
}

/* كرت اللعبة */
.game-card {
  width: 760px;
  margin: 35px auto 0;
  padding: 28px 36px 35px;
  border-radius: 36px;
  background: rgba(20, 8, 65, .78);
  border: 3px solid #a855f7;
  box-shadow:
    0 0 45px rgba(168,85,247,.75),
    inset 0 0 40px rgba(59,130,246,.16);
}

.level_5 {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 18px;
  font-size: 22px;
  font-weight: 800;
  margin-bottom: 28px;
}

.progress {
  width: 260px;
  height: 18px;
  background: rgba(255,255,255,.2);
  border-radius: 20px;
  overflow: hidden;
  border: 2px solid rgba(255,255,255,.25);
}

.progress-fill {
  height: 100%;
  width: 10%;
  background: linear-gradient(90deg, #84cc16, #22c55e);
  border-radius: 20px;
}

.question {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 28px;
  margin-bottom: 22px;
}

.number-box {
  width: 135px;
  height: 135px;
  border-radius: 24px;
  background: linear-gradient(180deg, #fff, #e9eefc);
  color: #111827;
  display: grid;
  place-items: center;
  font-size: 78px;
  font-weight: 900;
  box-shadow: 0 10px 0 rgba(255,255,255,.35);
}

#num1 { color: #9333ea; }
#num2 { color: #22c55e; }
.qmark { color: #f97316; }

.operator {
  font-size: 78px;
  font-weight: 900;
}

.instruction {
  font-size: 24px;
  font-weight: 800;
  margin-bottom: 20px;
}

.options {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 18px;
}

.option {
  height: 95px;
  border: none;
  border-radius: 24px;
  background: linear-gradient(180deg, #ffffff, #e9eefc);
  font-size: 52px;
  font-weight: 900;
  cursor: pointer;
  box-shadow: 0 8px 0 rgba(255,255,255,.35);
  transition: .2s;
}

.option:hover {
  transform: translateY(-8px) scale(1.04);
}

.option:nth-child(1) { color: #0ea5e9; }
.option:nth-child(2) { color: #22c55e; }
.option:nth-child(3) { color: #f97316; }
.option:nth-child(4) { color: #9333ea; }

.message {
  min-height: 32px;
  margin-top: 18px;
  font-size: 24px;
  font-weight: 900;
}

.back-btn {
  position: absolute;
  left: 50px;
  top: 120px;
  color: white;
  text-decoration: none;
  background: rgba(76,29,149,.85);
  border: 2px solid #7c3aed;
  padding: 12px 24px;
  border-radius: 24px;
  font-size: 20px;
  font-weight: bold;
  z-index: 5;
}
#nextLevelBtn {
  margin-top: 15px;
  padding: 14px 28px;
  border-radius: 22px;
  border: none;
  background: linear-gradient(90deg, #22c55e, #4ade80);
  color: white;
  font-size: 22px;
  font-weight: 900;
  cursor: pointer;
  box-shadow: 0 0 25px rgba(34,197,94,.8);
}

@keyframes pop {
  from { transform: scale(.85); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
</style>
</head>

<body>

<section id="splash">
  <div class="splash-card">
    
    <h1>Division Quiz</h1> <!-- كود بداية لفل-->
    <p>Let’s divide and have fun!</p>
  </div>
</section>

<div class="space-page">
  <div class="moon-ground"></div>

  <a href="PracticeDiv.html" class="back-btn">← Back</a>

  <main class="game-wrap">
    <header class="topbar"> <!-- كود فوق باليسار-->
      <div class="logo">
        <h2>SpaceSpark</h2>
        <p>Learn • Explore • Grow</p>
      </div>

      <div class="score-pill">⭐ <span id="score"><?php echo $totalPoints; ?></span></div>

    </header>

    <section class="title">
      <p>Numbers Planet</p>
      <h1>Division</h1>
      <p>Let’s divide and have fun!</p>
    </section>

    <section class="game-card">  <!--بداية لعبة-->
      <div class="level_5">
        <span>⭐ Level 5</span>
        <div class="progress"><div id="progressFill" class="progress-fill"></div></div>
        <span id="levelText">1/5</span>
      </div>

      <div class="question">
        <div class="number-box" id="num1">3</div>
        <div class="operator">÷</div>
        <div class="number-box" id="num2">2</div>
        <div class="operator">=</div>
        <div class="number-box qmark">?</div>
      </div>

      <div class="instruction">Choose the correct answer</div>
      <div class="options" id="options"></div>
      <div class="message" id="message"></div>
    </section>
  </main>
</div>

<script>

let num1, num2, correct;
let score = <?php echo $totalPoints; ?>;
let level = 1;
let questionCount = 0;
let gameFinished = false;

function generateQuestion() {
  if (gameFinished) return;

  correct = Math.floor(Math.random() * 10) + 1;
  num2 = Math.floor(Math.random() * 9) + 1;
  num1 = correct * num2;

  document.getElementById("num1").textContent = num1;
  document.getElementById("num2").textContent = num2;

  let answers = [correct];

  while (answers.length < 4) {
    let wrong = Math.floor(Math.random() * 12) + 1;
    if (!answers.includes(wrong)) answers.push(wrong);
  }

  answers.sort(() => Math.random() - 0.5);

  const options = document.getElementById("options");
  options.innerHTML = "";

  answers.forEach(answer => {
    const btn = document.createElement("button");
    btn.className = "option";
    btn.textContent = answer;
    btn.onclick = () => checkAnswer(answer);
    options.appendChild(btn);
  });
}

function checkAnswer(answer) {
  if (gameFinished) return;

  const message = document.getElementById("message");

  
  
  if (answer === correct) {
  
    questionCount++;

    message.textContent = "Correct! Great job!";
    let correctSound = new Audio("correct.MP4");
    correctSound.play();

    message.style.color = "#22c55e";

    fetch("add_points.php", {
        method: "POST"
    })
    .then(res => res.text())
    .then(points => {
        document.getElementById("score").textContent = points;
    });

    document.getElementById("levelText").textContent = questionCount + "/5";
    document.getElementById("progressFill").style.width = (questionCount * 10) + "%";

    if (questionCount === 5) {
      gameFinished = true;

      document.querySelector(".question").style.display = "none";
      document.querySelector(".instruction").style.display = "none";
      document.getElementById("options").innerHTML = "";
      document.getElementById("progressFill").style.width = "100%";

      message.innerHTML = `
        You completed Numbers Planet! <br> 
        now can travel to a new planet ! <br>
        <button id="nextLevelBtn">Done</button>
      `;

      document.getElementById("nextLevelBtn").onclick = () => {
        window.location.href = "levelend.html";
      };

      return;
    }

    setTimeout(generateQuestion, 650);
} else {
    message.textContent = "Try again!";
    let wrongSound = new Audio("tryagain.MP4");
    wrongSound.play();
    message.style.color = "#f97316";
}

 
}

window.onload = () => {
  setTimeout(() => {
    document.getElementById("splash").style.display = "none";
    generateQuestion();
  }, 1800);
};

</script>

</body>
</html>
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
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Addition Planet</title>

<style>/* سبلاش */
#splash {
  position: fixed;
  inset: 0;
  z-index: 20;
  background:
    radial-gradient(circle at 30% 20%, #4c1d95, transparent 25%),
    linear-gradient(180deg, #020617, #071b45);
  display: flex;
  justify-content: center;
  align-items: center;
}

.splash-card {
  text-align: center;
  padding: 50px 80px;
  border-radius: 35px;
  background: rgba(39, 12, 92, .65);
  border: 2px solid #a855f7;
  box-shadow: 0 0 50px rgba(168,85,247,.9);
  animation: pop .8s ease;
}

.splash-planet {
  width: 160px;
  height: 160px;
  margin: 0 auto 20px;
  border-radius: 50%;
  background: radial-gradient(circle at 35% 25%, #67e8f9, #0ea5e9 45%, #1d4ed8 75%);
  box-shadow: 0 0 35px #38bdf8;
  position: relative;
}

.splash-planet::before {
  content: "1 2 3";
  position: absolute;
  font-size: 34px;
  font-weight: 900;
  top: 58px;
  left: 31px;
  color: white;
  text-shadow: 0 4px #7c3aed;
}

.splash-planet::after {
  content: "";
  position: absolute;
  width: 225px;
  height: 45px;
  border: 10px solid rgba(56,189,248,.9);
  border-radius: 50%;
  left: -43px;
  top: 58px;
  transform: rotate(-12deg);
}

.splash-card h1 {
  font-size: 58px;
  margin: 0;
}

.splash-card p {
  font-size: 24px;
}
</style>
</head>

<body>

<section id="splash">
  <div class="splash-card">
    <div class="splash-planet"></div>
    <h1>Addition Level</h1>
    <p>Let’s add and have fun!</p>
  </div>
</section>

<div class="space-page">
  <div class="moon-ground"></div>

  <a href="practiceadd.html" class="back-btn">← Back</a>

  <main class="game-wrap">
    <header class="topbar">
      <div class="logo">
        <h2>SpaceSpark</h2>
        <p>Learn • Explore • Grow</p>
      </div>

      <div class="score-pill">⭐ <span id="score">120</span></div>
    </header>

    <section class="title">
      <p>Numbers Planet</p>
      <h1>Addition</h1>
      <p>Let’s add and have fun!</p>
    </section>

    <section class="game-card">
      <div class="level_2">
        <span>⭐ Level <span id="levelNumber">2</span></span>
        <div class="progress">
          <div id="progressFill" class="progress-fill"></div>
        </div>
        <span id="levelText">0/5</span>
      </div>

      <div class="question">
        <div class="number-box" id="num1"></div>
        <div class="operator">+</div>
        <div class="number-box" id="num2"></div>
        <div class="operator">=</div>
        <div class="number-box qmark">?</div>
      </div>

      <div class="instruction">Choose the correct answer</div>
      <div class="options" id="options"></div>
      <div class="message" id="message"></div>
    </section>
  </main>
</div>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Numbers Planet - Levels</title>

  <style>

    body {
      margin: 0;
      font-family: Arial;
      background: radial-gradient(circle at top, #0b1a3a, #020617);
      color: white;
      overflow-x: hidden;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      padding: 15px 25px;
    }

    .stars-box {
      background: #ffffff20;
      padding: 8px 15px;
      border-radius: 20px;
    }

    .map {
      position: relative;
      height: 80vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-around;
    }

    .path {
      position: absolute;
      width: 4px;
      height: 100%;
      background: linear-gradient(transparent, #3d22a0, transparent);
      left: 50%;
      transform: translateX(-50%);
    }

    .planet {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2;
      transition: 0.3s;
    }

    .planet-inner {
      text-align: center;
    }

    /* البيسك فقط أخضر */
    #basic {
      background: radial-gradient(circle, #4ade80, #15803d);
      box-shadow: 0 0 25px #4ade80;
      cursor: pointer;
    }

    #basic:hover {
      transform: scale(1.1);
    }

    /* اللفلات المفتوحة */
    .unlocked {
      background: radial-gradient(circle, #777f9e, #564e9c);
      box-shadow: 0 0 25px #918d94;
      cursor: pointer;
    }

    .unlocked:hover {
      transform: scale(1.1);
    }

    /* اللفلات المقفلة */
    .locked {
      background: gray;
      opacity: 0.5;
      cursor: not-allowed;
    }

    .title {
      display: block;
      font-size: 14px;
      margin-top: 5px;
    }

  </style>
</head>

<body>

<header class="topbar">

  <h1>🪐 Numbers Planet</h1>

  <div class="stars-box">
    ⭐ <span id="starsCount">0</span>
  </div>

</header>

<main class="map">

  <div class="path"></div>

  <!-- Basic -->
  <div class="planet unlocked"
       id="basic"
       onclick="goLevel('basic')">

    <div class="planet-inner">
      <span class="title">Basic</span>
    </div>

  </div>

  <!-- Addition -->
  <div class="planet unlocked"
       id="addition"
       onclick="goLevel('addition')">

    <div class="planet-inner">
      <span class="title">Addition</span>
    </div>

  </div>

  <!-- Subtraction -->
  <div class="planet locked"
       id="subtraction"
       onclick="goLevel('subtraction')">

    <div class="planet-inner">
      🔒
      <span class="title">Subtraction</span>
    </div>

  </div>

  <!-- Multiplication -->
  <div class="planet locked"
       id="multiplication"
       onclick="goLevel('multiplication')">

    <div class="planet-inner">
      🔒
      <span class="title">Multiplication</span>
    </div>

  </div>

  <!-- Division -->
  <div class="planet locked"
       id="division"
       onclick="goLevel('division')">

    <div class="planet-inner">
      🔒
      <span class="title">Division</span>
    </div>

  </div>

</main>

<script>

let stars = localStorage.getItem("stars") || 0;

document.getElementById("starsCount").innerText = stars;

let levels = {

  basic: true,

  addition: true,

  subtraction: false,

  multiplication: false,

  division: false
};

function updateUI() {

  for (let level in levels) {

    let el = document.getElementById(level);

    if (levels[level]) {

      el.classList.remove("locked");
      el.classList.add("unlocked");

      el.innerHTML = `
        <div class="planet-inner">
          <span class="title">
            ${capitalize(level)}
          </span>
        </div>
      `;

    } else {

      el.classList.remove("unlocked");
      el.classList.add("locked");

      el.innerHTML = `
        <div class="planet-inner">
          🔒
          <span class="title">
            ${capitalize(level)}
          </span>
        </div>
      `;
    }
  }
}

updateUI();

function goLevel(level) {

  if (!levels[level]) return;

  if (level === "basic") {
    window.location.href = "BasicLevel.html";
  }

  if (level === "addition") {
    window.location.href = "practiceadd.html";
  }

  if (level === "subtraction") {
    window.location.href = "PracticeSub.html";
  }

  if (level === "multiplication") {
    window.location.href = "PracticeMulti.html";
  }

  if (level === "division") {
    window.location.href = "PracticeDiv.html";
  }
}

function capitalize(text) {

  return text.charAt(0).toUpperCase() + text.slice(1);

}

</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Numbers Planet - Levels</title>

  <style>

    body {
      margin: 0;
      font-family: Arial;
      background: radial-gradient(circle at top, #0b1a3a, #020617);
      color: white;
      overflow-x: hidden;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      padding: 15px 25px;
    }

    .stars-box {
      background: #ffffff20;
      padding: 8px 15px;
      border-radius: 20px;
    }

    .map {
      position: relative;
      height: 80vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-around;
    }

    .path {
      position: absolute;
      width: 4px;
      height: 100%;
      background: linear-gradient(transparent, #3d22a0, transparent);
      left: 50%;
      transform: translateX(-50%);
    }

    .planet {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2;
      transition: 0.3s;
    }

    .planet-inner {
      text-align: center;
    }

    /* أول 4 كواكب أخضر */
    #basic,
    #addition,
    #subtraction,
    #multiplication {
      background: radial-gradient(circle, #4ade80, #15803d);
      box-shadow: 0 0 25px #4ade80;
      cursor: pointer;
    }

    #basic:hover,
    #addition:hover,
    #subtraction:hover,
    #multiplication:hover {
      transform: scale(1.1);
    }

    /* اللفلات المفتوحة */
    .unlocked {
      background: radial-gradient(circle, #777f9e, #564e9c);
      box-shadow: 0 0 25px #918d94;
      cursor: pointer;
    }

    .unlocked:hover {
      transform: scale(1.1);
    }

    /* اللفلات المقفلة */
    .locked {
      background: gray;
      opacity: 0.5;
      cursor: not-allowed;
    }

    .title {
      display: block;
      font-size: 14px;
      margin-top: 5px;
    }

  </style>
</head>

<body>

<header class="topbar">

  <h1>🪐 Numbers Planet</h1>

  <div class="stars-box">
    ⭐ <span id="starsCount">0</span>
  </div>

</header>

<main class="map">

  <div class="path"></div>

  <!-- Basic -->
  <div class="planet unlocked"
       id="basic"
       onclick="goLevel('basic')">

    <div class="planet-inner">
      <span class="title">Basic</span>
    </div>

  </div>

  <!-- Addition -->
  <div class="planet unlocked"
       id="addition"
       onclick="goLevel('addition')">

    <div class="planet-inner">
      <span class="title">Addition</span>
    </div>

  </div>

  <!-- Subtraction -->
  <div class="planet unlocked"
       id="subtraction"
       onclick="goLevel('subtraction')">

    <div class="planet-inner">
      <span class="title">Subtraction</span>
    </div>

  </div>

  <!-- Multiplication -->
  <div class="planet unlocked"
       id="multiplication"
       onclick="goLevel('multiplication')">

    <div class="planet-inner">
      <span class="title">Multiplication</span>
    </div>

  </div>

  <!-- Division -->
  <div class="planet unlocked"
       id="division"
       onclick="goLevel('division')">

    <div class="planet-inner">
      <span class="title">Division</span>
    </div>

  </div>

</main>

<script>

let stars = localStorage.getItem("stars") || 0;

document.getElementById("starsCount").innerText = stars;

let levels = {

  basic: true,

  addition: true,

  subtraction: true,

  multiplication: true,

  division: true
};

function updateUI() {

  for (let level in levels) {

    let el = document.getElementById(level);

    if (levels[level]) {

      el.classList.remove("locked");
      el.classList.add("unlocked");

      el.innerHTML = `
        <div class="planet-inner">
          <span class="title">
            ${capitalize(level)}
          </span>
        </div>
      `;

    } else {

      el.classList.remove("unlocked");
      el.classList.add("locked");

      el.innerHTML = `
        <div class="planet-inner">
          🔒
          <span class="title">
            ${capitalize(level)}
          </span>
        </div>
      `;
    }
  }
}

updateUI();

function goLevel(level) {

  if (!levels[level]) return;

  if (level === "basic") {
    window.location.href = "BasicLevel.html";
  }

  if (level === "addition") {
    window.location.href = "practiceadd.html";
  }

  if (level === "subtraction") {
    window.location.href = "PracticeSub.html";
  }

  if (level === "multiplication") {
    window.location.href = "PracticeMulti.html";
  }

  if (level === "division") {
    window.location.href = "PracticeDiv.html";
  }
}

function capitalize(text) {

  return text.charAt(0).toUpperCase() + text.slice(1);

}

</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Numbers Planet - Levels</title>

  <style>

    body {
      margin: 0;
      font-family: Arial;
      background: radial-gradient(circle at top, #0b1a3a, #020617);
      color: white;
      overflow-x: hidden;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      padding: 15px 25px;
    }

    .stars-box {
      background: #ffffff20;
      padding: 8px 15px;
      border-radius: 20px;
    }

    /* زر الهوم */

    .home-wrap {
      text-align: center;
      margin-top: 10px;
    }

    .home-btn {
      padding: 18px 45px;
      border: 3px solid #8b5cf6;
      border-radius: 50px;
      cursor: pointer;

      background: linear-gradient(180deg, #7c3aed, #5b21b6);

      color: white;
      font-size: 28px;
      font-weight: bold;
      text-decoration: none;

      box-shadow:
        0 0 15px rgba(139,92,246,0.8),
        inset 0 0 10px rgba(255,255,255,0.2);

      transition: 0.3s;
    }

    .home-btn:hover {
      transform: scale(1.05);

      box-shadow:
        0 0 25px rgba(139,92,246,1),
        inset 0 0 15px rgba(255,255,255,0.3);
    }

    /* الماب */

    .map {
      position: relative;
      height: 80vh;

      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-around;

      padding-top: 150px;
    }

    .path {
      position: absolute;
      width: 4px;
      height: 100%;
      background: linear-gradient(transparent, #3d22a0, transparent);
      left: 50%;
      transform: translateX(-50%);
    }

    /* الكواكب */

    .planet {
      width: 110px;
      height: 110px;
      border-radius: 50%;

      display: flex;
      align-items: center;
      justify-content: center;

      z-index: 2;
      transition: 0.3s;
    }

    .planet-inner {
      text-align: center;
    }

    /* كل الكواكب أخضر */

    .unlocked {
      background: radial-gradient(circle, #4ade80, #15803d);
      box-shadow: 0 0 25px #4ade80;
      cursor: pointer;
    }

    .unlocked:hover {
      transform: scale(1.1);
    }

    .title {
      display: block;
      font-size: 14px;
      margin-top: 5px;
    }

  </style>
</head>

<body>

<header class="topbar">

  <h1>🪐 Numbers Planet</h1>

  <div class="stars-box">
    ⭐ <span id="starsCount">0</span>
  </div>

</header>

<!-- زر الهوم فوق -->

<div class="home-wrap">

  <a href="index.php" class="home-btn">
     Go To Home
  </a>

</div>

<!-- الكواكب -->

<main class="map">

  <div class="path"></div>

  <!-- Basic -->
  <div class="planet unlocked"
       id="basic"
       onclick="goLevel('basic')">

    <div class="planet-inner">
      <span class="title">Basic</span>
    </div>

  </div>

  <!-- Addition -->
  <div class="planet unlocked"
       id="addition"
       onclick="goLevel('addition')">

    <div class="planet-inner">
      <span class="title">Addition</span>
    </div>

  </div>

  <!-- Subtraction -->
  <div class="planet unlocked"
       id="subtraction"
       onclick="goLevel('subtraction')">

    <div class="planet-inner">
      <span class="title">Subtraction</span>
    </div>

  </div>

  <!-- Multiplication -->
  <div class="planet unlocked"
       id="multiplication"
       onclick="goLevel('multiplication')">

    <div class="planet-inner">
      <span class="title">Multiplication</span>
    </div>

  </div>

  <!-- Division -->
  <div class="planet unlocked"
       id="division"
       onclick="goLevel('division')">

    <div class="planet-inner">
      <span class="title">Division</span>
    </div>

  </div>

</main>

<script>

let stars = localStorage.getItem("stars") || 0;

document.getElementById("starsCount").innerText = stars;


/* التنقل بين اللفلات */

function goLevel(level) {

  if (level === "basic") {
    window.location.href = "BasicLevel.html";
  }

  if (level === "addition") {
    window.location.href = "practiceadd.html";
  }

  if (level === "subtraction") {
    window.location.href = "PracticeSub.html";
  }

  if (level === "multiplication") {
    window.location.href = "PracticeMulti.html";
  }

  if (level === "division") {
    window.location.href = "PracticeDiv.html";
  }

}

</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Numbers Planet - Levels</title>

  <style>

    body {
      margin: 0;
      font-family: Arial;
      background: radial-gradient(circle at top, #0b1a3a, #020617);
      color: white;
      overflow-x: hidden;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      padding: 15px 25px;
    }

    .stars-box {
      background: #ffffff20;
      padding: 8px 15px;
      border-radius: 20px;
    }

    .map {
      position: relative;
      height: 80vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-around;
    }

    .path {
      position: absolute;
      width: 4px;
      height: 100%;
      background: linear-gradient(transparent, #3d22a0, transparent);
      left: 50%;
      transform: translateX(-50%);
    }

    .planet {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2;
      transition: 0.3s;
    }

    .planet-inner {
      text-align: center;
    }

    /* أول 3 كواكب أخضر */
    #basic,
    #addition,
    #subtraction {
      background: radial-gradient(circle, #4ade80, #15803d);
      box-shadow: 0 0 25px #4ade80;
      cursor: pointer;
    }

    #basic:hover,
    #addition:hover,
    #subtraction:hover {
      transform: scale(1.1);
    }

    /* اللفلات المفتوحة */
    .unlocked {
      background: radial-gradient(circle, #777f9e, #564e9c);
      box-shadow: 0 0 25px #918d94;
      cursor: pointer;
    }

    .unlocked:hover {
      transform: scale(1.1);
    }

    /* اللفلات المقفلة */
    .locked {
      background: gray;
      opacity: 0.5;
      cursor: not-allowed;
    }

    .title {
      display: block;
      font-size: 14px;
      margin-top: 5px;
    }

  </style>
</head>

<body>

<header class="topbar">

  <h1>🪐 Numbers Planet</h1>

  <div class="stars-box">
    ⭐ <span id="starsCount">0</span>
  </div>

</header>

<main class="map">

  <div class="path"></div>

  <!-- Basic -->
  <div class="planet unlocked"
       id="basic"
       onclick="goLevel('basic')">

    <div class="planet-inner">
      <span class="title">Basic</span>
    </div>

  </div>

  <!-- Addition -->
  <div class="planet unlocked"
       id="addition"
       onclick="goLevel('addition')">

    <div class="planet-inner">
      <span class="title">Addition</span>
    </div>

  </div>

  <!-- Subtraction -->
  <div class="planet unlocked"
       id="subtraction"
       onclick="goLevel('subtraction')">

    <div class="planet-inner">
      <span class="title">Subtraction</span>
    </div>

  </div>

  <!-- Multiplication -->
  <div class="planet unlocked"
       id="multiplication"
       onclick="goLevel('multiplication')">

    <div class="planet-inner">
      <span class="title">Multiplication</span>
    </div>

  </div>

  <!-- Division -->
  <div class="planet locked"
       id="division"
       onclick="goLevel('division')">

    <div class="planet-inner">
      🔒
      <span class="title">Division</span>
    </div>

  </div>

</main>

<script>

let stars = localStorage.getItem("stars") || 0;

document.getElementById("starsCount").innerText = stars;

let levels = {

  basic: true,

  addition: true,

  subtraction: true,

  multiplication: true,

  division: false
};

function updateUI() {

  for (let level in levels) {

    let el = document.getElementById(level);

    if (levels[level]) {

      el.classList.remove("locked");
      el.classList.add("unlocked");

      el.innerHTML = `
        <div class="planet-inner">
          <span class="title">
            ${capitalize(level)}
          </span>
        </div>
      `;

    } else {

      el.classList.remove("unlocked");
      el.classList.add("locked");

      el.innerHTML = `
        <div class="planet-inner">
          🔒
          <span class="title">
            ${capitalize(level)}
          </span>
        </div>
      `;
    }
  }
}

updateUI();

function goLevel(level) {

  if (!levels[level]) return;

  if (level === "basic") {
    window.location.href = "BasicLevel.html";
  }

  if (level === "addition") {
    window.location.href = "practiceadd.html";
  }

  if (level === "subtraction") {
    window.location.href = "PracticeSub.html";
  }

  if (level === "multiplication") {
    window.location.href = "PracticeMulti.html";
  }

  if (level === "division") {
    window.location.href = "PracticeDiv.html";
  }
}

function capitalize(text) {

  return text.charAt(0).toUpperCase() + text.slice(1);

}

</script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Numbers Planet - Levels</title>

  <style>
    body {
      margin: 0;
      font-family: Arial;
      background: radial-gradient(circle at top, #0b1a3a, #020617);
      color: white;
      overflow-x: hidden;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      padding: 15px 25px;
    }

    .stars-box {
      background: #ffffff20;
      padding: 8px 15px;
      border-radius: 20px;
    }

    .map {
      position: relative;
      height: 80vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-around;
    }

    .path {
      position: absolute;
      width: 4px;
      height: 100%;
      background: linear-gradient(transparent, #3d22a0, transparent);
      left: 50%;
      transform: translateX(-50%);
    }

    .planet {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2;
      transition: 0.3s;
    }

    .planet-inner {
      text-align: center;
    }

    .unlocked {
      background: radial-gradient(circle, #777f9e, #564e9c);
      box-shadow: 0 0 25px #918d94;
      cursor: pointer;
    }

    .unlocked:hover {
      transform: scale(1.1);
    }

    .locked {
      background: gray;
      opacity: 0.5;
      cursor: not-allowed;
    }

    .title {
      display: block;
      font-size: 14px;
      margin-top: 5px;
    }
  </style>
</head>

<body>

<header class="topbar">
  <h1>🪐 Numbers Planet</h1>

  <div class="stars-box">
    ⭐ <span id="starsCount">0</span>
  </div>
</header>

<main class="map">

  <div class="path"></div>

  <!-- Basic -->
  <div class="planet unlocked"
       id="basic"
       onclick="goLevel('basic')">

    <div class="planet-inner">
      <span class="title">Basic</span>
    </div>

  </div>

  <!-- Addition -->
  <div class="planet locked"
       id="addition"
       onclick="goLevel('addition')">

    <div class="planet-inner">
      🔒
      <span class="title">Addition</span>
    </div>

  </div>

  <!-- Subtraction -->
  <div class="planet locked"
       id="subtraction"
       onclick="goLevel('subtraction')">

    <div class="planet-inner">
      🔒
      <span class="title">Subtraction</span>
    </div>

  </div>

  <!-- Multiplication -->
  <div class="planet locked"
       id="multiplication"
       onclick="goLevel('multiplication')">

    <div class="planet-inner">
      🔒
      <span class="title">Multiplication</span>
    </div>

  </div>

  <!-- Division -->
  <div class="planet locked"
       id="division"
       onclick="goLevel('division')">

    <div class="planet-inner">
      🔒
      <span class="title">Division</span>
    </div>

  </div>

</main>

<script>

let stars = localStorage.getItem("stars") || 0;

document.getElementById("starsCount").innerText = stars;

let levels = {

  basic: true,

  addition: false,

  subtraction: false,

  multiplication: false,

  division: false
};

function updateUI() {

  for (let level in levels) {

    let el = document.getElementById(level);

    if (levels[level]) {

      el.classList.remove("locked");
      el.classList.add("unlocked");

      el.innerHTML = `
        <div class="planet-inner">
          <span class="title">
            ${capitalize(level)}
          </span>
        </div>
      `;

    } else {

      el.classList.remove("unlocked");
      el.classList.add("locked");

      el.innerHTML = `
        <div class="planet-inner">
          🔒
          <span class="title">
            ${capitalize(level)}
          </span>
        </div>
      `;
    }
  }
}

updateUI();

function goLevel(level) {

  if (!levels[level]) return;

  if (level === "basic") {
    window.location.href = "BasicLevel.html";
  }

  if (level === "addition") {
    window.location.href = "practiceadd.html";
  }

  if (level === "subtraction") {
    window.location.href = "PracticeSub.html";
  }

  if (level === "multiplication") {
    window.location.href = "PracticeMulti.html";
  }

  if (level === "division") {
    window.location.href = "PracticeDiv.html";
  }
}

function capitalize(text) {
  return text.charAt(0).toUpperCase() + text.slice(1);
}

</script>

</body>
</html>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Numbers Planet - Levels</title>

  <style>

    body {
      margin: 0;
      font-family: Arial;
      background: radial-gradient(circle at top, #0b1a3a, #020617);
      color: white;
      overflow-x: hidden;
    }

    .topbar {
      display: flex;
      justify-content: space-between;
      padding: 15px 25px;
    }

    .stars-box {
      background: #ffffff20;
      padding: 8px 15px;
      border-radius: 20px;
    }

    .map {
      position: relative;
      height: 80vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: space-around;
    }

    .path {
      position: absolute;
      width: 4px;
      height: 100%;
      background: linear-gradient(transparent, #3d22a0, transparent);
      left: 50%;
      transform: translateX(-50%);
    }

    .planet {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 2;
      transition: 0.3s;
    }

    .planet-inner {
      text-align: center;
    }

    /* البيسك والأديشن أخضر */
    #basic,
    #addition {
      background: radial-gradient(circle, #4ade80, #15803d);
      box-shadow: 0 0 25px #4ade80;
      cursor: pointer;
    }

    #basic:hover,
    #addition:hover {
      transform: scale(1.1);
    }

    /* اللفلات المفتوحة */
    .unlocked {
      background: radial-gradient(circle, #777f9e, #564e9c);
      box-shadow: 0 0 25px #918d94;
      cursor: pointer;
    }

    .unlocked:hover {
      transform: scale(1.1);
    }

    /* اللفلات المقفلة */
    .locked {
      background: gray;
      opacity: 0.5;
      cursor: not-allowed;
    }

    .title {
      display: block;
      font-size: 14px;
      margin-top: 5px;
    }

  </style>
</head>

<body>

<header class="topbar">

  <h1>🪐 Numbers Planet</h1>

  <div class="stars-box">
    ⭐ <span id="starsCount">0</span>
  </div>

</header>

<main class="map">

  <div class="path"></div>

  <!-- Basic -->
  <div class="planet unlocked"
       id="basic"
       onclick="goLevel('basic')">

    <div class="planet-inner">
      <span class="title">Basic</span>
    </div>

  </div>

  <!-- Addition -->
  <div class="planet unlocked"
       id="addition"
       onclick="goLevel('addition')">

    <div class="planet-inner">
      <span class="title">Addition</span>
    </div>

  </div>

  <!-- Subtraction -->
  <div class="planet unlocked"
       id="subtraction"
       onclick="goLevel('subtraction')">

    <div class="planet-inner">
      <span class="title">Subtraction</span>
    </div>

  </div>

  <!-- Multiplication -->
  <div class="planet locked"
       id="multiplication"
       onclick="goLevel('multiplication')">

    <div class="planet-inner">
      🔒
      <span class="title">Multiplication</span>
    </div>

  </div>

  <!-- Division -->
  <div class="planet locked"
       id="division"
       onclick="goLevel('division')">

    <div class="planet-inner">
      🔒
      <span class="title">Division</span>
    </div>

  </div>

</main>

<script>

let stars = localStorage.getItem("stars") || 0;

document.getElementById("starsCount").innerText = stars;

let levels = {

  basic: true,

  addition: true,

  subtraction: true,

  multiplication: false,

  division: false
};

function updateUI() {

  for (let level in levels) {

    let el = document.getElementById(level);

    if (levels[level]) {

      el.classList.remove("locked");
      el.classList.add("unlocked");

      el.innerHTML = `
        <div class="planet-inner">
          <span class="title">
            ${capitalize(level)}
          </span>
        </div>
      `;

    } else {

      el.classList.remove("unlocked");
      el.classList.add("locked");

      el.innerHTML = `
        <div class="planet-inner">
          🔒
          <span class="title">
            ${capitalize(level)}
          </span>
        </div>
      `;
    }
  }
}

updateUI();

function goLevel(level) {

  if (!levels[level]) return;

  if (level === "basic") {
    window.location.href = "BasicLevel.html";
  }

  if (level === "addition") {
    window.location.href = "practiceadd.html";
  }

  if (level === "subtraction") {
    window.location.href = "PracticeSub.html";
  }

  if (level === "multiplication") {
    window.location.href = "PracticeMulti.html";
  }

  if (level === "division") {
    window.location.href = "PracticeDiv.html";
  }
}

function capitalize(text) {

  return text.charAt(0).toUpperCase() + text.slice(1);

}

</script>

</body>
</html>
<?php
session_start();
$totalPoints = $_SESSION["points"] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Multiplication</title>

<style>
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family: "Trebuchet MS", Arial, sans-serif;
  color: white;
  background: #050b25;
  overflow: hidden;
}

/* خلفية فضاء */
.space-page {
  min-height: 100vh;
  position: relative;
  background:
    radial-gradient(circle at 20% 25%, rgba(124, 58, 237, .45), transparent 25%),
    radial-gradient(circle at 85% 20%, rgba(0, 183, 255, .35), transparent 22%),
    linear-gradient(180deg, #05091f 0%, #071943 55%, #170035 100%);
}

/* نجوم */
.space-page::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(white 1px, transparent 1px),
    radial-gradient(#ffd76b 1.3px, transparent 1.3px),
    radial-gradient(#7dd3fc 1px, transparent 1px);
  background-size: 70px 70px, 130px 130px, 180px 180px;
  opacity: .8;
}


/* صورة/سطح فضاء تحت */
.moon-ground {
  position: absolute;
  bottom: -90px;
  left: -5%;
  width: 110%;
  height: 250px;
  border-radius: 50% 50% 0 0;
  background:
    radial-gradient(circle at 20% 45%, #4b465b 0 18px, transparent 19px),
    radial-gradient(circle at 45% 35%, #5f5870 0 24px, transparent 25px),
    radial-gradient(circle at 70% 50%, #454056 0 20px, transparent 21px),
    radial-gradient(circle at 85% 30%, #696176 0 18px, transparent 19px),
    linear-gradient(180deg, #bcb5c9, #756d86);
  box-shadow: inset 0 25px 45px rgba(255,255,255,.25);
  z-index: 1;
}

/* سبلاش */
#splash {
  position: fixed;
  inset: 0;
  z-index: 20;
  background:
    radial-gradient(circle at 30% 20%, #4c1d95, transparent 25%),
    linear-gradient(180deg, #020617, #071b45);
  display: flex;
  justify-content: center;
  align-items: center;
}

.splash-card {
  text-align: center;
  padding: 50px 80px;
  border-radius: 35px;
  background: rgba(39, 12, 92, .65);
  border: 2px solid #a855f7;
  box-shadow: 0 0 50px rgba(168,85,247,.9);
  animation: pop .8s ease;
}


.splash-card h1 {
  font-size: 58px;
  margin: 0;
}

.splash-card p {
  font-size: 24px;
}

/* الصفحة الرئيسية */
.game-wrap {
  position: relative;
  z-index: 3;
  min-height: 100vh;
  padding: 35px 60px;
}

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo h2 {
  margin: 0;
  font-size: 32px;
}

.logo p {
  margin: 0;
  opacity: .9;
}

.score-pill {
  background: rgba(76, 29, 149, .85);
  border: 2px solid #7c3aed;
  border-radius: 28px;
  padding: 14px 28px;
  font-size: 30px;
  font-weight: 900;
  box-shadow: 0 0 25px rgba(124,58,237,.6);
}

.title {
  text-align: center;
  margin-top: 35px;
}

.title h1 {
  font-size: 76px;
  margin: 0;
  text-shadow: 0 0 20px rgba(255,255,255,.35);
}

.title p {
  font-size: 28px;
  margin: 8px 0;
}

/* كرت اللعبة */
.game-card {
  width: 760px;
  margin: 35px auto 0;
  padding: 28px 36px 35px;
  border-radius: 36px;
  background: rgba(20, 8, 65, .78);
  border: 3px solid #a855f7;
  box-shadow:
    0 0 45px rgba(168,85,247,.75),
    inset 0 0 40px rgba(59,130,246,.16);
}

.level_4 {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 18px;
  font-size: 22px;
  font-weight: 800;
  margin-bottom: 28px;
}

.progress {
  width: 260px;
  height: 18px;
  background: rgba(255,255,255,.2);
  border-radius: 20px;
  overflow: hidden;
  border: 2px solid rgba(255,255,255,.25);
}

.progress-fill {
  height: 100%;
  width: 10%;
  background: linear-gradient(90deg, #84cc16, #22c55e);
  border-radius: 20px;
}

.question {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 28px;
  margin-bottom: 22px;
}

.number-box {
  width: 135px;
  height: 135px;
  border-radius: 24px;
  background: linear-gradient(180deg, #fff, #e9eefc);
  color: #111827;
  display: grid;
  place-items: center;
  font-size: 78px;
  font-weight: 900;
  box-shadow: 0 10px 0 rgba(255,255,255,.35);
}

#num1 { color: #9333ea; }
#num2 { color: #22c55e; }
.qmark { color: #f97316; }

.operator {
  font-size: 78px;
  font-weight: 900;
}

.instruction {
  font-size: 24px;
  font-weight: 800;
  margin-bottom: 20px;
}

.options {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 18px;
}

.option {
  height: 95px;
  border: none;
  border-radius: 24px;
  background: linear-gradient(180deg, #ffffff, #e9eefc);
  font-size: 52px;
  font-weight: 900;
  cursor: pointer;
  box-shadow: 0 8px 0 rgba(255,255,255,.35);
  transition: .2s;
}

.option:hover {
  transform: translateY(-8px) scale(1.04);
}

.option:nth-child(1) { color: #0ea5e9; }
.option:nth-child(2) { color: #22c55e; }
.option:nth-child(3) { color: #f97316; }
.option:nth-child(4) { color: #9333ea; }

.message {
  min-height: 32px;
  margin-top: 18px;
  font-size: 24px;
  font-weight: 900;
}

.back-btn {
  position: absolute;
  left: 50px;
  top: 120px;
  color: white;
  text-decoration: none;
  background: rgba(76,29,149,.85);
  border: 2px solid #7c3aed;
  padding: 12px 24px;
  border-radius: 24px;
  font-size: 20px;
  font-weight: bold;
  z-index: 5;
}

#nextLevelBtn {
  margin-top: 15px;
  padding: 14px 28px;
  border-radius: 22px;
  border: none;
  background: linear-gradient(90deg, #22c55e, #4ade80);
  color: white;
  font-size: 22px;
  font-weight: 900;
  cursor: pointer;
  box-shadow: 0 0 25px rgba(34,197,94,.8);
}

#nextLevelBtn:hover {
  transform: scale(1.05);
}

@keyframes pop {
  from { transform: scale(.85); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
</style>
</head>

<body>

<section id="splash">
  <div class="splash-card">
   
    <h1>Multiplication Quiz</h1> <!-- كود بداية لفل-->
    <p>Let’s Multiply and have fun!</p>
  </div>
</section>

<div class="space-page">
  <div class="moon-ground"></div>

  <a href="PracticeMulti.html" class="back-btn">← Back</a>

  <main class="game-wrap">
    <header class="topbar"> <!-- كود فوق باليسار-->
      <div class="logo">
        <h2>SpaceSpark</h2>
        <p>Learn • Explore • Grow</p>
      </div>

<div class="score-pill">⭐ <span id="score"><?php echo $totalPoints; ?></span></div>
    </header>

    <section class="title">
      <p>Numbers Planet</p>
      <h1>Multiplication</h1>
      <p>Let’s Multiply and have fun!</p>
    </section>

    <section class="game-card">  <!--بداية لعبة-->
      <div class="level_4">
        <span>⭐ Level 4</span>
        <div class="progress"><div id="progressFill" class="progress-fill"></div></div>
        <span id="levelText">1/5</span>
      </div>

      <div class="question">
        <div class="number-box" id="num1">3</div>
        <div class="operator">X</div>
        <div class="number-box" id="num2">2</div>
        <div class="operator">=</div>
        <div class="number-box qmark">?</div>
      </div>

      <div class="instruction">Choose the correct answer</div>
      <div class="options" id="options"></div>
      <div class="message" id="message"></div>
    </section>
  </main>
</div>

<script>
let num1, num2, correct;
let score = 120;
let level = 1;
let questionCount = 0;
let gameFinished = false;

function generateQuestion() {
  if (gameFinished) return;

  num1 = Math.floor(Math.random() * 9) + 1; 
  num2 = Math.floor(Math.random() * 9) + 1;

  correct = num1 * num2;

  document.getElementById("num1").textContent = num1;
  document.getElementById("num2").textContent = num2;

  let answers = [correct];

  while (answers.length < 4) {
    let wrong = Math.floor(Math.random() * 81) + 1;
    if (!answers.includes(wrong)) answers.push(wrong);
  }

  answers.sort(() => Math.random() - 0.5);

  const options = document.getElementById("options");
  options.innerHTML = "";

  answers.forEach(answer => {
    const btn = document.createElement("button");
    btn.className = "option";
    btn.textContent = answer;
    btn.onclick = () => checkAnswer(answer);
    options.appendChild(btn);
  });
}

function checkAnswer(answer) {
  if (gameFinished) return;

  const message = document.getElementById("message");

  if (answer === correct) {
 
    questionCount++;

    message.textContent = "Correct! Great job!";
    let correctSound = new Audio("correct.MP4");
      correctSound.play();

    message.style.color = "#22c55e";

    fetch("add_points.php", {
  method: "POST"
})
.then(res => res.text())
.then(points => {
  document.getElementById("score").textContent = points;
});
    document.getElementById("levelText").textContent = questionCount + "/5";
    document.getElementById("progressFill").style.width = (questionCount * 10) + "%";

    //نفس حق الادشن
   if (questionCount === 5) {
  gameFinished = true;

  document.querySelector(".question").style.display = "none";
  document.querySelector(".instruction").style.display = "none";
  document.getElementById("options").style.display = "none";

  document.getElementById("progressFill").style.width = "100%";

  message.innerHTML = `
    <div style="text-align:center;">
      <h2 style="color:#22c55e;">🎉 You Win! 🎉</h2>
      <p style="color:white;">You can go to the next level now!</p>
      <button id="nextLevelBtn">Next Level</button>
    </div>
  `;

  document.getElementById("nextLevelBtn").onclick = () => {
    window.location.href = "leveldiv.html";
  };

  return;
}

if (questionCount < 5) {
  setTimeout(generateQuestion, 650);
}

  } else {
    message.textContent = "Try again!";
    let wrongSound = new Audio("tryagain.MP4");
    wrongSound.play();
    message.style.color = "#f97316";
  }
}

window.onload = () => {
  setTimeout(() => {
    document.getElementById("splash").style.display = "none";
    generateQuestion();
  }, 1800);
};
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Addition Practice</title>

<style>
* { box-sizing: border-box; }

body {
  margin: 0;
  font-family: "Trebuchet MS", Arial, sans-serif;
  color: white;
  background: #050b25;
  overflow: hidden;
}

.space-page {
  min-height: 100vh;
  position: relative;
  background:
    radial-gradient(circle at 20% 25%, rgba(124, 58, 237, .45), transparent 25%),
    radial-gradient(circle at 85% 20%, rgba(0, 183, 255, .35), transparent 22%),
    linear-gradient(180deg, #05091f 0%, #071943 55%, #170035 100%);
}

.space-page::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(white 1px, transparent 1px),
    radial-gradient(#ffd76b 1.3px, transparent 1.3px),
    radial-gradient(#7dd3fc 1px, transparent 1px);
  background-size: 70px 70px, 130px 130px, 180px 180px;
  opacity: .8;
}

/* المعدل */
.moon-ground {
  position: absolute;
  bottom: -115px;
  left: -6%;
  width: 112%;
  height: 285px;
  border-radius: 50% 50% 0 0;
  background:
    radial-gradient(circle at 8% 68%, rgba(82, 76, 105, .55) 0 22px, transparent 24px),
    radial-gradient(circle at 18% 58%, rgba(95, 88, 120, .65) 0 28px, transparent 31px),
    radial-gradient(circle at 31% 67%, rgba(75, 70, 95, .5) 0 20px, transparent 23px),
    radial-gradient(circle at 45% 45%, rgba(107, 100, 130, .7) 0 30px, transparent 33px),
    radial-gradient(circle at 58% 63%, rgba(78, 72, 98, .5) 0 18px, transparent 21px),
    radial-gradient(circle at 72% 52%, rgba(98, 90, 118, .6) 0 25px, transparent 28px),
    radial-gradient(circle at 86% 66%, rgba(72, 66, 92, .5) 0 21px, transparent 24px),
    linear-gradient(180deg, #d5d0df 0%, #b6afc7 45%, #857d98 100%);
  box-shadow:
    inset 0 28px 45px rgba(255,255,255,.28),
    inset 0 -18px 35px rgba(52,45,74,.35);
  z-index: 1;
}

#splash {
  position: fixed;
  inset: 0;
  z-index: 20;
  background: linear-gradient(180deg, #020617, #071b45);
  display: flex;
  justify-content: center;
  align-items: center;
}

.splash-card {
  text-align: center;
  padding: 50px 80px;
  border-radius: 35px;
  background: rgba(39, 12, 92, .65);
  border: 2px solid #a855f7;
  box-shadow: 0 0 50px rgba(168,85,247,.9);
}

.splash-card h1 { font-size: 58px; margin: 0; }
.splash-card p { font-size: 24px; }

.game-wrap {
  position: relative;
  z-index: 3;
  min-height: 100vh;
  padding: 35px 60px;
}

.back-btn {
  position: absolute;
  left: 50px;
  top: 120px;
  color: white;
  text-decoration: none;
  background: rgba(76,29,149,.85);
  border: 2px solid #7c3aed;
  padding: 12px 24px;
  border-radius: 24px;
  font-size: 20px;
  font-weight: bold;
  z-index: 5;
}

.title {
  text-align: center;
  margin-top: 35px;
}

.title h1 {
  font-size: 58px;
  margin: 0;
}

.title p {
  font-size: 22px;
  margin: 8px 0;
}

.practice-area {
  width: 980px;
  margin: 35px auto 0;
  text-align: center;
}

.question {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 28px;
  margin-bottom: 30px;
}

.star-group {
  width: 180px;
  min-height: 100px;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  color: #facc15;
  font-size: 34px;
  gap: 6px;
  padding: 8px 12px;
}

.operator {
  font-size: 60px;
  font-weight: 900;
}

.qmark {
  font-size: 70px;
  color: #f97316;
}

.instruction {
  font-size: 22px;
  font-weight: 900;
  margin-bottom: 20px;
}

.options {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  width: 650px;
  margin: 0 auto;
  gap: 18px;
}

.option {
  min-height: 75px;
  border: none;
  border-radius: 26px;
  background: rgba(255,255,255,.12);
  border: 2px solid rgba(255,255,255,.35);
  color: #facc15;
  font-size: 26px;
  cursor: pointer;
}

.option:hover {
  transform: translateY(-6px) scale(1.03);
  background: rgba(168,85,247,.28);
}

.message {
  min-height: 34px;
  margin-top: 20px;
  font-size: 24px;
  font-weight: 900;
}

.next-btn {
  margin-top: 15px;
  padding: 14px 30px;
  border: none;
  border-radius: 24px;
  background: linear-gradient(90deg, #7c3aed, #38bdf8);
  color: white;
  font-size: 22px;
  font-weight: 900;
  cursor: pointer;
}
</style>
</head>

<body>

<section id="splash">
  <div class="splash-card">
    <h1>Addition Practice</h1>
    <p>Let’s count together!</p>
  </div>
</section>

<div class="space-page">
  <div class="moon-ground"></div>

  <a href="leveladd.html" class="back-btn">← Back</a>

  <main class="game-wrap">
    <section class="title">
      <h1>Addition Practice</h1>
      <p>Count the stars and choose the same total</p>
    </section>

    <section class="practice-area">
      <div class="question">
        <div class="star-group" id="stars1"></div>
        <div class="operator">+</div>
        <div class="star-group" id="stars2"></div>
        <div class="operator">=</div>
        <div class="qmark">?</div>
      </div>

      <div class="instruction">Choose the correct group</div>
      <div class="options" id="options"></div>
      <div class="message" id="message"></div>

      <button class="next-btn" onclick="nextQuestion()">Next →</button>
    </section>
  </main>
</div>

<script>
let num1, num2, correct;
let questionCount = 0;
let maxQuestions = 5;
let answered = false;

function makeStars(number) {
  return "⭐".repeat(number);
}

function generateQuestion() {
  answered = false;

  num1 = Math.floor(Math.random() * 5) + 1;
  num2 = Math.floor(Math.random() * 5) + 1;
  correct = num1 + num2;

  document.getElementById("stars1").textContent = makeStars(num1);
  document.getElementById("stars2").textContent = makeStars(num2);
  document.getElementById("message").textContent = "";

  let answers = [correct];

  while (answers.length < 4) {
    let wrong = Math.floor(Math.random() * 10) + 1;
    if (!answers.includes(wrong)) answers.push(wrong);
  }

  answers.sort(() => Math.random() - 0.5);

  const options = document.getElementById("options");
  options.innerHTML = "";

  answers.forEach(answer => {
    const btn = document.createElement("button");
    btn.className = "option";
    btn.textContent = makeStars(answer);
    btn.onclick = () => checkAnswer(answer);
    options.appendChild(btn);
  });
}

function checkAnswer(answer) {
  if (answered) return;

  const message = document.getElementById("message");

  if (answer === correct) {
    message.textContent = "Great job!";
    message.style.color = "#22c55e";
    answered = true;
  } else {
    message.textContent = "Try again!";
    message.style.color = "#f97316";
  }
}

function nextQuestion() {
  if (!answered) {
    const message = document.getElementById("message");
    message.textContent = "Answer first!";
    message.style.color = "#f97316";
    return;
  }

  questionCount++;

  if (questionCount >= maxQuestions) {
    document.querySelector(".practice-area").innerHTML = `
      <h1>Awesome!</h1>
      <p>You finished the practice!</p>
      <button class="next-btn" onclick="goToQuiz()">Go to Quiz</button>
    `;
    return;
  }

  generateQuestion();
}

function goToQuiz() {
  window.location.href = "addQuiz.html";
}

window.onload = () => {
  setTimeout(() => {
    document.getElementById("splash").style.display = "none";
    generateQuestion();
  }, 1500);
};
</script>

</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Division Practice</title>

<style>
* { box-sizing: border-box; }

body {
  margin: 0;
  font-family: "Trebuchet MS", Arial, sans-serif;
  color: white;
  background: #050b25;
  overflow: hidden;
}

.space-page {
  min-height: 100vh;
  position: relative;
  background:
    radial-gradient(circle at 20% 25%, rgba(124, 58, 237, .45), transparent 25%),
    radial-gradient(circle at 85% 20%, rgba(0, 183, 255, .35), transparent 22%),
    linear-gradient(180deg, #05091f 0%, #071943 55%, #170035 100%);
}

.space-page::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(white 1px, transparent 1px),
    radial-gradient(#ffd76b 1.3px, transparent 1.3px),
    radial-gradient(#7dd3fc 1px, transparent 1px);
  background-size: 70px 70px, 130px 130px, 180px 180px;
  opacity: .8;
}

.moon-ground {
  position: absolute;
  bottom: -115px;
  left: -6%;
  width: 112%;
  height: 285px;
  border-radius: 50% 50% 0 0;
  background:
    radial-gradient(circle at 8% 68%, rgba(82, 76, 105, .55) 0 22px, transparent 24px),
    radial-gradient(circle at 18% 58%, rgba(95, 88, 120, .65) 0 28px, transparent 31px),
    radial-gradient(circle at 31% 67%, rgba(75, 70, 95, .5) 0 20px, transparent 23px),
    radial-gradient(circle at 45% 45%, rgba(107, 100, 130, .7) 0 30px, transparent 33px),
    radial-gradient(circle at 58% 63%, rgba(78, 72, 98, .5) 0 18px, transparent 21px),
    radial-gradient(circle at 72% 52%, rgba(98, 90, 118, .6) 0 25px, transparent 28px),
    radial-gradient(circle at 86% 66%, rgba(72, 66, 92, .5) 0 21px, transparent 24px),
    linear-gradient(180deg, #d5d0df 0%, #b6afc7 45%, #857d98 100%);
  box-shadow:
    inset 0 28px 45px rgba(255,255,255,.28),
    inset 0 -18px 35px rgba(52,45,74,.35);
  z-index: 1;
}

#splash {
  position: fixed;
  inset: 0;
  z-index: 20;
  background: linear-gradient(180deg, #020617, #071b45);
  display: flex;
  justify-content: center;
  align-items: center;
}

.splash-card {
  text-align: center;
  padding: 50px 80px;
  border-radius: 35px;
  background: rgba(39, 12, 92, .65);
  border: 2px solid #a855f7;
  box-shadow:
    0 0 25px rgba(168,85,247,.7),
    0 0 60px rgba(168,85,247,.6),
    0 0 100px rgba(168,85,247,.5);
}

.splash-card h1 {
  font-size: 58px;
  margin: 0;
}

.splash-card p {
  font-size: 24px;
}

.game-wrap {
  position: relative;
  z-index: 3;
  min-height: 100vh;
  padding: 35px 60px;
}

.back-btn {
  position: absolute;
  left: 50px;
  top: 120px;
  color: white;
  text-decoration: none;
  background: rgba(76,29,149,.85);
  border: 2px solid #7c3aed;
  padding: 12px 24px;
  border-radius: 24px;
  font-size: 20px;
  font-weight: bold;
  z-index: 5;
}

.title {
  text-align: center;
  margin-top: 35px;
}

.title h1 {
  font-size: 58px;
  margin: 0;
}

.title p {
  font-size: 22px;
  margin: 8px 0;
}

.practice-area {
  width: 980px;
  margin: 35px auto 0;
  text-align: center;
}

.question {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 22px;
  margin-bottom: 30px;
}

.star-group {
  width: 180px;
  min-height: 100px;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  color: #facc15;
  font-size: 34px;
  gap: 6px;
  padding: 8px 12px;
}

.total-stars {
  width: 280px;
  font-size: 30px;
}

.operator {
  width: 60px;
  font-size: 56px;
  font-weight: 900;
  line-height: 1;
  display: flex;
  justify-content: center;
  align-items: center;
  margin: 0 6px;
}

.division-sign {
  font-size: 52px;
  transform: translateY(-3px);
}

.qmark {
  font-size: 70px;
  color: #f97316;
}

.instruction {
  font-size: 22px;
  font-weight: 900;
  margin-bottom: 20px;
}

.options {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  width: 650px;
  margin: 0 auto;
  gap: 18px;
}

.option {
  min-height: 75px;
  border: none;
  border-radius: 26px;
  background: rgba(255,255,255,.12);
  border: 2px solid rgba(255,255,255,.35);
  color: #facc15;
  font-size: 26px;
  cursor: pointer;
}

.option:hover {
  transform: translateY(-6px) scale(1.03);
  background: rgba(168,85,247,.28);
}

.message {
  min-height: 34px;
  margin-top: 20px;
  font-size: 24px;
  font-weight: 900;
}

.next-btn {
  margin-top: 15px;
  padding: 14px 30px;
  border: none;
  border-radius: 24px;
  background: linear-gradient(90deg, #7c3aed, #38bdf8);
  color: white;
  font-size: 22px;
  font-weight: 900;
  cursor: pointer;
}
</style>
</head>

<body>

<section id="splash">
  <div class="splash-card">
    <h1>Division Practice</h1>
    <p>Let’s share the stars!</p>
  </div>
</section>

<div class="space-page">
  <div class="moon-ground"></div>

  <a href="leveldiv.html" class="back-btn">← Back</a>

  <main class="game-wrap">

    <section class="title">
      <h1>Division Practice</h1>
      <p>Divide the stars evenly</p>
    </section>

    <section class="practice-area">
      <div class="question">
        <div class="star-group total-stars" id="stars1"></div>
        <div class="operator division-sign">÷</div>
        <div class="star-group" id="stars2"></div>
        <div class="operator">=</div>
        <div class="qmark">?</div>
      </div>

      <div class="instruction">Choose the correct group</div>
      <div class="options" id="options"></div>
      <div class="message" id="message"></div>

      <button class="next-btn" onclick="nextQuestion()">Next →</button>
    </section>

  </main>
</div>

<script>
let num1, num2, correct;
let questionCount = 0;
let maxQuestions = 5;
let answered = false;
let usedQuestions = [];

const questions = [
  [4, 2],
  [6, 2],
  [6, 3],
  [8, 2],
  [8, 4],
  [9, 3]
];

function makeStars(number) {
  return "⭐".repeat(number);
}

function generateQuestion() {
  answered = false;

  let q;
  let key;

  do {
    q = questions[Math.floor(Math.random() * questions.length)];
    num1 = q[0];
    num2 = q[1];
    key = num1 + "÷" + num2;
  } while (usedQuestions.includes(key));

  usedQuestions.push(key);
  correct = num1 / num2;

  document.getElementById("stars1").textContent = makeStars(num1);
  document.getElementById("stars2").textContent = makeStars(num2);
  document.getElementById("message").textContent = "";

  let answers = [correct];

  while (answers.length < 4) {
    let wrong = Math.floor(Math.random() * 5) + 1;
    if (!answers.includes(wrong)) answers.push(wrong);
  }

  answers.sort(() => Math.random() - 0.5);

  const options = document.getElementById("options");
  options.innerHTML = "";

  answers.forEach(answer => {
    const btn = document.createElement("button");
    btn.className = "option";
    btn.textContent = makeStars(answer);
    btn.onclick = () => checkAnswer(answer);
    options.appendChild(btn);
  });
}

function checkAnswer(answer) {
  if (answered) return;

  const message = document.getElementById("message");

  if (answer === correct) {
    message.textContent = "Great job!";
    message.style.color = "#22c55e";
    answered = true;
  } else {
    message.textContent = "Try again!";
    message.style.color = "#f97316";
  }
}

function nextQuestion() {
  if (!answered) {
    const message = document.getElementById("message");
    message.textContent = "Answer first!";
    message.style.color = "#f97316";
    return;
  }

  questionCount++;

  if (questionCount >= maxQuestions) {
    document.querySelector(".practice-area").innerHTML = `
      <h1>Awesome!</h1>
      <p>You finished the practice!</p>
      <button class="next-btn" onclick="goToQuiz()">Go to Quiz</button>
    `;
    return;
  }

  generateQuestion();
}

function goToQuiz() {
  window.location.href = "DivisionQuiz.php";
}

window.onload = () => {
  setTimeout(() => {
    document.getElementById("splash").style.display = "none";
    generateQuestion();
  }, 1500);
};
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Multiplication Practice</title>

<style>
* { box-sizing: border-box; }

body {
  margin: 0;
  font-family: "Trebuchet MS", Arial, sans-serif;
  color: white;
  background: #050b25;
  overflow: hidden;
}

.space-page {
  min-height: 100vh;
  position: relative;
  background:
    radial-gradient(circle at 20% 25%, rgba(124, 58, 237, .45), transparent 25%),
    radial-gradient(circle at 85% 20%, rgba(0, 183, 255, .35), transparent 22%),
    linear-gradient(180deg, #05091f 0%, #071943 55%, #170035 100%);
}

.space-page::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(white 1px, transparent 1px),
    radial-gradient(#ffd76b 1.3px, transparent 1.3px),
    radial-gradient(#7dd3fc 1px, transparent 1px);
  background-size: 70px 70px, 130px 130px, 180px 180px;
  opacity: .8;
}

.moon-ground {
  position: absolute;
  bottom: -115px;
  left: -6%;
  width: 112%;
  height: 285px;
  border-radius: 50% 50% 0 0;
  background:
    radial-gradient(circle at 8% 68%, rgba(82, 76, 105, .55) 0 22px, transparent 24px),
    radial-gradient(circle at 18% 58%, rgba(95, 88, 120, .65) 0 28px, transparent 31px),
    radial-gradient(circle at 31% 67%, rgba(75, 70, 95, .5) 0 20px, transparent 23px),
    radial-gradient(circle at 45% 45%, rgba(107, 100, 130, .7) 0 30px, transparent 33px),
    radial-gradient(circle at 58% 63%, rgba(78, 72, 98, .5) 0 18px, transparent 21px),
    radial-gradient(circle at 72% 52%, rgba(98, 90, 118, .6) 0 25px, transparent 28px),
    radial-gradient(circle at 86% 66%, rgba(72, 66, 92, .5) 0 21px, transparent 24px),
    linear-gradient(180deg, #d5d0df 0%, #b6afc7 45%, #857d98 100%);
  box-shadow:
    inset 0 28px 45px rgba(255,255,255,.28),
    inset 0 -18px 35px rgba(52,45,74,.35);
  z-index: 1;
}

#splash {
  position: fixed;
  inset: 0;
  z-index: 20;
  background: linear-gradient(180deg, #020617, #071b45);
  display: flex;
  justify-content: center;
  align-items: center;
}

.splash-card {
  text-align: center;
  padding: 50px 80px;
  border-radius: 35px;
  background: rgba(39, 12, 92, .65);
  border: 2px solid #a855f7;
  box-shadow:
    0 0 25px rgba(168,85,247,.7),
    0 0 60px rgba(168,85,247,.6),
    0 0 100px rgba(168,85,247,.5);
}

.splash-card h1 {
  font-size: 58px;
  margin: 0;
}

.splash-card p {
  font-size: 24px;
}

.game-wrap {
  position: relative;
  z-index: 3;
  min-height: 100vh;
  padding: 35px 60px;
}

.back-btn {
  position: absolute;
  left: 50px;
  top: 120px;
  color: white;
  text-decoration: none;
  background: rgba(76,29,149,.85);
  border: 2px solid #7c3aed;
  padding: 12px 24px;
  border-radius: 24px;
  font-size: 20px;
  font-weight: bold;
  z-index: 5;
}

.title {
  text-align: center;
  margin-top: 35px;
}

.title h1 {
  font-size: 58px;
  margin: 0;
}

.title p {
  font-size: 22px;
  margin: 8px 0;
}

.practice-area {
  width: 980px;
  margin: 35px auto 0;
  text-align: center;
}

.question {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 28px;
  margin-bottom: 30px;
}

.star-group {
  width: 180px;
  min-height: 100px;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  color: #facc15;
  font-size: 34px;
  gap: 6px;
  padding: 8px 12px;
}

.operator {
  font-size: 60px;
  font-weight: 900;
}

.qmark {
  font-size: 70px;
  color: #f97316;
}

.instruction {
  font-size: 22px;
  font-weight: 900;
  margin-bottom: 20px;
}

.options {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  width: 650px;
  margin: 0 auto;
  gap: 18px;
}

.option {
  min-height: 75px;
  border: none;
  border-radius: 26px;
  background: rgba(255,255,255,.12);
  border: 2px solid rgba(255,255,255,.35);
  color: #facc15;
  font-size: 26px;
  cursor: pointer;
}

.option:hover {
  transform: translateY(-6px) scale(1.03);
  background: rgba(168,85,247,.28);
}

.message {
  min-height: 34px;
  margin-top: 20px;
  font-size: 24px;
  font-weight: 900;
}

.next-btn {
  margin-top: 15px;
  padding: 14px 30px;
  border: none;
  border-radius: 24px;
  background: linear-gradient(90deg, #7c3aed, #38bdf8);
  color: white;
  font-size: 22px;
  font-weight: 900;
  cursor: pointer;
}
</style>
</head>

<body>

<section id="splash">
  <div class="splash-card">
    <h1>Multiplication Practice</h1>
    <p>Let’s count together!</p>
  </div>
</section>

<div class="space-page">
  <div class="moon-ground"></div>

  <a href="levelmul.html" class="back-btn">← Back</a>

  <main class="game-wrap">

    <section class="title">
      <h1>Multiplication Practice</h1>
      <p>Multiply the stars and choose the correct answer</p>
    </section>

    <section class="practice-area">
      <div class="question">
        <div class="star-group" id="stars1"></div>
        <div class="operator">×</div>
        <div class="star-group" id="stars2"></div>
        <div class="operator">=</div>
        <div class="qmark">?</div>
      </div>

      <div class="instruction">Choose the correct group</div>
      <div class="options" id="options"></div>
      <div class="message" id="message"></div>

      <button class="next-btn" onclick="nextQuestion()">Next →</button>
    </section>

  </main>
</div>

<script>
let num1, num2, correct;
let questionCount = 0;
let maxQuestions = 5;
let answered = false;
let usedQuestions = [];

const questions = [
  [2, 2],
  [2, 3],
  [3, 2],
  [3, 3],
  [2, 4],
  [4, 2]
];

function makeStars(number) {
  return "⭐".repeat(number);
}

function generateQuestion() {
  answered = false;

  let q;
  let key;

  do {
    q = questions[Math.floor(Math.random() * questions.length)];
    num1 = q[0];
    num2 = q[1];
    key = num1 + "x" + num2;
  } while (usedQuestions.includes(key));

  usedQuestions.push(key);

  correct = num1 * num2;

  document.getElementById("stars1").textContent = makeStars(num1);
  document.getElementById("stars2").textContent = makeStars(num2);
  document.getElementById("message").textContent = "";

  let answers = [correct];

  while (answers.length < 4) {
    let wrong = Math.floor(Math.random() * 8) + 2;
    if (!answers.includes(wrong)) answers.push(wrong);
  }

  answers.sort(() => Math.random() - 0.5);

  const options = document.getElementById("options");
  options.innerHTML = "";

  answers.forEach(answer => {
    const btn = document.createElement("button");
    btn.className = "option";
    btn.textContent = makeStars(answer);
    btn.onclick = () => checkAnswer(answer);
    options.appendChild(btn);
  });
}

function checkAnswer(answer) {
  if (answered) return;

  const message = document.getElementById("message");

  if (answer === correct) {
    message.textContent = "Great job!";
    message.style.color = "#22c55e";
    answered = true;
  } else {
    message.textContent = "Try again!";
    message.style.color = "#f97316";
  }
}

function nextQuestion() {
  if (!answered) {
    const message = document.getElementById("message");
    message.textContent = "Answer first!";
    message.style.color = "#f97316";
    return;
  }

  questionCount++;

  if (questionCount >= maxQuestions) {
    document.querySelector(".practice-area").innerHTML = `
      <h1>Awesome!</h1>
      <p>You finished the practice!</p>
      <button class="next-btn" onclick="goToQuiz()">Go to Quiz</button>
    `;
    return;
  }

  generateQuestion();
}

function goToQuiz() {
  window.location.href = "MultiplicationQuiz.php";
}

window.onload = () => {
  setTimeout(() => {
    document.getElementById("splash").style.display = "none";
    generateQuestion();
  }, 1500);
};
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Subtraction Practice</title>

<style>
* { box-sizing: border-box; }

body {
  margin: 0;
  font-family: "Trebuchet MS", Arial, sans-serif;
  color: white;
  background: #050b25;
  overflow: hidden;
}

.space-page {
  min-height: 100vh;
  position: relative;
  background:
    radial-gradient(circle at 20% 25%, rgba(124, 58, 237, .45), transparent 25%),
    radial-gradient(circle at 85% 20%, rgba(0, 183, 255, .35), transparent 22%),
    linear-gradient(180deg, #05091f 0%, #071943 55%, #170035 100%);
}

.space-page::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(white 1px, transparent 1px),
    radial-gradient(#ffd76b 1.3px, transparent 1.3px),
    radial-gradient(#7dd3fc 1px, transparent 1px);
  background-size: 70px 70px, 130px 130px, 180px 180px;
  opacity: .8;
}

.moon-ground {
  position: absolute;
  bottom: -90px;
  left: -5%;
  width: 110%;
  height: 250px;
  border-radius: 50% 50% 0 0;
  background: linear-gradient(180deg, #bcb5c9, #756d86);
}

#splash {
  position: fixed;
  inset: 0;
  z-index: 20;
  background: linear-gradient(180deg, #020617, #071b45);
  display: flex;
  justify-content: center;
  align-items: center;
}

.splash-card {
  text-align: center;
  padding: 50px 80px;
  border-radius: 35px;
  background: rgba(39, 12, 92, .65);
  border: 2px solid #a855f7;
  box-shadow: 0 0 50px rgba(168,85,247,.9);
}

.splash-card h1 {
  font-size: 58px;
  margin: 0;
}

.splash-card p {
  font-size: 24px;
}

.game-wrap {
  position: relative;
  z-index: 3;
  min-height: 100vh;
  padding: 35px 60px;
}

.back-btn {
  position: absolute;
  left: 50px;
  top: 120px;
  color: white;
  text-decoration: none;
  background: rgba(76,29,149,.85);
  border: 2px solid #7c3aed;
  padding: 12px 24px;
  border-radius: 24px;
  font-size: 20px;
  font-weight: bold;
  z-index: 5;
}

.title {
  text-align: center;
  margin-top: 35px;
}

.title h1 {
  font-size: 58px;
  margin: 0;
}

.title p {
  font-size: 22px;
  margin: 8px 0;
}

.practice-area {
  width: 980px;
  margin: 35px auto 0;
  text-align: center;
}

.question {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 28px;
  margin-bottom: 30px;
}

.star-group {
  width: 180px;
  min-height: 100px;
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
  color: #facc15;
  font-size: 34px;
  gap: 6px;
  padding: 8px 12px;
}

.operator {
  font-size: 60px;
  font-weight: 900;
  margin: 0 10px;
}

.qmark {
  font-size: 70px;
  color: #f97316;
  margin-left: 10px;
}

.options {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  width: 650px;
  margin: 0 auto;
  gap: 18px;
}

.option {
  min-height: 75px;
  border-radius: 26px;
  background: rgba(255,255,255,.12);
  border: 2px solid rgba(255,255,255,.35);
  color: #facc15;
  font-size: 26px;
  cursor: pointer;
}

.message {
  margin-top: 20px;
  font-size: 24px;
  font-weight: 900;
}

.next-btn {
  margin-top: 15px;
  padding: 14px 30px;
  border-radius: 24px;
  border: none;
  background: linear-gradient(90deg, #7c3aed, #38bdf8);
  color: white;
  font-size: 22px;
  cursor: pointer;
}
</style>
</head>

<body>

<section id="splash">
  <div class="splash-card">
    <h1>Subtraction Practice</h1>
    <p>Let’s count what is left!</p>
  </div>
</section>

<div class="space-page">
  <div class="moon-ground"></div>

  <a href="levelsub.html" class="back-btn">← Back</a>

  <main class="game-wrap">

    <section class="title">
      <h1>Subtraction Practice</h1>
      <p>Count the stars left</p>
    </section>

    <section class="practice-area">
      <div class="question">
        <div class="star-group" id="stars1"></div>
        <div class="operator">−</div>
        <div class="star-group" id="stars2"></div>
        <div class="operator">=</div>
        <div class="qmark">?</div>
      </div>

      <div class="options" id="options"></div>
      <div class="message" id="message"></div>

      <button class="next-btn" onclick="nextQuestion()">Next →</button>
    </section>

  </main>
</div>

<script>
let num1, num2, correct;
let questionCount = 0;
let maxQuestions = 5;
let answered = false;
let usedQuestions = [];

// أسئلة جاهزة (تمنع تكرار الناتج 1)
const questions = [
  [3,1],
  [4,1],
  [4,2],
  [5,1],
  [5,2],
  [5,3]
];

function makeStars(n) {
  return "⭐".repeat(n);
}

function generateQuestion() {
  answered = false;

  let q, key;

  do {
    q = questions[Math.floor(Math.random() * questions.length)];
    num1 = q[0];
    num2 = q[1];
    key = num1 + "-" + num2;
  } while (usedQuestions.includes(key));

  usedQuestions.push(key);

  correct = num1 - num2;

  document.getElementById("stars1").textContent = makeStars(num1);
  document.getElementById("stars2").textContent = makeStars(num2);
  document.getElementById("message").textContent = "";

  let answers = [correct];

  while (answers.length < 4) {
    let wrong = Math.floor(Math.random() * 5) + 1;
    if (!answers.includes(wrong)) answers.push(wrong);
  }

  answers.sort(() => Math.random() - 0.5);

  const options = document.getElementById("options");
  options.innerHTML = "";

  answers.forEach(a => {
    const btn = document.createElement("button");
    btn.className = "option";
    btn.textContent = makeStars(a);
    btn.onclick = () => checkAnswer(a);
    options.appendChild(btn);
  });
}

function checkAnswer(a) {
  if (answered) return;

  const msg = document.getElementById("message");

  if (a === correct) {
    msg.textContent = "Great job!";
    msg.style.color = "#22c55e";
    answered = true;
  } else {
    msg.textContent = "Try again!";
    msg.style.color = "#f97316";
  }
}

function nextQuestion() {
  if (!answered) return;

  questionCount++;

  if (questionCount >= maxQuestions) {
    document.querySelector(".practice-area").innerHTML = `
      <h1>Awesome!</h1>
      <button class="next-btn" onclick="goToQuiz()">Go to Quiz</button>
    `;
    return;
  }

  generateQuestion();
}

function goToQuiz() {
  window.location.href = "subQuiz.php";
}

window.onload = () => {
  setTimeout(() => {
    document.getElementById("splash").style.display = "none";
    generateQuestion();
  }, 1500);
};
</script>

</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SpaceSpark Profile</title>

<style>
*{
  box-sizing:border-box;
}

body{
  margin:0;
  font-family:Arial,sans-serif;
  color:white;
  min-height:100vh;
  background:
    linear-gradient(rgba(5,10,35,0.45), rgba(5,10,35,0.65)),
    url("background.png");
  background-size:cover;
  background-position:center;
  overflow-y:auto;
  overflow-x:hidden;
}

.topbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:20px 45px;
}

.logo-text h1{
  margin:0;
  font-size:30px;
}

.logo-text p{
  margin:0;
  font-size:15px;
  opacity:0.9;
}

.nav{
  background:rgba(70,20,130,0.75);
  padding:12px 24px;
  border-radius:35px;
  border:2px solid rgba(180,120,255,0.5);
  box-shadow:0 0 20px rgba(160,90,255,0.35);
}

.nav a{
  color:white;
  text-decoration:none;
  margin:0 14px;
  font-size:17px;
  font-weight:bold;
}

.nav a.active{
  background:#6d8cff;
  padding:10px 18px;
  border-radius:25px;
}

.profile-container{
  display:flex;
  justify-content:center;
  align-items:flex-start;
  padding:8px 20px 20px;
}

.profile-card{
  width:780px;
  padding:20px 22px 18px;
  border-radius:35px;
  position:relative;
  background:rgba(20,10,55,0.78);
  border:2px solid rgba(255,216,77,0.45);
  box-shadow:
    0 0 35px rgba(255,216,77,0.25),
    0 0 60px rgba(125,80,255,0.25);
  backdrop-filter:blur(12px);
  text-align:center;
}

.profile-header{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:20px;
  margin-bottom:15px;
}

.profile-info{
  display:flex;
  align-items:center;
  gap:18px;
  text-align:left;
}

.profile-img{
  width:95px;
  height:95px;
  border-radius:50%;
  border:4px solid #ffd84d;
  box-shadow:0 0 25px rgba(255,216,77,0.6);
}

.profile-card h2{
  font-size:34px;
  margin:0;
}

.role{
  font-size:16px;
  opacity:0.9;
  margin:5px 0 0;
}

.stat-box{
  width:190px;
  background:rgba(255,255,255,0.1);
  border:2px solid rgba(255,255,255,0.18);
  border-radius:24px;
  padding:14px;
}

.stat-box h3{
  margin:0;
  font-size:32px;
  color:#ffd84d;
}

.stat-box p{
  margin:6px 0 0;
  font-size:15px;
}

.planets-profile{
  display:grid;
  grid-template-columns:repeat(3,1fr);
  gap:16px;
  margin-top:12px;
}

.planet-profile-card{
  background:rgba(255,255,255,0.1);
  border:2px solid rgba(255,255,255,0.18);
  border-radius:28px;
  padding:10px;
  cursor:pointer;
  transition:0.3s;
}

.planet-profile-card:hover{
  transform:translateY(-6px) scale(1.03);
  box-shadow:0 0 25px rgba(255,216,77,0.35);
}

.planet-profile-card img{
  width:85px;
  height:85px;
  object-fit:contain;
}

.planet-profile-card h3{
  margin:6px 0 0;
  font-size:19px;
}

.planet-details{
  margin-top:16px;
  padding:16px 20px;
  border-radius:28px;
  background:rgba(255,255,255,0.1);
  border:2px solid rgba(255,255,255,0.18);
  text-align:left;
}

.planet-details h3{
  margin:0 0 8px;
  font-size:24px;
}

.planet-details p{
  font-size:15px;
  margin:6px 0;
}

.bar{
  width:100%;
  height:14px;
  background:rgba(255,255,255,0.15);
  border-radius:30px;
  overflow:hidden;
  margin-top:10px;
}

.fill{
  height:100%;
  border-radius:30px;
}

.numbers{
  background:linear-gradient(90deg,#38bdf8,#2563eb);
}

.letters{
  background:linear-gradient(90deg,#d946ef,#9333ea);
}

.animals{
  background:linear-gradient(90deg,#84cc16,#22c55e);
}

.back-btn{
  display:inline-block;
  margin-top:16px;
  padding:11px 26px;
  border-radius:35px;
  background:linear-gradient(135deg,#ffd84d,#ff9f43);
  color:#08122e;
  text-decoration:none;
  font-weight:bold;
  font-size:16px;
}
</style>
</head>

<body>

<header class="topbar">
  <div class="logo-text">
    <h1>SpaceSpark</h1>
    <p>Learn • Explore • Grow</p>
  </div>

  <nav class="nav">
    <a href="index.html">Home</a>
    <a href="profile.html" class="active">Profile</a>
  </nav>
</header>

<section class="profile-container">

<div class="profile-card">

  <div class="profile-header">

    <div class="profile-info">
      <img src="profile.png" class="profile-img">

      <div>
        <h2 id="profileName">Astronaut</h2>
        
      </div>
    </div>

    <div class="stat-box">
      <h3 id="totalStars">0</h3>
      <p>Total Stars ⭐</p>
    </div>

  </div>

  <div class="planets-profile">

    <div class="planet-profile-card" onclick="showPlanet('numbers')">
      <img src="numbers.png">
      <h3>Numbers Planet</h3>
    </div>

    <div class="planet-profile-card" onclick="showPlanet('letters')">
      <img src="letters.png">
      <h3>Letters Planet</h3>
    </div>

    <div class="planet-profile-card" onclick="showPlanet('animals')">
      <img src="animals2.png">
      <h3>Animals Planet</h3>
    </div>

  </div>

  <div class="planet-details" id="planetDetails">
    <h3>Choose a planet </h3>
    <p>Select a planet to view progress ⭐</p>
  </div>

  <a href="index.html" class="back-btn">⬅ Back Home</a>

</div>

</section>

<script>
const username = localStorage.getItem("username") || "Astronaut";

document.getElementById("profileName").innerText = username;

let totalStars = localStorage.getItem("stars") || 0;
document.getElementById("totalStars").innerText = totalStars;

fetch("get_score.php?username=" + encodeURIComponent(username))
.then(response => response.json())
.then(data => {
  document.getElementById("totalStars").innerText = data.stars;
  localStorage.setItem("stars", data.stars);
  localStorage.setItem("unlocked_level", data.unlocked_level);
})
.catch(() => {});

function showPlanet(planet){
  const box = document.getElementById("planetDetails");

  if(planet === "numbers"){
    let unlocked = Number(localStorage.getItem("unlocked_level")) || 1;
    let progress = unlocked * 20;

    box.innerHTML = `
      <h3>Numbers Planet</h3>
      <p>Highest Level: ${unlocked} / 5</p>
      <p>Progress: ${progress}%</p>
      <div class="bar">
        <div class="fill numbers" style="width:${progress}%"></div>
      </div>
    `;
  }

  if(planet === "letters"){
    box.innerHTML = `
      <h3>Letters Planet</h3>
      <p>Highest Level: 0 / 4</p>
      <p>Progress: 0%</p>
      <div class="bar">
        <div class="fill letters" style="width:0%"></div>
      </div>
    `;
  }

  if(planet === "animals"){
    box.innerHTML = `
      <h3>Animals Planet</h3>
      <p>Highest Level: 0 / 4</p>
      <p>Progress: 0%</p>
      <div class="bar">
        <div class="fill animals" style="width:0%"></div>
      </div>
    `;
  }
}
</script>

</body>
</html>
function nextPage() {
  document.getElementById("page1").classList.remove("active");
  document.getElementById("page2").classList.add("active");
}

function prevPage() {
  document.getElementById("page2").classList.remove("active");
  document.getElementById("page1").classList.add("active");
}

function playSound(file) {
  let audio = new Audio(file);
  audio.play();
}
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: "Trebuchet MS", Arial, sans-serif;
}

body {
  background: #07051a;
  color: #fff;
  overflow: hidden;
}

/* Splash */
.splash {
  position: fixed;
  inset: 0;
  background:
    linear-gradient(rgba(10, 7, 35, 0.25), rgba(10, 7, 35, 0.35)),
    url("background.png") center/cover no-repeat;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
  transition: opacity 0.9s ease, visibility 0.9s ease;
}

.splash.fade-out {
  opacity: 0;
  visibility: hidden;
}

.splash-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
}

.splash-content {
  position: relative;
  text-align: center;
  z-index: 2;
}

/* كلمة Spark تلمع */
.spark-word {
  color: #ffd700;
  text-shadow: 0 0 8px #ffd700, 0 0 16px #fff;
  animation: glowPulse 2s infinite ease-in-out;
}

.brand-mark {
  font-size: 30px;
  margin-bottom: 10px;
  color: #ffd700;
}

/* النجوم */
.twinkle {
  position: absolute;
  color: #fff7cc;
  font-size: 20px;
  opacity: 0;
  animation: twinkleAnim 2s infinite ease-in-out;
  text-shadow: 0 0 8px #fff, 0 0 16px #ffd700;
}

.t1 {
  top: -10px;
  left: 48%;
  animation-delay: 0.2s;
}

.t2 {
  top: 15px;
  right: -15px;
  animation-delay: 0.8s;
}

.t3 {
  bottom: -10px;
  left: 70%;
  animation-delay: 1.3s;
}

/* tagline */
.splash-tagline {
  margin-top: 15px;
  font-size: 1.2rem;
  color: #cccccc;
}

/* أنيميشن */
@keyframes glowPulse {
  0%, 100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

@keyframes twinkleAnim {
  0%, 100% {
    opacity: 0;
    transform: scale(0.5);
  }
  50% {
    opacity: 1;
    transform: scale(1.4);
  }
}

/* Main page */
.page {
  position: relative;
  width: 100vw;
  min-height: 100vh;
  background:
    linear-gradient(rgba(10, 7, 35, 0.08), rgba(10, 7, 35, 0.16)),
    url("background.png") center/cover no-repeat;
  padding: 28px 40px 36px;
}

.hidden {
  display: none;
}

.show {
  display: block;
  animation: fadeIn 0.8s ease;
}

.page-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(to bottom, rgba(0,0,0,0.05), rgba(0,0,0,0.12));
  pointer-events: none;
}

/* Topbar */
.topbar {
  position: relative;
  z-index: 2;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav {
  display: flex;
  gap: 10px;
  background: rgba(64, 18, 121, 0.78);
  border: 2px solid rgba(160, 99, 255, 0.3);
  border-radius: 999px;
  padding: 10px 16px;
  backdrop-filter: blur(8px);
  box-shadow: 0 10px 24px rgba(0,0,0,0.22);
}

.nav a {
  text-decoration: none;
  color: white;
  padding: 10px 16px;
  border-radius: 999px;
  font-weight: 700;
  font-size: 1rem;
  transition: 0.3s;
}

.nav a.active,
.nav a:hover {
  background: linear-gradient(180deg, #7b82ff, #5f66ff);
  box-shadow: 0 0 18px rgba(117, 125, 255, 0.35);
}

.stars-box {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;

  background: rgba(64, 18, 121, 0.78);
  border: 2px solid rgba(160, 99, 255, 0.28);
  border-radius: 999px;
  font-size: medium;
  padding: 8px 14px;   /* 👈 صغرنا البوكس */
  min-width: fit-content;
}

.star-score {
  font-weight: 900;
  color: #ffe36a;
  text-shadow: 0 0 10px rgba(255, 227, 106, 0.5);
}

.profile-pic {
  width: 55px;
  height: 55px;
  border-radius: 50%;
  object-fit: cover;
}

/* Hero */
.hero {
  position: relative;
  z-index: 2;
  text-align: center;
  margin-top: 55px;
}

.hero h2 {
  font-size: 5rem;
  font-weight: 900;
  line-height: 1.08;
  margin-bottom: 10px;
  text-shadow: 0 4px 20px rgba(0,0,0,0.2);
}

.subtitle {
  font-size: 1.8rem;
  color: #f4eeff;
}

/* Content */
.content-section {
  position: relative;
  z-index: 2;
  margin-top: 35px;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  gap: 30px;
}

.moveRight {
  flex: 0 0 280px;
  display: flex;
  align-items: flex-end;
  justify-content: center;
}

.planets-grid {
  display: flex;
  gap: 28px;
  align-items: flex-start;
  justify-content: center;
}

.planet-card {
  width: 300px;
  text-align: center;
}

.planet-image-wrap {
  width: 300px;
  height: 220px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.planet-image-wrap img {
  max-width: 100%;
  max-height: 100%;
  object-fit: contain;
  filter: drop-shadow(0 18px 24px rgba(0,0,0,0.28));
}

.planet-btn {
  margin: 12px auto 12px;
  width: 280px;
  padding: 16px 20px;
  border-radius: 999px;
  font-size: 1.2rem;
  font-weight: 900;
  color: #fff;
  border: 2px solid rgba(255,255,255,0.24);
  box-shadow: 0 10px 22px rgba(0,0,0,0.18);
}

.blue {
  background: linear-gradient(180deg, #61b8ff, #4594ff);
}

.purple {
  background: linear-gradient(180deg, #d56dff, #b24eff);
}

.green {
  background: linear-gradient(180deg, #81dc5c, #5ecb58);
}

.planet-card p {
  font-size: 1rem;
  line-height: 1.5;
  color: #f7f2ff;
  font-weight: 700;
  padding: 0 12px;
}

/* Animations */
@keyframes fadeIn {
  from {
    opacity: 0;
    transform: scale(1.01);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Responsive */
@media (max-width: 1300px) {
  .hero h2 {
    font-size: 4.2rem;
  }

  .planet-card,
  .planet-image-wrap {
    width: 260px;
  }

  .planet-btn {
    width: 240px;
  }
}

@media (max-width: 1100px) {
  body {
    overflow: auto;
  }

  .topbar,
  .content-section,
  .planets-grid {
    flex-direction: column;
    align-items: center;
  }

  .hero h2 {
    font-size: 3.2rem;
  }

  .nav {
    flex-wrap: wrap;
    justify-content: center;
  }

  .splash-logo {
    font-size: 4rem;
  }
}

.splash-logo {
  position: relative;
  display: inline-block;
  font-size: 3rem;
  font-weight: bold;
  color: white;
  letter-spacing: 2px;
}

.splash-logo span {
  display: inline-block;
}

.login-overlay{
    position:fixed;
    inset:0;
    display:flex;
    justify-content:center;
    align-items:center;
    background:rgba(0,0,0,0.45);
    z-index:9999;
    backdrop-filter:blur(4px);
}

.login-card{
    width:min(520px,92vw);
    background:rgba(20,20,60,0.9);
    padding:40px;
    border-radius:24px;
    text-align:center;
    box-shadow:0 18px 45px rgba(0,0,0,0.35);
}

.login-card h2{
    color:white;
    font-size:34px;
    margin-bottom:20px;
}

.login-form{
    display:flex;
    flex-direction:column;
    gap:16px;
}

.login-input{
    width:100%;
    padding:18px 20px;
    border:none;
    border-radius:14px;
    font-size:18px;
    outline:none;
}

.login-btn{
    width:100%;
    padding:18px 20px;
    border:none;
    border-radius:14px;
    background:#4CAF50;
    color:white;
    font-size:20px;
    cursor:pointer;
}
body {
  text-align: center;
  font-family: Arial;
  background: linear-gradient(#0b1a3a, #1e2a5a);
  color: white;
}

.page {
  display: none;
}

.page.active {
  display: block;
}

.grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 15px;
  padding: 20px;
}

.card {
  background: #ffffff20;
  padding: 10px;
  background:
    linear-gradient(rgba(10, 7, 35, 0.25), rgba(10, 7, 35, 0.35)),
    url("background.png") center/cover no-repeat;
   
  border-radius: 15px;
}



button {
  margin-top: 10px;
  padding: 5px 10px;
  border: none;
  border-radius: 10px;
  cursor: pointer;
}

.card img{
    height: 250px;
    width: 200px;
}

<?php
session_start();
$totalPoints = $_SESSION["points"] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Subtraction</title>

<style>
* { box-sizing: border-box; }

body {
  margin: 0;
  font-family: "Trebuchet MS", Arial, sans-serif;
  color: white;
  background: #050b25;
  overflow: hidden;
}

.space-page {
  min-height: 100vh;
  position: relative;
  background:
    radial-gradient(circle at 20% 25%, rgba(124, 58, 237, .45), transparent 25%),
    radial-gradient(circle at 85% 20%, rgba(0, 183, 255, .35), transparent 22%),
    linear-gradient(180deg, #05091f 0%, #071943 55%, #170035 100%);
}

.space-page::before {
  content: "";
  position: absolute;
  inset: 0;
  background-image:
    radial-gradient(white 1px, transparent 1px),
    radial-gradient(#ffd76b 1.3px, transparent 1.3px),
    radial-gradient(#7dd3fc 1px, transparent 1px);
  background-size: 70px 70px, 130px 130px, 180px 180px;
  opacity: .8;
  pointer-events: none;
}

.moon-ground {
  position: absolute;
  bottom: -90px;
  left: -5%;
  width: 110%;
  height: 250px;
  border-radius: 50% 50% 0 0;
  background:
    radial-gradient(circle at 20% 45%, #4b465b 0 18px, transparent 19px),
    radial-gradient(circle at 45% 35%, #5f5870 0 24px, transparent 25px),
    radial-gradient(circle at 70% 50%, #454056 0 20px, transparent 21px),
    radial-gradient(circle at 85% 30%, #696176 0 18px, transparent 19px),
    linear-gradient(180deg, #bcb5c9, #756d86);
  box-shadow: inset 0 25px 45px rgba(255,255,255,.25);
  z-index: 1;
}

#splash {
  position: fixed;
  inset: 0;
  z-index: 20;
  background:
    radial-gradient(circle at 30% 20%, #4c1d95, transparent 25%),
    linear-gradient(180deg, #020617, #071b45);
  display: flex;
  justify-content: center;
  align-items: center;
}

.splash-card {
  text-align: center;
  padding: 50px 80px;
  border-radius: 35px;
  background: rgba(39, 12, 92, .65);
  border: 2px solid #a855f7;
  box-shadow: 0 0 50px rgba(168,85,247,.9);
  animation: pop .8s ease;
}



.splash-card h1 {
  font-size: 58px;
  margin: 0;
}

.splash-card p {
  font-size: 24px;
}

.game-wrap {
  position: relative;
  z-index: 3;
  min-height: 100vh;
  padding: 35px 60px;
}

.topbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.logo h2 {
  margin: 0;
  font-size: 32px;
}

.logo p {
  margin: 0;
  opacity: .9;
}

.score-pill {
  background: rgba(76, 29, 149, .85);
  border: 2px solid #7c3aed;
  border-radius: 28px;
  padding: 14px 28px;
  font-size: 30px;
  font-weight: 900;
  box-shadow: 0 0 25px rgba(124,58,237,.6);
}

.title {
  text-align: center;
  margin-top: 35px;
}

.title h1 {
  font-size: 76px;
  margin: 0;
  text-shadow: 0 0 20px rgba(255,255,255,.35);
}

.title p {
  font-size: 28px;
  margin: 8px 0;
}

.game-card {
  width: 760px;
  margin: 35px auto 0;
  padding: 28px 36px 35px;
  border-radius: 36px;
  background: rgba(20, 8, 65, .78);
  border: 3px solid #a855f7;
  box-shadow:
    0 0 45px rgba(168,85,247,.75),
    inset 0 0 40px rgba(59,130,246,.16);
}

.level_3 {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 18px;
  font-size: 22px;
  font-weight: 800;
  margin-bottom: 28px;
}

.progress {
  width: 260px;
  height: 18px;
  background: rgba(255,255,255,.2);
  border-radius: 20px;
  overflow: hidden;
  border: 2px solid rgba(255,255,255,.25);
}

.progress-fill {
  height: 100%;
  width: 0%;
  background: linear-gradient(90deg, #84cc16, #22c55e);
  border-radius: 20px;
  transition: .3s;
}

.question {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 28px;
  margin-bottom: 22px;
}

.number-box {
  width: 135px;
  height: 135px;
  border-radius: 24px;
  background: linear-gradient(180deg, #fff, #e9eefc);
  color: #111827;
  display: grid;
  place-items: center;
  font-size: 78px;
  font-weight: 900;
  box-shadow: 0 10px 0 rgba(255,255,255,.35);
}

#num1 { color: #9333ea; }
#num2 { color: #22c55e; }
.qmark { color: #f97316; }

.operator {
  font-size: 78px;
  font-weight: 900;
}

.instruction {
  font-size: 24px;
  font-weight: 800;
  margin-bottom: 20px;
}

.options {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 18px;
}

.option {
  height: 95px;
  border: none;
  border-radius: 24px;
  background: linear-gradient(180deg, #ffffff, #e9eefc);
  font-size: 52px;
  font-weight: 900;
  cursor: pointer;
  box-shadow: 0 8px 0 rgba(255,255,255,.35);
  transition: .2s;
}

.option:hover {
  transform: translateY(-8px) scale(1.04);
}

.option:nth-child(1) { color: #0ea5e9; }
.option:nth-child(2) { color: #22c55e; }
.option:nth-child(3) { color: #f97316; }
.option:nth-child(4) { color: #9333ea; }

.message {
  min-height: 32px;
  margin-top: 18px;
  font-size: 24px;
  font-weight: 900;
}

#nextLevelBtn {
  margin-top: 15px;
  padding: 14px 28px;
  border-radius: 22px;
  border: none;
  background: linear-gradient(90deg, #22c55e, #4ade80);
  color: white;
  font-size: 22px;
  font-weight: 900;
  cursor: pointer;
  box-shadow: 0 0 25px rgba(34,197,94,.8);
}

.back-btn {
  position: absolute;
  left: 50px;
  top: 120px;
  color: white;
  text-decoration: none;
  background: rgba(76,29,149,.85);
  border: 2px solid #7c3aed;
  padding: 12px 24px;
  border-radius: 24px;
  font-size: 20px;
  font-weight: bold;
  z-index: 5;
}



@keyframes pop {
  from { transform: scale(.85); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
</style>
</head>

<body>

<section id="splash">
  <div class="splash-card">
    
    <h1>Subtraction Quiz</h1>
    <p>Let’s subtract and have fun!</p>
  </div>
</section>

<div class="space-page">
  <div class="moon-ground"></div>

  
 
 <a href="PracticeSub.html" class="back-btn">← Back</a>
 


  <main class="game-wrap">
    <header class="topbar">
      <div class="logo">
        <h2>SpaceSpark</h2>
        <p>Learn • Explore • Grow</p>
      </div>

     <div class="score-pill">⭐ <span id="score"><?php echo $totalPoints; ?></span></div>

    </header>

    <section class="title">
      <p>Numbers Planet</p>
      <h1>Subtraction</h1>
      <p>Let’s subtract and have fun!</p>
    </section>

    <section class="game-card">
      <div class="level_3">
        <span>⭐ Level 3</span>
        <div class="progress">
          <div id="progressFill" class="progress-fill"></div>
        </div>
        <span id="levelText">0/5</span>
      </div>

      <div class="question">
        <div class="number-box" id="num1"></div>
        <div class="operator">−</div>
        <div class="number-box" id="num2"></div>
        <div class="operator">=</div>
        <div class="number-box qmark">?</div>
      </div>

      <div class="instruction">Choose the correct answer</div>
      <div class="options" id="options"></div>
      <div class="message" id="message"></div>
    </section>
  </main>
</div>

<script>
let num1, num2, correct;
let score = <?php echo $totalPoints; ?>;
let questionCount = 0;
let gameFinished = false;

function generateQuestion() {
  if (gameFinished) return;

  num1 = Math.floor(Math.random() * 10) + 5;
  num2 = Math.floor(Math.random() * num1);
  correct = num1 - num2;

  document.getElementById("num1").textContent = num1;
  document.getElementById("num2").textContent = num2;

  let answers = [correct];

  while (answers.length < 4) {
    let wrong = Math.floor(Math.random() * 15);
    if (!answers.includes(wrong)) answers.push(wrong);
  }

  answers.sort(() => Math.random() - 0.5);

  const options = document.getElementById("options");
  options.innerHTML = "";

  answers.forEach(answer => {
    const btn = document.createElement("button");
    btn.className = "option";
    btn.textContent = answer;
    btn.onclick = () => checkAnswer(answer);
    options.appendChild(btn);
  });
}

function checkAnswer(answer) {
  if (gameFinished) return;

  const message = document.getElementById("message");

if (answer === correct && !gameFinished) {

    questionCount++;

    message.textContent = "Correct! Great job!";
    let correctSound = new Audio("correct.MP4");
      correctSound.play();

    message.style.color = "#22c55e";

    fetch("add_points.php", {
  method: "POST"
})
.then(res => res.text())
.then(points => {
  document.getElementById("score").textContent = points;
});
    document.getElementById("levelText").textContent = questionCount + "/5";
    document.getElementById("progressFill").style.width = (questionCount * 10) + "%";

  if (questionCount === 5) {
  gameFinished = true;

  // نخفي السؤال
  document.querySelector(".question").style.display = "none";

  // نخفي النص
  document.querySelector(".instruction").style.display = "none";

  // نخفي الخيارات
  document.getElementById("options").style.display = "none";

  // نخلي البار فل
  document.getElementById("progressFill").style.width = "100%";

  // رسالة الفوز
  message.innerHTML = `
    <div style="text-align:center;">
      <h2 style="color:#22c55e;">🎉 You Win! 🎉</h2>
      <p style="color:white;">You can go to the next level now!</p>
      <button id="nextLevelBtn">Next Level</button>
    </div>
  `;

  document.getElementById("nextLevelBtn").onclick = () => {
    window.location.href = "levelmul.html";
  };

  return;
}

    if (questionCount < 5) {
  setTimeout(generateQuestion, 650);
}

  } else {
    message.textContent = "Try again!";
    let wrongSound = new Audio("tryagain.MP4");
    wrongSound.play();
    message.style.color = "#f97316";
  }
}

window.onload = () => {
  setTimeout(() => {
    document.getElementById("splash").style.display = "none";
    generateQuestion();
  }, 1800);
};
</script>

</body>
</html>
<?php
echo "PHP works";
?>
