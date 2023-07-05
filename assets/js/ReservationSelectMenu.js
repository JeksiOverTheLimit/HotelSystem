function fetchCitiesByCountry() {
    const countryId = document.getElementById('countries').value;
    fetch(`../Controllers/ReservationPageController.php?Reservation&countryId=${countryId}`)
        .then(response => response.json())
        .then(result => {
            const citiesSelect = document.getElementById("cities");
            citiesSelect.innerHTML = "";

            for (let i = 0; i < result.length; i++) {
                const cityOption = document.createElement("option");
                cityOption.value = result[i].id;
                cityOption.text = result[i].name;
                citiesSelect.appendChild(cityOption);
            }
        })
        .catch(error => console.error(error));
}
