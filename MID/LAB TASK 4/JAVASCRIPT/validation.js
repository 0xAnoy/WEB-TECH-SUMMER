function validateForm(event) {

  if (event) event.preventDefault();
  

document.querySelectorAll(".error").forEach(span => span.textContent = "");

let hasError = false;

const nameRegex = /^[A-Za-z\s-]+$/;
const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const mobileRegex = /^\(\d{3}\) \d{3}-\d{4}$/;
const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/;

var fullname = document.getElementById('fullname').value.trim();
var age = document.getElementById('age').value;
var phone = document.getElementById('phone').value.trim();
var email = document.getElementById('email').value.trim();
var insurance = document.getElementById('insurance').value.trim();
var policy = document.getElementById('policy').value.trim();

var username = document.getElementById('username').value.trim();
var password = document.getElementById('password').value.trim();
var redactedPassword = "*".repeat(password.length);


// This check is not needed as we are checking each field individually below. This is commented out because this doesn't let the user see which field is causing the error.

// if (fullname === "" || age === "" || phone === "" || email === "" || insurance === "" || policy === "" || username === "" || password === "" || confirmPassword === "") {
//   alert("Please fill all the fields.");
//   return false;
// } 

if (!fullname || !nameRegex.test(fullname)) {
  document.getElementById('error-fullname').textContent = "Only letterts, spaces and hyphens are allowed.";
  hasError = true;
}

if (!age || age < 18) {
  document.getElementById('error-age').textContent = "Age must be 18 or older";
  hasError = true;
}

if (!phone || !( /^\d{10}$/.test(phone) || mobileRegex.test(phone) )) {
  document.getElementById('error-phone').textContent = "Enter 10 digits or format (XXX) XXX-XXXX";
  hasError = true;
}

if (!email || !emailRegex.test(email)) {
  document.getElementById('error-email').textContent = "Email is required.";
  hasError = true;
}

if (!insurance || insurance === "Select") {
  document.getElementById('error-insurance').textContent = "Please select an insurance provider.";
  hasError = true;
}
if (!policy) {
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
