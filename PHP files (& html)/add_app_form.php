<html>
<body>
    
<h2>Choose Date:</h2>
    <form action="search_doc.php" method="post">
        <p>Appointment Date:
            <input type="date" name="app_date" min='<?php echo date("Y-m-d");?>' required/>
        </p>
        <p>Appointment Hour:
            <input type="time" name="app_time"  min="09:00:00" max="17:00:00" step="3600" required/>
            <small>Office hours are 9am to 5pm</small>
        </p>
        <p> 
            <input type=hidden name='VAT_client' value='<?php echo (integer)$_REQUEST['VAT_client'];?>' required/>
        </p>
        <p>Description: 
            <input type=Text name='description' required/>
        </p>
        <p>
            <input type="submit" value="Search For Doctor"/>
        </p>
        </form>
        <a href="javascript:history.go(-1)">
            <input type='submit' value='Go Back'/></a>
        </form>
</body>
</html>
