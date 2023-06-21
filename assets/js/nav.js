
fetch('../../src/Views/Navigations.html')
    .then(response => response.text())
    .then(html => {
        document.getElementById('navigation-placeholder').innerHTML = html;
    });
