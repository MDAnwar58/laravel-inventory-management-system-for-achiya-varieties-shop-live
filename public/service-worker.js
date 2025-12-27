let Products = [];
let audioAllowed = false;

const setProducts = (data) => (Products = data);

if (audioAllowed === false) {
    // Unlock audio on first user click
    document.querySelector("body").addEventListener("click", () => {
        audioAllowed = true;
        //console.log("Audio");
        // alert("Audio playback enabled for notifications!");
    });
}

// Load products and schedule notifications
document.addEventListener("DOMContentLoaded", () => {
    getLowStockProducts();
});

// Fetch low-stock products from Laravel route
async function getLowStockProducts() {
    try {
        const res = await fetch("/admin/low-stocks");
        if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
        const products = await res.json();
        if (products.length > 0) {
            setProducts(products);
            setVoiceList(products);
            alertProductsStockLow();
        }
    } catch (error) {
        console.error(error);
    }
}

// Display audio elements for each product (if using MP3)
function setVoiceList(prods) {
    const voiceList = document.getElementById("voice-list");
    voiceList.innerHTML = "";
    prods.forEach((p) => {
        if (p.id && p.voice_alert) {
            voiceList.innerHTML += `<audio class="ttsAudio" data-product-id="${p.id}">
                <source src="${p.voice_alert}" type="audio/mpeg">
            </audio>`;
        }
    });
}

// Schedule notifications at target times
function alertProductsStockLow() {
    const targetTimes = [
        "11:00AM",
        "11:08AM",
        "11:09AM",
        "12:40AM",
        "03:00PM",
        "09:49PM",
    ];
    setInterval(() => {
        const now = new Date();
        let hours = now.getHours();
        const minutes = now.getMinutes().toString().padStart(2, "0");
        const ampm = hours >= 12 ? "PM" : "AM";
        hours = hours % 12 || 12;
        const formattedHours = hours.toString().padStart(2, "0");
        const currentTime = `${formattedHours}:${minutes}${ampm}`;

        if (targetTimes.includes(currentTime)) {
            notificationManage(Products);
        }
    }, 60 * 1000); // every 1 minute
}

// Display notifications and play audio
async function notificationManage(products) {
    if (!audioAllowed) return; // user must click Start Alerts first

    const lowStocks = document.querySelector(".low-stocks");
    lowStocks.classList.remove("d-none");

    for (const p of products) {
        // Play audio if available
        const ttsAudio = document.querySelector(
            `.ttsAudio[data-product-id="${p.id}"]`
        );
        if (ttsAudio) {
            ttsAudio
                .play()
                .catch((err) => console.log("Playback failed:", err));
        } else {
            // Fallback: SpeechSynthesis
            speakText(p.stock, p.name, "en-US");
        }

        // Convert stock to Bangla number (optional)
        const stock = englishToBanglaNumber(p.stock);

        // Show notification
        lowStocks.innerHTML += `
            <div id="notification${p.id}" class="low-stock-notification">
                <button type="button" id="notification-close-btn-${p.id}" class="btn btn-sm btn-outline-danger rounded-circle">
                    <i class="fa-solid fa-xmark"></i>
                </button>
                <div>${p.name} ${stock}টি স্টক আছে, প্রোডাক্টটির স্টক বৃদ্ধি করতে হবে।</div>
            </div>
        `;

        const notification = document.querySelector(`#notification${p.id}`);
        const closeBtn = document.querySelector(
            `#notification-close-btn-${p.id}`
        );
        closeBtn.addEventListener("click", () => notification.remove());

        await sleep(10000); // show next notification after 10s
        notification.remove();
    }
}

// Sleep helper
function sleep(ms) {
    return new Promise((resolve) => setTimeout(resolve, ms));
}
