let loader = false;

function loaderAuthPages() {
    if (loader === false) {
        hideLoader();
        loader = true;
    }
}
loaderAuthPages();

function toNormalText(strText) {
    // replace "_" with " "
    let text = "";
    let normalText = strText.replace(/_/g, " ");

    // convert to Title Case
    normalText = normalText
        .toLowerCase()
        .replace(/\b\w/g, (char) => char.toUpperCase());
    text = normalText ? normalText : "";
    return text;
}

// Example usage

function processNotify(msg, status = "success", position = "top-right") {
    toastr.options = {
        closeButton: true,
        debug: false,
        newestOnTop: true,
        progressBar: true,
        positionClass: `toast-${position}`, // now always valid
        preventDuplicates: false,
        showDuration: "300",
        hideDuration: "1000",
        timeOut: "5000",
        extendedTimeOut: "1000",
        showEasing: "swing",
        hideEasing: "linear",
        showMethod: "fadeIn",
        hideMethod: "fadeOut",
    };

    // only valid toastr methods
    if (["success", "error", "warning", "info"].includes(status)) {
        toastr[status](msg);
    } else {
        toastr.info(msg); // fallback
    }
}

function date_format(date_and_time) {
    const formatted = new Intl.DateTimeFormat("en-GB", {
        day: "2-digit",
        month: "short",
        year: "numeric",
    })
        .format(new Date(date_and_time))
        .replace("Sept", "Sep");

    // split into parts
    const [day, month, year] = formatted.split(" ");
    return `${day} ${month}, ${year}`;
}
function JSONParse(data) {
    let dataReplace = data.replace(/&quot;/g, '"');
    let customer = JSON.parse(dataReplace);
    return customer;
}
function formatted_date(date) {
    return date ? date.split(" ")[0] : "";
}
function formatted_date_for_edit(date) {
    if (!date) return "";

    // handle ISO string like "2025-09-20T00:00:00.000000Z"
    if (date.includes("T")) {
        return date.split("T")[0]; // "2025-09-20"
    }

    // handle mm/dd/yyyy format
    if (date.includes("/")) {
        const [month, day, year] = date.split("/");
        return `${year}-${month.padStart(2, "0")}-${day.padStart(2, "0")}`;
    }
    return "";
}
function formatted_date_flatpickr(date) {
    if (!date) return "";

    let d;

    // handle ISO string like "2025-09-20T00:00:00.000000Z"
    if (date.includes("T")) {
        d = new Date(date);
    }
    // handle mm/dd/yyyy format
    else if (date.includes("/")) {
        const [month, day, year] = date.split("/");
        d = new Date(`${year}-${month}-${day}`);
    } else {
        d = new Date(date);
    }

    const day = String(d.getDate()).padStart(2, "0");
    const month = String(d.getMonth() + 1).padStart(2, "0");
    const year = d.getFullYear();

    return `${day}/${month}/${year}`; // dd/mm/yyyy
}
function formatDateToReadable(dateStr) {
    if (!dateStr) return "";

    const date = new Date(dateStr);
    const day = date.getDate();
    const monthNames = [
        "Jan",
        "Feb",
        "Mar",
        "Apr",
        "May",
        "Jun",
        "Jul",
        "Aug",
        "Sep",
        "Oct",
        "Nov",
        "Dec",
    ];
    const month = monthNames[date.getMonth()];
    const year = date.getFullYear();

    return `${day} ${month}, ${year}`;
}

function englishToBanglaNumber(num) {
    const banglaDigits = ["০", "১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯"];
    return num.toString().replace(/[0-9]/g, (d) => banglaDigits[d]);
}
