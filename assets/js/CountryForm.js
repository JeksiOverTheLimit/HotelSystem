fetch('../../src/Views/country_form.php')
    .then(response => response.text())
    .then(html => {
        document.getElementById('form').innerHTML = html;
    });
