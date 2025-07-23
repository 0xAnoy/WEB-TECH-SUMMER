<!DOCTYPE html>
<html>
<head>
    <title>Bank Management System</title>
    <style>
        body{
            font-family: Arial, sans-serif;
            background-color: #f1f3e8
            padding:30px;
        }
        h1,h2
        {
            text-allign: center;
        }
        label {
          font-weight: bold;
        }


        form 
        {
            background-color: #f1f3e8
            width: 350px;
            margin: auto;
            padding: 20px 30px;
            border-radius: 5px;
        }

        input[type="text"],input[type="date"],input[type="email"],input[type="number"],input[type="password"],textarea
        {
            padding: 4px;
            width: 100%;
            margin-top: 5px;
            border: 1px solid #ccc
            border-radius: 3px;
        }

        input[type="submit"],input[type="reset"]
        {
            padding: 5px 10px;
            margin: auto;
            background-color: skyblue;
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

  <form>

    <h3>Customer Registration Form</h3>
    <tr>
    <td><label>Full Name:</label></td>
    <td><input type="text" name="fullname"></td>
    </tr>

    <tr>
    <td><label>Date of Birth:</label></td>
    <td><input type="date" name="dob"></td>
    </tr>

    <tr>
    <label>Gender:</label>
    <td><input type="radio" name="gender" value="male">Male</td>
    <td><input type="radio" name="gender" value="female">Female</td>
    <td><input type="radio" name="gender" value="other">Other</td>
    </tr> <br>

    <label>Marital Status:</label>
    <select name="marital">
      <option>Single</option>
      <option>Married</option>
      <option>Other</option>
    </select> <br>

    <label>Account Type:</label>
    <select name="account">
      <option>Savings</option>
      <option>Current</option>
      <option>Fixed Deposit</option>
    </select><br>

    <label>Initial Deposit Amount:</label>
    <input type="number" name="deposit">

    <label>Mobile Number:</label>
    <input type="text" name="mobile">

    <label>Email Address:</label>
    <input type="email" name="email">

    <label>Address:</label>
    <textarea name="address" rows="2"></textarea>

    <label>Occupation:</label>
    <input type="text" name="occupation">

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

</body>
</html>