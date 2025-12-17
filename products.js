// MEGVÁRJUK, AMÍG A DOM TELJESEN BETÖLTŐDIK
document.addEventListener("DOMContentLoaded", function() {

    // A KERESŐMEZŐ, A KERESÉS GOMB ÉS A JAVASLATLISTA LEKÉRÉSE
    const searchInput = document.getElementById("searchInput");
    const searchButton = document.getElementById("searchButton");
    const suggestionsList = document.getElementById("suggestions");

    // AZ ÖSSZES TERMÉKNÉV KIGYŰJTÉSE A KÁRTYÁKBÓL EGY TÖMBBE
    const products = Array.from(document.querySelectorAll(".card .contentBx h2"))
        .map(el => el.textContent);

    // JAVASLATOK MEGJELENÍTÉSE A BEÍRT SZÖVEG ALAPJÁN
    function showSuggestions() {
        // A FELHASZNÁLÓ ÁLTAL BEÍRT SZÖVEG KISBETŰSÍTÉSE ÉS LEVÁGÁSA
        const query = searchInput.value.toLowerCase().trim();
        suggestionsList.innerHTML = "";

        // HA NINCS BEÍRVA SEMMI, ELREJTJÜK A JAVASLATOKAT
        if (!query) {
            suggestionsList.style.display = "none";
            return;
        }

        // A TERMÉKEK SZŰRÉSE A BEÍRT SZÖVEG ALAPJÁN
        const filtered = products.filter(item =>
            item.toLowerCase().includes(query)
        );

        // HA NINCS TALÁLAT, ELREJTJÜK A JAVASLATOKAT
        if (filtered.length === 0) {
            suggestionsList.style.display = "none";
            return;
        }

        // TALÁLATOK LISTÁZÁSA
        filtered.forEach(item => {
            const li = document.createElement("li");
            li.textContent = item;

            // KATTINTÁS ESETÉN KITÖLTI A KERESŐMEZŐT ÉS ELINDÍTJA A KERESÉST
            li.addEventListener("click", function() {
                searchInput.value = item;
                suggestionsList.style.display = "none";
                handleSearch();
            });

            suggestionsList.appendChild(li);
        });

        // JAVASLATLISTA MEGJELENÍTÉSE
        suggestionsList.style.display = "block";
    }

    // A KERESÉS LOGIKÁJA
    function handleSearch() {
        const query = searchInput.value.toLowerCase().trim();
        const productElements = document.querySelectorAll(".card .contentBx h2");

        // HA A KERESŐ ÜRES, FIGYELMEZTETÉS
        if (!query) {
            alert("PLEASE ENTER A PRODUCT NAME!");
            return;
        }

        let found = false;

        // VÉGIGMEGYÜNK AZ ÖSSZES TERMÉKEN
        productElements.forEach(el => {
            if (el.textContent.toLowerCase() === query) {
                // GÖRGETÉS A MEGTALÁLT TERMÉKHEZ
                el.scrollIntoView({ behavior: "smooth", block: "center" });

                // KIEMELÉS HOZZÁADÁSA
                el.parentElement.classList.add("highlight");

                // KIEMELÉS ELTÁVOLÍTÁSA 2 MÁSODPERC UTÁN
                setTimeout(() => el.parentElement.classList.remove("highlight"), 2000);

                found = true;
            }
        });

        // HA NINCS TALÁLAT
        if (!found) {
            alert("NO MATCHING PRODUCT FOUND!");
        }
    }

    // KERESÉS GOMB KATTINTÁS
    searchButton.addEventListener("click", handleSearch);

    // GÉPELÉS KÖZBEN JAVASLATOK MUTATÁSA
    searchInput.addEventListener("keyup", showSuggestions);

    // ENTER LENYOMÁSÁRA KERESÉS INDÍTÁSA
    searchInput.addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            e.preventDefault();
            suggestionsList.style.display = "none";
            handleSearch();
        }
    });

    // KATTINTÁS AZ OLDALON KÍVÜL → JAVASLATLISTA ELTŰNIK
    document.addEventListener("click", function(e) {
        if (!e.target.closest(".search-box")) {
            suggestionsList.style.display = "none";
        }
    });
});
