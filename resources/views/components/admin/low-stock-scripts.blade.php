@props([
    'settings' => null,
])
<script>
function formatTo12Hour(timeStr) {
    if (!timeStr) return "";
    const [hour, minute] = timeStr.split(":");
    let h = parseInt(hour, 10);
    const suffix = h >= 12 ? "PM" : "AM";
    h = h % 12 || 12; // convert 0 -> 12
    const hh = h.toString().padStart(2, "0"); // add leading zero
    return `${hh}:${minute}${suffix}`;
}

let Products = [];
const setProducts = (data) => (Products = data);
let audioAllowed = false;
let settings = @json($settings);
let alert_msg = settings.low_stock_alert_msg;
let targeted_times = [];
if (settings && settings.schedules.length > 0)targeted_times=settings.schedules.map(setting => formatTo12Hour(setting.time));



if (audioAllowed === false) {
    // Unlock audio on first user click
    document.querySelector("body").addEventListener("click", () => {
        audioAllowed = true;
        // alert("Audio playback enabled for notifications!");
    });
}

// Load products and schedule notifications
document.addEventListener("DOMContentLoaded", () => {
    getLowStockProducts(alert_msg);
});

// Fetch low-stock products from Laravel route
async function getLowStockProducts(alert_msg) {
    try {
        const res = await fetch(`/admin/low-stocks?text=${alert_msg}&lang=bn`);
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
    const targetTimes = targeted_times;
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
            sendEmailForLowStockProducts();
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
                <div>${p.name} ${stock}${alert_msg}</div>
            </div>
        `;
        //টি স্টক আছে, প্রোডাক্টটির স্টক বৃদ্ধি করতে হবে।
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

function sendEmailForLowStockProducts()
{
    fetch('/admin/send-email-for-low-stocks', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    })
    .then(res => res.json())
    .then(data => {
        console.log(data)
    })
    .catch(err => console.log(err))
}


</script>
