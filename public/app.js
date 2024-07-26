async function fetchDatabases() {
    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;

    const response = await fetch('http://localhost/dbcompare/get_user_databases.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'username': username,
            'password': password
        })
    });

    const data = await response.json();

    if (data.error) {
        alert(data.error);
        return;
    }

    const dbSelect1 = document.getElementById('database1');
    const dbSelect2 = document.getElementById('database2');

    dbSelect1.innerHTML = '';
    dbSelect2.innerHTML = '';

    data.databases.forEach(db => {
        const option1 = document.createElement('option');
        option1.value = db;
        option1.textContent = db;

        const option2 = option1.cloneNode(true);

        dbSelect1.appendChild(option1);
        dbSelect2.appendChild(option2);
    });

    document.getElementById('dbSelectionForm').style.display = 'block';
}

async function compareDatabases(event) {
    event.preventDefault();

    const username = document.getElementById('username').value;
    const password = document.getElementById('password').value;
    const database1 = document.getElementById('database1').value;
    const database2 = document.getElementById('database2').value;

    const response = await fetch('http://localhost/dbcompare/compare_databases.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({
            'username': username,
            'password': password,
            'database1': database1,
            'database2': database2
        })
    });

    const data = await response.json();
    console.log(data);

    // Process and display the comparison result
}
