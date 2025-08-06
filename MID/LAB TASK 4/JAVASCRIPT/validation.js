function validateForm(event) {

  if (event) event.preventDefault();

  document.querySelectorAll(".error").forEach(span => span.textContent = "");

  let hasError = false;

  const nameRegex = /^[A-Za-z\s-]+$/;
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const mobileRegex = /^\(\d{3}\) \d{3}-\d{4}$/;
  const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/;

  var fullname = document.getElementById('fullname').value.trim();
  var age = document.getElementById('age').value;
  var phone = document.getElementById('phone').value.trim();
  var email = document.getElementById('email').value.trim();
  var insurance = document.getElementById('insurance').value.trim();
  var policy = document.getElementById('policy').value.trim();
  var password = document.querySelector('input[type="password"]').value.trim();
  var confirmPassword = document.querySelector('input[type="confirmPassword"]').value.trim();
  var redactedPassword = "*".repeat(password.length);

  if (fullname === "" || age === "" || phone === "" || email === "" || insurance === "" || policy === "" || email === "" || username === "" || password === "" || confirmPassword === "") {
        alert("Please fill all the fields.");
        return false;
  }

  if (!fullname || !nameRegex.test(fullname)) {
    document.getElementById('error-fullname').textContent = "Only letterts, spaces and hyphens are allowed.";
    hasError = true;
  }

  if (!age || deposit < 18) {
    document.getElementById('error-age').textContent = "Age must be 18 or older";
    hasError = true;
  }

  if (!mobile || mobile.length !== 10 || isNaN(mobile) || !mobileRegex.test(mobile)) {
    document.getElementById('error-phone').textContent = "Format Must be (XXX) XXX-XXXX";
    hasError = true;
  }

  if (!email || !emailRegex.test(email)) {
    document.getElementById('error-email').textContent = "Email is required.";
    hasError = true;
  }

  if (!insurance) {
    document.getElementById('error-insurance').textContent = "Please select an insurance provider.";
    hasError = true;
  }
    if (!policy || policy.length < 10) {
        document.getElementById('error-policy').textContent = "Must be alphanumeric and at least 10 characters long.";
        hasError = true;
    }
    if (username.length <= 5 ) {
        document.getElementById('error-username').textContent = "Username must be more than 5 characters";
        hasError = true;
    }

  if (!password || !passwordRegex.test(password)) {
    document.getElementById('error-password').textContent = "Min 8 character is required with at least one letter and one number and symbol.";
    hasError = true;
  }

  if (hasError) return false;

  alert(
        "Registration successful!\n" +
        "NAME: " + fullname + "\n" +
        "AGE: " + age + "\n" + 
        "MOBILE: " + phone + "\n" +
        "EMAIL: " + email + "\n" + 
        "INSURANCE PROVIDER: " + insurance + "\n" +
        "INSURANCE POLICY NUMBER: " + policy + "\n" + 
        "USERNAME: " + username + "\n" +
        "PASSWORD: " + redactedPassword + "\n"
      );
  
  return false;
}
