<!DOCTYPE html>
<html>
<head>
    <title>Bank Management System</title>
    <style>
        #switchbutton{
          padding: 10px,20px;
          font-size: 15px;
          cursor: pointer;
          position: absolute;
          left:950px;
          top: 20px;
        }
        body{
            font-family: Arial, sans-serif;
            background-color: #f1f3e8
            padding:30px;
        }
        h1,h2
        {
          text-align: center;
        }
        label {
          font-weight: bold;
        }


        form 
        {
            background-color: skyblue;
            width: 700px;
            margin: auto;
            padding: 20px 25px;
            border-radius: 5px;
        }

        input[type="text"],input[type="date"],input[type="email"],input[type="number"],input[type="password"],textarea
        {
            padding: 6px;
            width: 100%;
            margin-top: 10px;
            border: 1px solid #2b2a2aff;
            border-radius: 5px;
        }

        input[type="submit"],input[type="reset"]
        {
            padding: 5px 10px;
            margin: auto;
            background-color: white;
            cursor:pointer;
            font-weight: bold;
        }
        input[type="radio"]
        {
          margin-left: 10px;
          margin-right: 5px;
        }
        textarea{
          resize: vertical;
        }
        input[type="file"]
        {
          margin-top: 10px;
        }

        .checkbox{
          margin-top: 15px;
        }
        .buttons {
          margin-top: 20px;
          text-align: center;
        }

    </style>



</head>


<body>

  <h1>Bank Management System</h1>
  <h2>Your Trusted Financial Partner</h2>

  <button id="switchbutton" onclick="toggle()">SWITCH BACKGROUND</button>

  <script>
    function toggle() {
      var body = document.body;
      var button = document.getElementById('switchbutton');
      if (body.style.backgroundColor === 'white'){
        body.style.backgroundColor = 'black';
        button.innerHTML= 'Switch to White';
      }else{
        body.style.backgroundColor = 'white';
        button.innerHTML= 'Switch to Black';
      }
    }
  </script>

  <form onsubmit="return handleSubmit(event)">

    <h3>Customer Registration Form</h3>

    <label>Full Name:</label>
    <input type="text" id="fullname" placeholder="Enter your full name">


    <label>Date of Birth:</label>
    <input type="date" id="dob">


    <label>Gender:</label>
    <input type="radio" id="gender" value="male">Male
    <input type="radio" id="gender" value="female">Female
    <input type="radio" id="gender" value="other">Other<br><br>

    <label>Marital Status:</label>
    <select id="marital">
      <option>Single</option>
      <option>Married</option>
      <option>Other</option>
    </select> <br><br>

    <label>Account Type:</label>
    <select id="account">
      <option>Savings</option>
      <option>Current</option>
      <option>Fixed Deposit</option>
    </select><br><br>

    <label>Initial Deposit Amount:</label>
    <input type="number" id="deposit">

    <label>Mobile Number:</label>
    <input type="text" id="mobile">

    <label>Email Address:</label>
    <input type="email" id="email">

    <label>Address:</label>
    <textarea id="address" rows="2"></textarea>

    <label>Occupation:</label>
    <input type="text" id="occupation">

    <label>National ID (NID):</label>
    <input type="text" name="nid">

    <label>Set Password:</label>
    <input type="password" name="password">

    <label>Upload ID Proof:</label>
    <input type="file" name="idproof">

    <div class="checkbox">
      <input type="checkbox" name="agree"> I agree to the terms and conditions
    </div>

    <div class="buttons">
      <input type="submit" value="Register">
      <input type="reset" value="Clear">
    </div>
  </form>

  <script>

function handleSubmit(event) {
      if (event) event.preventDefault();
      var fullname = document.getElementById('fullname').value;
      var dob = document.getElementById('dob').value;
      var marital = document.getElementById('marital').value;
      var account = document.getElementById('account').value;
      var deposit = document.getElementById('deposit').value;
      var mobile = document.getElementById('mobile').value;
      var email = document.getElementById('email').value;
      var address = document.getElementById('address').value;
      var occupation = document.getElementById('occupation').value;
      var password = document.querySelector('input[type="password"]').value;

      if (fullname === "" || dob === "" || marital === "" || account === "" || deposit === "" || mobile === "" || email === "" || address === "" || occupation === "" || password === "") {
        alert("Please fill all the fields.");
        return false;
      }
      if (mobile.length !== 11 || isNaN(mobile)){
        alert("Mobile number must be 11 digits.");
        return false;
      }
      if(deposit <= 0){
        alert("Deposit can't be zero or negative.");
        return false;
      }

      // Redact password with asterisks
      var redactedPassword = '*'.repeat(password.length); 

      alert(
        "Registration successful!\n" +
        "NAME: " + fullname + "\n" +
        "DOB: " + dob + "\n" + 
        "MARITAL STATUS: " + marital + "\n" +
        "ACCOUNT TYPE: " + account + "\n" + 
        "INITIAL DEPOSIT: " + deposit + "\n" +
        "MOBILE: " + mobile + "\n" +
        "EMAIL: " + email + "\n" + 
        "ADDRESS: " + address + "\n" +
        "OCCUPATION: " + occupation + "\n" +
        "PASSWORD: " + redactedPassword + "\n"
      );

      // Display submitted details
      var detailsDiv = document.getElementById('submittedDetails');
      if (!detailsDiv) {
        detailsDiv = document.createElement('div');
        detailsDiv.id = 'submittedDetails';
        detailsDiv.style.background = '#e6ffe6';
        detailsDiv.style.margin = '30px auto';
        detailsDiv.style.width = '700px';
        detailsDiv.style.padding = '20px';
        detailsDiv.style.borderRadius = '5px';
        detailsDiv.style.fontSize = '16px';
        document.body.appendChild(detailsDiv);
      }
      detailsDiv.innerHTML =
        '<h3>Submitted Details</h3>' +
        '<b>Full Name:</b> ' + fullname + '<br>' +
        '<b>Date of Birth:</b> ' + dob + '<br>' +
        '<b>Marital Status:</b> ' + marital + '<br>' +
        '<b>Account Type:</b> ' + account + '<br>' +
        '<b>Initial Deposit Amount:</b> ' + deposit + '<br>' +
        '<b>Mobile Number:</b> ' + mobile + '<br>' +
        '<b>Email Address:</b> ' + email + '<br>' +
        '<b>Address:</b> ' + address + '<br>' +
        '<b>Occupation:</b> ' + occupation + '<br>' +
        '<b>Password:</b> ' + redactedPassword + '<br>';

      return false;
    }
  

  </script>
</body>
</html>