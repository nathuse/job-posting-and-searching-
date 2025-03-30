document.addEventListener('DOMContentLoaded', () => {
    const keywordInput = document.getElementById('keywordSearch');
    const locationInput = document.getElementById('locationSearch');
    const searchForm = document.getElementById('searchForm');
    const resultsContainer = document.getElementById('searchResults');
    const errorContainer = document.getElementById('errorMessage');

    // Location suggestions based on keyword
    keywordInput.addEventListener('change', async () => {
        const keyword = keywordInput.value.trim();
        if (keyword) {
            const response = await fetch(`search-jobs.php?type=location&keyword=${encodeURIComponent(keyword)}`);
            const locations = await response.json();
            setupLocationAutocomplete(locations);
        }
    });

    function setupLocationAutocomplete(availableLocations) {
        const locationResults = document.createElement('div');
        locationResults.className = 'autocomplete-results';
        locationInput.parentNode.appendChild(locationResults);

        locationInput.addEventListener('input', () => {
            const searchTerm = locationInput.value.toLowerCase();
            locationResults.innerHTML = '';

            if (searchTerm.length < 1) return;

            const matches = availableLocations.filter(location => 
                location.toLowerCase().includes(searchTerm)
            );

            matches.forEach(location => {
                const div = document.createElement('div');
                div.className = 'suggestion-item';
                div.textContent = location;
                div.addEventListener('click', () => {
                    locationInput.value = location;
                    locationResults.innerHTML = '';
                });
                locationResults.appendChild(div);
            });
        });
    }

    // Handle form submission
    searchForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        errorContainer.style.display = 'none';
        
        const keyword = keywordInput.value.trim();
        const location = locationInput.value.trim();

        if (!keyword && !location) {
            errorContainer.textContent = 'Please enter a job title or location';
            errorContainer.style.display = 'block';
            return;
        }

        try {
            const response = await fetch(`search-jobs.php?keyword=${encodeURIComponent(keyword)}&location=${encodeURIComponent(location)}`);
            const data = await response.json();

            if (data.error) {
                errorContainer.textContent = data.error;
                errorContainer.style.display = 'block';
                return;
            }

            displaySearchResults(data.jobs);
        } catch (error) {
            errorContainer.textContent = 'An error occurred while searching. Please try again.';
            errorContainer.style.display = 'block';
        }
    });

    function displayResults(jobs) {
        resultsContainer.innerHTML = '';
        
        if (jobs.length === 0) {
            resultsContainer.innerHTML = '<div class="no-results">No jobs found matching your criteria</div>';
            return;
        }
    
        jobs.forEach(job => {
            const jobCard = document.createElement('div');
            jobCard.className = 'job-card';
            jobCard.innerHTML = `
                <h3>${job.title}</h3>
                <div class="location">
                    <i class="fas fa-map-marker-alt"></i>
                    ${job.location}
                </div>
                <div class="description">${job.description}</div>
                <a href="mailto:${job.contact_email}" class="apply-btn">
                    <i class="fas fa-paper-plane"></i> Apply Now
                </a>
            `;
            resultsContainer.appendChild(jobCard);
        });
    }
    
});
