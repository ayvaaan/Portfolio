const themeToggleBtn = document.getElementById("theme-toggle");
const mobileToggleBtn = document.getElementById("mobile-toggle");
const navLinks = document.getElementById("nav-links");
const themeIcon = document.getElementById("theme-icon");

// Theme Management
const systemTheme = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
const savedTheme = localStorage.getItem("theme") || systemTheme;
document.documentElement.setAttribute("data-theme", savedTheme);
updateThemeIcon(savedTheme);

themeToggleBtn.addEventListener("click", () => {
  const currentTheme = document.documentElement.getAttribute("data-theme");
  const newTheme = currentTheme === "dark" ? "light" : "dark";

  document.documentElement.setAttribute("data-theme", newTheme);
  localStorage.setItem("theme", newTheme);
  updateThemeIcon(newTheme);
});

function updateThemeIcon(theme) {
  if (theme === "dark") {
    // Sun Icon
    themeIcon.innerHTML =
      '<path d="M12 2.25a.75.75 0 0 1 .75.75v2.25a.75.75 0 0 1-1.5 0V3a.75.75 0 0 1 .75-.75ZM7.5 12a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM18.894 6.166a.75.75 0 0 0-1.06-1.06l-1.591 1.59a.75.75 0 1 0 1.06 1.061l1.591-1.59ZM21.75 12a.75.75 0 0 1-.75.75h-2.25a.75.75 0 0 1 0-1.5H21a.75.75 0 0 1 .75.75ZM17.834 18.894a.75.75 0 0 0 1.06-1.06l-1.59-1.591a.75.75 0 1 0-1.061 1.06l1.59 1.591ZM12 18.75a.75.75 0 0 1 .75.75V21a.75.75 0 0 1-1.5 0v-1.5a.75.75 0 0 1 .75-.75ZM6.166 18.894a.75.75 0 0 1-1.06-1.06l1.59-1.591a.75.75 0 1 1 1.061 1.06l-1.59 1.591ZM2.25 12a.75.75 0 0 1 .75-.75H5.25a.75.75 0 0 1 0 1.5H3a.75.75 0 0 1-.75-.75ZM6.166 6.166a.75.75 0 0 1 1.06-1.06l1.59 1.591a.75.75 0 1 1-1.061 1.06l-1.59-1.591Z" />';
  } else {
    // Moon Icon
    themeIcon.innerHTML = '<path fill-rule="evenodd" clip-rule="evenodd" d="M9.528 1.718a.75.75 0 0 1 .162.819A8.97 8.97 0 0 0 9 6a9 9 0 0 0 9 9 8.97 8.97 0 0 0 3.463-.69.75.75 0 0 1 .981.98 10.503 10.503 0 0 1-9.694 6.46c-5.799 0-10.5-4.7-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 0 1 .818.162Z" />';
  }
}

// Mobile Navigation Toggle
mobileToggleBtn.addEventListener("click", () => {
  const isActive = navLinks.classList.toggle("active");
  document.body.style.overflow = isActive ? "hidden" : "";

  // Toggle icon between hamburger and close (X)
  if (isActive) {
    mobileToggleBtn.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
      </svg>`;
  } else {
    mobileToggleBtn.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
      </svg>`;
  }
});

// Close mobile menu when a navigation link is clicked
navLinks.querySelectorAll("a").forEach((link) => {
  link.addEventListener("click", () => {
    navLinks.classList.remove("active");
    document.body.style.overflow = "";
    mobileToggleBtn.innerHTML = `
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
      </svg>`;
  });
});

// Update Year in Footer
const yearEl = document.getElementById("year");
if (yearEl) yearEl.textContent = new Date().getFullYear();

// Active Nav Link
const currentPage = location.pathname.split("/").pop() || "index.html";
document.querySelectorAll(".nav-links a").forEach((link) => {
  link.classList.remove("active");
  const linkPage = link.getAttribute("href").split("/").pop();
  if (linkPage === currentPage) link.classList.add("active");
});

