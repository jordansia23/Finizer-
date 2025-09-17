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
