// Fade-in animation on scroll
const faders = document.querySelectorAll('.fade-in');
window.addEventListener('scroll', () => {
  const triggerBottom = window.innerHeight * 0.9;
  faders.forEach(el => {
    const boxTop = el.getBoundingClientRect().top;
    if (boxTop < triggerBottom) {
      el.classList.add('show');
    }
  });
});

// Open login modal
document.querySelector('.open-modal').addEventListener('click', e => {
  e.preventDefault();
  document.querySelector('.bg-modal').style.display = 'flex';
});

// Close login modal
document.querySelectorAll('.close').forEach(btn => {
  btn.addEventListener('click', () => {
    btn.parentElement.parentElement.style.display = 'none';
  });
});

// Open signup modal
function openSignupModal() {
  document.querySelector(".bg-modal").style.display = "none";
  document.querySelector(".bg-modal-signup").style.display = "flex";
}

// Back to login modal
function backToLogin() {
  document.querySelector(".bg-modal-signup").style.display = "none";
  document.querySelector(".bg-modal").style.display = "flex";
}

// Open terms modal
function openTermsModal() {
  document.querySelector(".bg-modal-terms").style.display = "flex";
}

// Signup form validation
document.getElementById("signupForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const username = document.getElementById("username").value.trim();
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value;
  const terms = document.getElementById("terms").checked;

  if (!username || !email || !password) {
    alert("Please fill in all fields.");
    return;
  }

  const emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;
  if (!email.match(emailPattern)) {
    alert("Please enter a valid email address.");
    return;
  }

  if (!terms) {
    alert("You must agree with the Terms and Conditions.");
    return;
  }

  alert("Sign up successful! Returning to login...");
  document.querySelector(".bg-modal-signup").style.display = "none";
  document.querySelector(".bg-modal").style.display = "flex";
});

/* document.querySelectorAll('.icon').forEach(btn => {
  btn.addEventListener('click', () => {
    alert("You clicked " + btn.dataset.app);
  });
}); */