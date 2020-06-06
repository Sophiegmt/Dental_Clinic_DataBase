<html>
<body><h2>Search Client:</h2>
    <form action="search_client.php" method="post">
        <p>Client VAT:
            <input type="number" min="0" name="VAT_client" />
        </p>
        <p>Client Name:
            <input type="text" name="client_name" maxlength='39' />
        </p>
        <p>Client Address:
            <input type="text" name="client_addr" maxlength='34'/>
        </p>
        <p><input type="submit" value="Search"/></p>
        </form>
	<form action='new_client_form.php' method='post'>
        <input type='submit' value='Add new client'/>
        </form>
        <form action='initial_page.php' method='post'>
        <input type='submit' value='Go To Homepage'/>
        </form>
</body>
</html>
