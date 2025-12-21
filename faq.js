// MEGVÁRJUK, AMÍG A DOM TELJESEN BETÖLTŐDIK
document.addEventListener("DOMContentLoaded", () => {

  // AZ ÖSSZES GYIK (FAQ) KÉRDÉS ELEM KIVÁLASZTÁSA
  const questions = document.querySelectorAll(".faq-question");

  // VÉGIGMEGYÜNK AZ ÖSSZES KÉRDÉSEN
  questions.forEach(question => {

    // KATTINTÁSI ESEMÉNY HOZZÁADÁSA MINDEN KÉRDÉSHEZ
    question.addEventListener("click", () => {

      // AKTÍV OSZTÁLY KI- ÉS BEKAPCSOLÁSA (PL. STÍLUSOZÁSHOZ)
      question.classList.toggle("active");

      // A KÉRDÉSHEZ TARTOZÓ VÁLASZ (A KÖVETKEZŐ HTML ELEM)
      const answer = question.nextElementSibling;

      // AZ IKON KIVÁLASZTÁSA (+ / -)
      const icon = question.querySelector(".icon");

      // HA A VÁLASZ JELENLEG LÁTHATÓ
      if (answer.style.display === "block") {

        // VÁLASZ ELREJTÉSE
        answer.style.display = "none";

        // IKON VISSZAÁLLÍTÁSA PLUSZ JELRE
        icon.textContent = "+";

      } else {

        // VÁLASZ MEGJELENÍTÉSE
        answer.style.display = "block";

        // IKON MÍNUSZ JELRE VÁLTÁSA
        icon.textContent = "-";
      }
    });
  });

});
