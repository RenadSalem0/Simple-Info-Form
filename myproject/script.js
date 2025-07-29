document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            toggleStatus(id);
        });
    });
});

function toggleStatus(id) {
    if (!id) return;

    fetch(`toggle.php?id=${encodeURIComponent(id)}`)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.text(); // نستخدم text بدل json
        })
        .then(status => {
            const statusElement = document.getElementById(`status-${id}`);
            if (statusElement) {
                statusElement.textContent = status;
                statusElement.setAttribute('data-status', status);
                statusElement.classList.add('status-change');
                setTimeout(() => {
                    statusElement.classList.remove('status-change');
                }, 500);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}
