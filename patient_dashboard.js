
// Function to hide all sections
function hideAllSections() {
    document.querySelectorAll('.section').forEach(function(section) {
        section.classList.add('hidden');
    });
}

// Function to hide all sections
function hideAllSections() {
    const sections = document.querySelectorAll('.section');
    sections.forEach(function(section) {
        section.classList.add('hidden');
    });
}

// Function to show the selected section and hide others
function showSection(sectionId) {
    // Hide all sections
    document.querySelectorAll('.section').forEach(section => {
        section.style.display = 'none';
    });
    
    // Show the targeted section
    document.getElementById(sectionId).style.display = 'block';
}

// Add event listeners for each tab button
document.getElementById('appointments-tab').addEventListener('click', function () {
    showSection('appointments-section');
});

document.getElementById('health-data-tab').addEventListener('click', function () {
    showSection('health-data-section');
});

document.getElementById('communication-tab').addEventListener('click', function () {
    showSection('communication-section');
});

document.getElementById('feedback-tab').addEventListener('click', function () {
    showSection('feedback-section');
});


// Tab Navigation
document.getElementById('appointments-tab').addEventListener('click', function () {
    document.getElementById('appointments-section').classList.remove('hidden');
    document.getElementById('health-data-section').classList.add('hidden');
    document.getElementById('communication-section').classList.add('hidden');
    document.getElementById('feedback-section').classList.add('hidden');
});

document.getElementById('health-data-tab').addEventListener('click', function () {
    document.getElementById('appointments-section').classList.add('hidden');
    document.getElementById('health-data-section').classList.remove('hidden');
    document.getElementById('-section').classList.add('hidden');
    document.getElementById('video-calls-section').classList.add('hidden');
});

document.getElementById('messages-tab').addEventListener('click', function () {
    document.getElementById('appointments-section').classList.add('hidden');
    document.getElementById('health-data-section').classList.add('hidden');
    document.getElementById('messages-section').classList.remove('hidden');
    document.getElementById('video-calls-section').classList.add('hidden');
});

document.getElementById('video-calls-tab').addEventListener('click', function () {
    document.getElementById('appointments-section').classList.add('hidden');
    document.getElementById('health-data-section').classList.add('hidden');
    document.getElementById('messages-section').classList.add('hidden');
    document.getElementById('video-calls-section').classList.remove('hidden');
});

// File Download
async function fetchFiles() {
    try {
        const response = await fetch('fetch_files.php'); // URL of the PHP backend script
        const files = await response.json();

        const downloadSection = document.getElementById('download-section');

        // Display each file with a download button
        files.forEach(file => {
            const fileElement = document.createElement('div');
            fileElement.classList.add('flex', 'justify-between', 'items-center', 'p-2', 'border', 'border-gray-300', 'rounded');

            const fileName = document.createElement('span');
            fileName.textContent = file.file_name;
            fileElement.appendChild(fileName);

            const downloadButton = document.createElement('a');
            downloadButton.textContent = 'Download';
            downloadButton.href = `download.php?file_path=${file.file_path}`; // Direct link to download the file
            downloadButton.classList.add('bg-blue-500', 'text-white', 'px-4', 'py-2', 'rounded', 'hover:bg-blue-600');
            downloadElement.appendChild(downloadButton);

            downloadSection.appendChild(fileElement);
        });
    } catch (error) {
        console.error('Error fetching files:', error);
    }
}

// Fetch files on page load
fetchFiles();

 

// Video call simulation (for demonstration)
document.getElementById('start-call-btn').addEventListener('click', function () {
    var videoContainer = document.getElementById('video-call-container');
    videoContainer.innerHTML = '<p>Connecting to video call...</p>';
    setTimeout(function () {
        videoContainer.innerHTML = '<p>Video call in progress...</p>';
    }, 2000);
});
// JavaScript to handle tab switching
document.addEventListener("DOMContentLoaded", function() {
    const tabs = document.querySelectorAll(".tab-button");
    const sections = document.querySelectorAll(".section");

    tabs.forEach(tab => {
        tab.addEventListener("click", function() {
            // Remove active class from all sections and tabs
            sections.forEach(section => section.classList.remove("active"));
            tabs.forEach(tab => tab.classList.remove("bg-opacity-50"));

            // Get the corresponding section ID from the tab's ID
            const sectionId = tab.id.replace("-tab", "-section");
            document.getElementById(sectionId).classList.add("active");
            tab.classList.add("bg-opacity-50"); // Highlight active tab
        });
    });
});

