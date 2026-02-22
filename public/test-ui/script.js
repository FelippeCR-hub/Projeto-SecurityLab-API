const baseUrl = "http://localhost:8000";

async function apiRequest(endpoint, data) {
  const response = await fetch(baseUrl + endpoint, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(data)
  });
  const result = await response.json();
  document.getElementById("response").textContent =
    JSON.stringify(result, null, 2);
}

document.getElementById("formRegister").addEventListener("submit", e => {
  e.preventDefault();
  const form = e.target;
  apiRequest("/auth/register", {
    name: form.name.value,
    email: form.email.value,
    password: form.password.value
  });
});

document.getElementById("formLogin").addEventListener("submit", e => {
  e.preventDefault();
  const form = e.target;
  apiRequest("/auth/login", {
    email: form.email.value,
    password: form.password.value
  });
});

document.getElementById("formWeakHash").addEventListener("submit", e => {
  e.preventDefault();
  const form = e.target;
  apiRequest("/demo/create-weak-hash", {
    user_id: Number(form.user_id.value),
    algo: form.algo.value,
    demo_password: form.demo_password.value
  });
});

document.getElementById("formDict").addEventListener("submit", e => {
  e.preventDefault();
  const form = e.target;
  apiRequest("/demo/attack-dictionary", {
    demo_hash_id: Number(form.demo_hash_id.value)
  });
});
