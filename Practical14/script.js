var searchInput = document.getElementById('searchInput');
var resultsBody = document.getElementById('resultsBody');
var statusMessage = document.getElementById('statusMessage');
var debounceTimer;

function renderRows(records) {
    if (!records || records.length === 0) {
        resultsBody.innerHTML = '<tr><td colspan="5" class="empty-row">No records found.</td></tr>';
        return;
    }

    var rows = records.map(function (item) {
        return '<tr>' +
            '<td>' + escapeHtml(item.enrollment_no) + '</td>' +
            '<td>' + escapeHtml(item.full_name) + '</td>' +
            '<td>' + escapeHtml(item.branch) + '</td>' +
            '<td>' + escapeHtml(item.email) + '</td>' +
            '<td>' + escapeHtml(item.phone) + '</td>' +
            '</tr>';
    });

    resultsBody.innerHTML = rows.join('');
}

function escapeHtml(value) {
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function fetchResults() {
    var query = searchInput.value.trim();
    statusMessage.textContent = 'Searching...';

    fetch('search.php?q=' + encodeURIComponent(query))
        .then(function (response) {
            return response.json();
        })
        .then(function (payload) {
            if (!payload.success) {
                statusMessage.textContent = payload.message || 'Unable to fetch records.';
                statusMessage.className = 'status-message error';
                renderRows([]);
                return;
            }

            statusMessage.textContent = payload.data.length + ' record(s) found.';
            statusMessage.className = 'status-message success';
            renderRows(payload.data);
        })
        .catch(function () {
            statusMessage.textContent = 'Request failed. Please check database connection.';
            statusMessage.className = 'status-message error';
            renderRows([]);
        });
}

searchInput.addEventListener('keyup', function () {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(fetchResults, 300);
});

fetchResults();
