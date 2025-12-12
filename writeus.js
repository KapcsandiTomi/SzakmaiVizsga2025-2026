const form = document.getElementById("contactForm");
const statusDiv = document.getElementById("form-status");

form.addEventListener("submit", async (e) => {
  e.preventDefault();
  statusDiv.textContent = "⏳ Sending to the staff, don't exit...";
  statusDiv.style.color = "black";

  const formData = new FormData(form);
  const object = Object.fromEntries(formData);
  const json = JSON.stringify(object);

  try {
    const res = await fetch(form.action, {
      method: form.method,
      headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
      },
      body: json,
    });

    const data = await res.json();

    if (data.success) {
      statusDiv.textContent = "✅ You sent the message to our staff team!";
      statusDiv.style.color = "green";
      form.reset();
    } else {
      statusDiv.textContent = "❌ Something wrong!: " + (data.message || "Try again later!");
      statusDiv.style.color = "red";
    }
  } catch (error) {
    statusDiv.textContent = "⚠️ Internal network error: " + error.message;
    statusDiv.style.color = "orange";
  }
});