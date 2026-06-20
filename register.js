```javascript
// ===============================
// REGISTER PAGE - PT INDOFOOD
// ===============================

document.addEventListener("DOMContentLoaded", () => {
  const registerForm = document.querySelector(".register-form");
  const firstNameInput = document.getElementById("firstName");
  const lastNameInput = document.getElementById("lastName");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const rememberCheckbox = document.querySelector('input[name="rememberActivity"]');

  const signInButton = document.querySelector(".signin-btn");
  const signUpButton = document.querySelector(".signup-btn");

  // Ambil data yang pernah disimpan
  loadRememberedData();

  // Event submit register
  registerForm.addEventListener("submit", (event) => {
    event.preventDefault();

    const firstName = firstNameInput.value.trim();
    const lastName = lastNameInput.value.trim();
    const email = emailInput.value.trim();
    const password = passwordInput.value.trim();
    const remember = rememberCheckbox.checked;

    clearAllErrors();

    let isValid = true;

    if (firstName === "") {
      showError(firstNameInput, "First name wajib diisi");
      isValid = false;
    }

    if (lastName === "") {
      showError(lastNameInput, "Last name wajib diisi");
      isValid = false;
    }

    if (email === "") {
      showError(emailInput, "Email wajib diisi");
      isValid = false;
    } else if (!isValidEmail(email)) {
      showError(emailInput, "Format email tidak valid");
      isValid = false;
    }

    if (password === "") {
      showError(passwordInput, "Password wajib diisi");
      isValid = false;
    } else if (password.length < 6) {
      showError(passwordInput, "Password minimal 6 karakter");
      isValid = false;
    }

    if (!isValid) return;

    const userData = {
      firstName,
      lastName,
      email,
      password,
      remember
    };

    // Simulasi penyimpanan akun
    localStorage.setItem("registeredUser", JSON.stringify(userData));

    // Simpan data login jika remember dicentang
    if (remember) {
      localStorage.setItem("rememberedEmail", email);
      localStorage.setItem("rememberedFirstName", firstName);
      localStorage.setItem("rememberActivity", "true");
    } else {
      localStorage.removeItem("rememberedEmail");
      localStorage.removeItem("rememberedFirstName");
      localStorage.removeItem("rememberActivity");
    }

    showSuccessMessage("Account berhasil dibuat!");

    signUpButton.disabled = true;
    signUpButton.textContent = "Processing...";

    setTimeout(() => {
      signUpButton.disabled = false;
      signUpButton.textContent = "Sign Up";

      // Ganti ke halaman tujuan setelah register
      // window.location.href = "dashboard.html";
      alert("Register berhasil! Data akun tersimpan di localStorage.");
    }, 1000);
  });

  // Event tombol Sign In
  signInButton.addEventListener("click", () => {
    // Ganti sesuai halaman login kamu
    // window.location.href = "login.html";
    alert("Arahkan ke halaman Sign In / Login.");
  });

  // ===============================
  // FUNCTIONS
  // ===============================

  function isValidEmail(email) {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailPattern.test(email);
  }

  function showError(input, message) {
    const formGroup = input.closest(".form-group");

    input.classList.add("input-error");

    const errorMessage = document.createElement("small");
    errorMessage.className = "error-message";
    errorMessage.textContent = message;

    formGroup.appendChild(errorMessage);
  }

  function clearAllErrors() {
    const errorMessages = document.querySelectorAll(".error-message");
    const errorInputs = document.querySelectorAll(".input-error");

    errorMessages.forEach((message) => message.remove());
    errorInputs.forEach((input) => input.classList.remove("input-error"));
  }

  function showSuccessMessage(message) {
    const oldMessage = document.querySelector(".success-message");
    if (oldMessage) oldMessage.remove();

    const successMessage = document.createElement("div");
    successMessage.className = "success-message";
    successMessage.textContent = message;

    registerForm.prepend(successMessage);

    setTimeout(() => {
      successMessage.remove();
    }, 2500);
  }

  function loadRememberedData() {
    const rememberedEmail = localStorage.getItem("rememberedEmail");
    const rememberedFirstName = localStorage.getItem("rememberedFirstName");
    const rememberActivity = localStorage.getItem("rememberActivity");

    if (rememberActivity === "true") {
      if (rememberedEmail) emailInput.value = rememberedEmail;
      if (rememberedFirstName) firstNameInput.value = rememberedFirstName;
      rememberCheckbox.checked = true;
    }
  }

  // Efek input ketika user mengetik
  const formInputs = document.querySelectorAll(".form-input");

  formInputs.forEach((input) => {
    input.addEventListener("input", () => {
      input.classList.remove("input-error");

      const formGroup = input.closest(".form-group");
      const errorMessage = formGroup.querySelector(".error-message");

      if (errorMessage) {
        errorMessage.remove();
      }
    });
  });
});
```
