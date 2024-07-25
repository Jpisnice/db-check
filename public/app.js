let DBs = [];

function showDbs(user,pass){
    const username = user;
        const password = pass;

        fetch('http://localhost/get_user_databases.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                'username': username,
                'password': password
            })
        })
        .then(response => response.json())
        .then(data => {
            // Log the JSON response to the console
            console.log(data);
            // Process the data as needed
        })
        .catch(error => console.error('Error fetching data:', error));
}

function compareTables(db1,db2){
    //compare each and every db for matching schema and data types of colums if matched names of columns
    //write 
}

function getSchema(dbName){
    //get schema of the database

}