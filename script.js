// Fetch CSRF token from the server
async function fetchCSRFToken() {
    const response = await fetch('get-csrf-token.php'); // Assuming this file generates a CSRF token
    const data = await response.json();
    document.getElementById('csrf_token').value = data.csrf_token;
}

// Handle job posting form submission
document.getElementById('jobForm').addEventListener('submit', async (e) => {
    e.preventDefault(); // Prevent the form from submitting the traditional way

    // Get form data
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());

    // Send data to the backend
    try {
        const response = await fetch('post-job.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        });

        const result = await response.json();

        if (result.success) {
            // Show success message
            alert(result.success);
            // Clear the form
            e.target.reset();
            // Fetch a new CSRF token
            fetchCSRFToken();
        } else {
            alert(result.error || 'Failed to post job. Please try again.');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
});

// Handle search form submission
document.getElementById('searchForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const location = document.querySelector('input[name="location"]').value;
    const keyword = document.querySelector('input[name="keyword"]').value;
    fetchJobs(location, keyword, 1);
});

// Autocomplete for location input
function setupAutocomplete(inputId, type) {
    const input = document.getElementById(inputId);
    const listContainer = document.createElement("div");
    listContainer.classList.add("autocomplete-items");
    input.parentNode.appendChild(listContainer);

    input.addEventListener("input", function () {
        let val = this.value.trim();
        if (!val) {
            listContainer.innerHTML = "";
            return;
        }

        fetch(`search-jobs.php?type=${type}&search=${val}`)
            .then(response => response.json())
            .then(data => {
                listContainer.innerHTML = "";
                if (data.length > 0) {
                    data.forEach(location => {
                        let div = document.createElement("div");
                        div.textContent = location;
                        div.addEventListener("click", function () {
                            input.value = location;
                            listContainer.innerHTML = "";
                        });
                        listContainer.appendChild(div);
                    });
                } else {
                    listContainer.innerHTML = "<div>No locations found</div>";
                }
            })
            .catch(error => {
                console.error("Error fetching suggestions:", error);
                listContainer.innerHTML = "<div>Error fetching suggestions</div>";
            });
    });

    document.addEventListener("click", function (e) {
        if (!listContainer.contains(e.target) && e.target !== input) {
            listContainer.innerHTML = "";
        }
    });
}

// Fetch and display jobs
async function fetchJobs(location = '', keyword = '', page = 1) {
    const response = await fetch(`search-jobs.php?location=${location}&keyword=${keyword}&page=${page}`);
    const data = await response.json();

    const resultsDiv = document.getElementById('results');
    resultsDiv.innerHTML = '';
    if (data.jobs && data.jobs.length > 0) {
        data.jobs.forEach(job => {
            const jobDiv = document.createElement('div');
            jobDiv.className = 'job';
            jobDiv.innerHTML = `
                <h3>${job.title}</h3>
                <p>${job.description}</p>
                <p><strong>Location:</strong> ${job.location}</p>
                <p><strong>Contact:</strong> ${job.contact_email}</p>
            `;
            resultsDiv.appendChild(jobDiv);
        });
    } else {
        resultsDiv.innerHTML = '<p>No jobs found.</p>';
    }

    const paginationDiv = document.getElementById('pagination');
    paginationDiv.innerHTML = '';
    if (data.pagination && data.pagination.total_pages > 1) {
        for (let i = 1; i <= data.pagination.total_pages; i++) {
            const link = document.createElement('a');
            link.href = `#`;
            link.textContent = i;
            link.onclick = (event) => {
                event.preventDefault();
                fetchJobs(location, keyword, i);
            };
            paginationDiv.appendChild(link);
        }
    }
}

// Load initial data
document.addEventListener("DOMContentLoaded", function () {
    fetchCSRFToken();
    setupAutocomplete("location", "location");
    // Function to fetch autocomplete suggestions
function fetchSuggestions(input, suggestionsDiv) {
    const term = input.value;
    if (term.length > 0) {
        console.log("Fetching suggestions for term:", term); // Debugging
        fetch(`fetch-suggestions.php?term=${encodeURIComponent(term)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.json();
            })
            .then(data => {
                console.log("Suggestions received:", data); // Debugging
                suggestionsDiv.innerHTML = '';
                data.forEach(item => {
                    const suggestion = document.createElement('div');
                    suggestion.textContent = item;
                    suggestion.onclick = () => {
                        input.value = item;
                        suggestionsDiv.innerHTML = '';
                    };
                    suggestionsDiv.appendChild(suggestion);
                });
            })
            .catch(error => {
                console.error("Error fetching suggestions:", error); // Debugging
            });
    } else {
        suggestionsDiv.innerHTML = '';
    }
}
// Function to fetch autocomplete suggestions
function fetchSuggestions(input, suggestionsDiv) {
    const term = input.value;
    if (term.length > 0) {
        console.log("Fetching suggestions for term:", term); // Debugging
        fetch(`fetch-suggestions.php?term=${encodeURIComponent(term)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error("Network response was not ok");
                }
                return response.text(); // First, get the raw response as text
            })
            .then(text => {
                console.log("Raw response:", text); // Debugging: Log the raw response
                try {
                    const data = JSON.parse(text); // Parse the response as JSON
                    console.log("Parsed JSON:", data); // Debugging: Log the parsed JSON
                    suggestionsDiv.innerHTML = '';
                    data.forEach(item => {
                        const suggestion = document.createElement('div');
                        suggestion.textContent = item;
                        suggestion.onclick = () => {
                            input.value = item;
                            suggestionsDiv.innerHTML = '';
                        };
                        suggestionsDiv.appendChild(suggestion);
                    });
                } catch (error) {
                    console.error("Error parsing JSON:", error); // Debugging: Log JSON parsing errors
                }
            })
            .catch(error => {
                console.error("Error fetching suggestions:", error); // Debugging: Log fetch errors
            });
    } else {
        suggestionsDiv.innerHTML = '';
    }
}
});
