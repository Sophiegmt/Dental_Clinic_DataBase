<html>
<body><h2>Add Client:</h2>
    <form action="new_client.php" method="post">
        <p>VAT:
            <input type="number" min="1" name="VAT_client" required/>
        </p>
        <p>Name:
            <input type="text" name="client_name" maxlength='39' required/>
        </p>
        <p>Birthdate:
            <input type="date" name="client_birthdate" max="<?php echo date("Y-m-d");?>" min="<?php echo date('Y-m-d', strtotime("1910-01-01"));?>" maxlength='34' required/>
        </p>
        <p>Street:
            <input type="text" name="client_street" maxlength='34' required/>
        </p>
        <p>City:
            <input type="text" name="client_city" maxlength='34' required/>
        </p>
        <p>ZIP:
            <input type="text" name="client_zip" maxlength='34' required/>
        </p>
        <p>Phone Number:
            <input type="number" name="client_phone" maxlength='34' required/>
        </p>
        <p>Gender:
            <select name="client_gender">
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </p>
        <p></p>
        <p><input type="submit" value="Add"/></p>
        </form>
         <a href="javascript:history.go(-1)">
            <input type='submit' value='Go Back'/></a>
        </form>
        <p></p>
        <form action='initial_page.php' method='post'>
        <input type='submit' value='Go to Homepage'/>
        </form>
</body>
</html>
