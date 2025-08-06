<!DOCTYPE html>
<html>
<head>
    <title>AIUB Clinic</title>
</head>

<body>
    <h1>AIUB CLINIC</h1>
    
    <form onsubmit="return validateForm(event)">
        <h3> Customer Registration Form</h3>
        
        <label>Full Name:</label>
        <input type="text" id="fullname"><br>

        <label>Age:</label>
        <input type="number" id-"age"><br>

        <label>Phone Number:</label>
        <input type="text" id="phone"><br>

        <label>Email:</label>
        <input type="email" id="email"><br>

        <label>Insurance Provider:</label>
        <select id="insurance">
            <option>Select</option>
            <option>Metlife</option>
            <option>Green Delta</option>
            <option>City Insurance</option>
        </select><br>

        <label>Insurance Policy Number:</label>
        <input type="text" id="policy" pattern="[a-zA-Z0-9]"><br>
        <h3>Additional Informatiom</h3>

        <label>Username:</label>
        <input type="text" id="username"><br>

        <label>Password:</label>
        <input type="password" id="password"><br>       
        <label>Confirm Password:</label>
        <input type="password" id="confirmPassword"><br>

        <input type="submit" value="Register">
    </form>

</body>
</html>










