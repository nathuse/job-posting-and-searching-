function setupAutocomplete(inputId, type) {
    const input = document.getElementById(inputId);
    const resultsDiv = document.createElement('div');
    resultsDiv.className = 'autocomplete-results';
    input.parentNode.appendChild(resultsDiv);

    input.addEventListener('input', async () => {
        const searchTerm = input.value.trim();
        if (searchTerm.length < 2) {
            resultsDiv.innerHTML = '';
            return;
        }

        try {
            const response = await fetch(`search-jobs.php?type=${type}&search=${encodeURIComponent(searchTerm)}`);
            const data = await response.json();
            
            resultsDiv.innerHTML = '';
            data.forEach(item => {
                const div = document.createElement('div');
                div.className = 'suggestion-item';
                div.textContent = item;
                div.addEventListener('click', () => {
                    input.value = item;
                    resultsDiv.innerHTML = '';
                });
                resultsDiv.appendChild(div);
            });
        } catch (error) {
            console.error('Error fetching suggestions:', error);
        }
    });

    // Close suggestions when clicking outside
    document.addEventListener('click', (e) => {
        if (!input.contains(e.target)) {
            resultsDiv.innerHTML = '';
        }
    });
}
