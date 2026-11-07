let questions = [];
let currentQuestion = 0;
let score = 0;
let timerInterval;
let timeLeft = 30;

const questionEl = document.getElementById("question");
const choicesEl = document.getElementById("choices");
const nextBtn = document.getElementById("nextBtn");
const timerEl = document.getElementById("timer");
const resultEl = document.getElementById("result");

// Gọi dữ liệu từ PHP
fetch("get_questions.php")
  .then(res => res.json())
  .then(data => {
    questions = data;
    loadQuestion();
  });

function startTimer() {
  clearInterval(timerInterval);
  timeLeft = 30;
  timerEl.textContent = timeLeft;
  timerInterval = setInterval(() => {
    timeLeft--;
    timerEl.textContent = timeLeft;
    if (timeLeft <= 0) {
      clearInterval(timerInterval);
      nextQuestion();
    }
  }, 1000);
}

function loadQuestion() {
  const q = questions[currentQuestion];
  questionEl.textContent = q.question_text;
  choicesEl.innerHTML = "";

  q.answers.forEach((ans) => {
    const div = document.createElement("div");
    div.className = "choice";
    div.textContent = ans.answer_text;
    div.onclick = () => selectAnswer(ans.is_correct, div);
    choicesEl.appendChild(div);
  });

  nextBtn.style.display = "block";
  startTimer();
}

function selectAnswer(isCorrect, div) {
  const choiceDivs = document.querySelectorAll(".choice");
  choiceDivs.forEach((el) => {
    el.style.pointerEvents = "none";
  });

  if (isCorrect == 1) {
    div.classList.add("correct");
    score++;
  } else {
    div.classList.add("wrong");
  }
}

function nextQuestion() {
  currentQuestion++;
  if (currentQuestion < questions.length) {
    loadQuestion();
  } else {
    showResult();
  }
}

function showResult() {
  clearInterval(timerInterval);
  document.querySelector(".timer").style.display = "none";
  questionEl.style.display = "none";
  choicesEl.style.display = "none";
  nextBtn.style.display = "none";
  resultEl.style.display = "block";
  resultEl.textContent = `Bạn trả lời đúng ${score}/${questions.length} câu!`;
}

nextBtn.addEventListener("click", nextQuestion);
