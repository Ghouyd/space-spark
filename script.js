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
