<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Practical 14 - AJAX Live Search</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="page">
        <section class="panel">
            <h1>Live Search - Student Records</h1>
            <p class="hint">Start typing student name or enrollment number to fetch matching results instantly.</p>

            <label for="searchInput">Search</label>
            <input type="text" id="searchInput" placeholder="Type name or enrollment number..." autocomplete="off">

            <p id="statusMessage" class="status-message"></p>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Enrollment No</th>
                            <th>Name</th>
                            <th>Branch</th>
                            <th>Email</th>
                            <th>Phone</th>
                        </tr>
                    </thead>
                    <tbody id="resultsBody">
                        <tr>
                            <td colspan="5" class="empty-row">Loading records...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <script src="script.js"></script>
</body>
</html>
