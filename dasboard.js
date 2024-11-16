document.addEventListener('DOMContentLoaded', function () {
    // Add event listeners to the sidebar links
    const sidebarLinks = document.querySelectorAll('aside nav a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent default anchor click behavior
            const href = this.getAttribute('href');
            if (href) {
                window.location.href = href; // Redirect to the specified page
            }
        });
    });

    // Add functionality to the buttons
    document.querySelectorAll('button').forEach(button => {
        button.addEventListener('click', function () {
            const destination = this.getAttribute('onclick');
            if (destination) {
                eval(destination.replace(/window\.location\.href='([^']+)';/, 'window.location.href = "$1";'));
            }
        });
    });
});