// Scroll Reveal Animation
const revealElements = () => {
  const elements = document.querySelectorAll('[data-reveal]');
  const options = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
  };

  const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.style.opacity = '1';
        entry.target.style.transform = 'translateY(0)';
        observer.unobserve(entry.target);
      }
    });
  }, options);

  elements.forEach((el) => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
  });
};

// Run on page load
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', revealElements);
} else {
  revealElements();
}

// Form Input Focus Animations
const formInputs = document.querySelectorAll('.form-control');
formInputs.forEach((input) => {
  input.addEventListener('focus', function() {
    this.style.transition = 'all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1)';
    this.style.transform = 'scale(1.02)';
  });
  
  input.addEventListener('blur', function() {
    this.style.transform = 'scale(1)';
  });
});

// Carousel Logic (About Page)
const slideEls = document.querySelectorAll(".carousel-slide");
const prevBtn = document.getElementById("prev");
const nextBtn = document.getElementById("next");

if (slideEls.length && prevBtn && nextBtn) {
  let index = 0;
  slideEls[index].classList.add("active");

  function goTo(i) {
    slideEls[index].classList.remove("active");
    index = (i + slideEls.length) % slideEls.length;
    slideEls[index].classList.add("active");
  }

  prevBtn.addEventListener("click", () => goTo(index - 1));
  nextBtn.addEventListener("click", () => goTo(index + 1));
  
  // Auto-advance carousel every 5 seconds
  setInterval(() => goTo(index + 1), 5000);
}

// Contact Form Logic (Contact Page)
// Sends form data to PHP backend to store in database
const contactForm = document.getElementById("contactForm");
const formStatus = document.getElementById("formStatus");
const submitBtn = document.getElementById("submitBtn");

if (contactForm) {
  contactForm.addEventListener("submit", async function (event) {
    event.preventDefault();

    if (!contactForm.checkValidity()) {
      contactForm.reportValidity();
      return;
    }

    const btnSpan = submitBtn.querySelector("span");
    const originalText = btnSpan.textContent;
    
    // Add loading animation
    submitBtn.style.pointerEvents = 'none';
    submitBtn.style.opacity = '0.7';
    btnSpan.textContent = "✨ Sending...";
    submitBtn.style.animation = 'pulse-scale 0.6s ease-in-out';
    formStatus.textContent = "";
    formStatus.className = "form-status";

    // Prepare form data
    const formData = new FormData(contactForm);

    try {
      // Send to PHP backend
      const response = await fetch('assets/php/submit-contact.php', {
        method: 'POST',
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        formStatus.textContent = "🎉 " + data.message;
        formStatus.className = "form-status success";
        formStatus.style.animation = 'slideUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1)';
        
        // Reset form with animation
        contactForm.style.animation = 'fadeInUp 0.4s ease-out';
        contactForm.reset();
      } else {
        const errorMsg = data.errors ? data.errors.join(', ') : data.message;
        formStatus.textContent = "❌ Error: " + errorMsg;
        formStatus.className = "form-status error";
      }
    } catch (error) {
      formStatus.textContent = "❌ Error sending message. Please try again.";
      formStatus.className = "form-status error";
      console.error('Form submission error:', error);
    } finally {
      // Reset button
      btnSpan.textContent = originalText;
      submitBtn.disabled = false;
      submitBtn.style.pointerEvents = 'auto';
      submitBtn.style.opacity = '1';
      submitBtn.style.animation = 'none';
    }
  });
}

// Intersection Observer for Scroll Animations
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
      observer.unobserve(entry.target);
    }
  });
}, observerOptions);

// Apply reveal animations to all elements with data-reveal attribute
document.querySelectorAll('[data-reveal]').forEach(el => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(20px)';
  el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
  observer.observe(el);
});
