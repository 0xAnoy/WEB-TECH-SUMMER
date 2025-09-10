<!DOCTYPE html>
<html>
<head>
    <title>AIUB Clinic</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <script src="../JAVASCRIPT/validation.js"></script>
</head>

<body>
    <h1>AIUB CLINIC</h1>
    
    <form onsubmit="return validateForm(event)">
        <h3> Customer Registration Form</h3>
        
        <label>Full Name:</label>
        <input type="text" id="fullname"><br>
        <span id="error-fullname" class="error"></span>

        <label>Age:</label><br>
        <input type="number" id="age"><br>
        <span id="error-age" class="error"></span>

        <label>Phone Number:</label>
        <input type="text" id="phone"><br>
        <span id="error-phone" class="error"></span>

        <label>Email:</label>
        <input type="email" id="email"><br>
        <span id="error-email" class="error"></span>

        <label>Insurance Provider:</label><br>
        <select id="insurance">
            <option>Select</option>
            <option>Metlife</option>
            <option>Green Delta</option>
            <option>City Insurance</option>
            <span id="error-insurance" class="error"></span>
        </select><br>

        <label>Insurance Policy Number:</label>
        <input type="text" id="policy" pattern="[a-zA-Z0-9]"><br>
        <span id="error-policy" class="error"></span>
        <h3>Additional Informatiom</h3>

        <label>Username:</label>
        <input type="text" id="username"><br>
        <span id="error-username" class="error"></span>

        <label>Password:</label>
        <input type="password" id="password"><br>  
        <span id="error-password" class="error"></span>     
        <label>Confirm Password:</label>
        <input type="password" id="confirmPassword"><br>

        <input type="submit" value="Register">
    </form>

</body>
</html>