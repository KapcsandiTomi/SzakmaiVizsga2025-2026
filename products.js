document.addEventListener("DOMContentLoaded", function() {

  const searchInput = document.getElementById("searchInput");
  const searchButton = document.getElementById("searchButton");
  const suggestionsList = document.getElementById("suggestions");

  const products = [
    "Logitech PRO X Wired Headset",
    "Logitech Astro A20 X",
    "Logitech PRO Pro G733",
    "Logitech PRO Pro G735",
    "Logitech PRO X 2",
    "ASUS ROG Delta S Wireless",
    "ASUS ROG Delta II",
    "ASUS ROG Strix Go Core",
    "ASUS ROG Fusion II 500",
    "ASUS ROG STRIX",
    "EXCALIBUR Black",
    "WAKIZASHI 2 HR White",
    "NAGAMAKI WHITE US",
    "WAKIZASHI 2 US Gray/Black",
    "SHINOBI 2 White CZ/SK",
    "ANTONIUM K745 PROk",
    "FIZZ K617",
    "EISA K686 HE Rapid Trigger",
    "EISA K686 PRO SE",
    "ARTEMIS K719 PRO",
    "Odyssey 180Hz gaming monitor",
    "32\" Vision AI Smart monitor M7",
    "27\" Odyssey OLED G6",
    "37\" ViewFinity S8 S80UD",
    "55\" Odyssey Ark G9 G97NC",
    "MPG 271QR QD-OLED X50",
    "MAG 274QPF X32",
    "MAG 275QF E20",
    "MAG 272QPF E20",
    "MAG 272QP QD-OLED X24",
    "Razer Viper V3 Pro",
    "Razer DeathAdder V4 Pro",
    "Razer Naga V2 Pro",
    "Razer Naga Left-Handed Edition",
    "Razer Pro Click V2 Vertical",
    "PRO X SUPERLIGHT 2",
    "PRO X SUPERLIGHT 2C",
    "PRO X2 SUPERSTRIKE",
    "G502 X PLUS",
    "G502 X LIGHTSPEED"
  ];

  function showSuggestions() {
    const query = searchInput.value.toLowerCase();
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

    let found = false;
    productElements.forEach(el => {
      if (el.textContent.toLowerCase() === query) {
        el.scrollIntoView({ behavior: "smooth", block: "center" });
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
