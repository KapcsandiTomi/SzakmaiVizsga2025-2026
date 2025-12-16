document.addEventListener("DOMContentLoaded", function() {

    const searchInput = document.getElementById("searchInput");
    const searchButton = document.getElementById("searchButton");
    const suggestionsList = document.getElementById("suggestions");

    const products = Array.from(document.querySelectorAll(".card .contentBx h2"))
        .map(el => el.textContent);

    function showSuggestions() {
        const query = searchInput.value.toLowerCase().trim();
        suggestionsList.innerHTML = "";

        if (!query) {
            suggestionsList.style.display = "none";
            return;
        }

        const filtered = products.filter(item =>
            item.toLowerCase().includes(query)
        );

        if (filtered.length === 0) {
            suggestionsList.style.display = "none";
            return;
        }

        filtered.forEach(item => {
            const li = document.createElement("li");
            li.textContent = item;

            li.addEventListener("click", function() {
                searchInput.value = item;
                suggestionsList.style.display = "none";
                handleSearch();
            });

            suggestionsList.appendChild(li);
        });

        suggestionsList.style.display = "block";
    }

    function handleSearch() {
        const query = searchInput.value.toLowerCase().trim();
        const productElements = document.querySelectorAll(".card .contentBx h2");

        if (!query) {
            alert("Please enter a product name!");
            return;
        }

        let found = false;
        productElements.forEach(el => {
            if (el.textContent.toLowerCase() === query) {
                el.scrollIntoView({ behavior: "smooth", block: "center" });
                el.parentElement.classList.add("highlight"); // extra vizuális kiemelés
                setTimeout(() => el.parentElement.classList.remove("highlight"), 2000);
                found = true;
            }
        });

        if (!found) {
            alert("No matching product found!");
        }
    }

    searchButton.addEventListener("click", handleSearch);
    searchInput.addEventListener("keyup", showSuggestions);

    searchInput.addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            suggestionsList.style.display = "none";
            handleSearch();
        }
    });

    document.addEventListener("click", function(e) {
        if (!e.target.closest(".search-box")) {
            suggestionsList.style.display = "none";
        }
    });
});
