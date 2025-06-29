const API_KEY = "AIzaSyAFPE5Ztgq7byRBnCrfODzcaCKbhGEJhzQ"; // ← حط مفتاحك هنا

async function handleSend() {
  const input = document.getElementById("input").value;

const res = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=${API_KEY}`, {

    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      contents: [{ parts: [{ text: input }] }]
    })
  });

  const data = await res.json();
  document.getElementById("response").innerText = data.candidates?.[0]?.content?.parts?.[0]?.text || "لا يوجد رد";
}
