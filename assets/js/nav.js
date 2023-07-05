
fetch('../../src/Views/Navigations.php')
    .then(response => response.text())
    .then(html => {
        document.getElementById('navigation-placeholder').innerHTML = html;
    });
