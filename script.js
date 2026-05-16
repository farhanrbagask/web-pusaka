/* ================================================
   NAVIGATION
   ================================================ */

window.addEventListener("scroll", function () {
  const header = document.querySelector("nav");
  header.classList.toggle("sticky", window.scrollY > 0);
});

// Fixed: removed duplicate toggleMenu; combined into one clean function
function toggleMenu() {
  const menuToggle = document.querySelector(".menuToggle");
  const navigation = document.querySelector(".navigation");
  menuToggle.classList.toggle("active");
  navigation.classList.toggle("active");
}

/* ================================================
   CONTACT FORM
   ================================================ */

const contactForm = document.getElementById("contact-form");
const loader = document.querySelector(".lds-ellipsis");

loader.style.display = "none";

contactForm.addEventListener("submit", function (e) {
  e.preventDefault();
  loader.style.display = "block";
  const url = e.target.action;
  const formData = new FormData(contactForm);

  fetch(url, {
    method: "POST",
    body: formData,
    mode: "no-cors",
  })
    .then(() => {
      loader.style.display = "none";
      window.location.href = "thanks.html";
    })
    .catch(() => alert("Terjadi kesalahan. Coba lagi."));
});

/* ================================================
   KALKULATOR HARGA
   ================================================ */

// --- State ---
var calcState = {
  service:   "print",
  size:      "a4",
  colorMode: "bw",
  pages:     1,
  copies:    1,
};

// --- Pricing (IDR per lembar) ---
const pricingTable = {
  print: {
    bw:    { a4: 500,  f4: 600,  a3: 1200 },
    color: { a4: 1500, f4: 2000, a3: 3500 },
  },
  fotocopy: {
    bw:    { a4: 300, f4: 400,  a3: 800  },
    color: { a4: 800, f4: 1000, a3: 1800 },
  },
  scan: {
    bw:    { a4: 500, f4: 600, a3: 1000 },
    color: { a4: 500, f4: 600, a3: 1000 },
  },
};

var finishingPricing = {
  jilid_spiral: { label: "Jilid Spiral",    price: 5000,  perCopy: true  },
  jilid_soft:   { label: "Jilid Softcover", price: 10000, perCopy: true  },
  laminating:   { label: "Laminating",      price: 2000,  perCopy: false },
};

var serviceLabels = { print: "Print", fotocopy: "Fotocopy", scan: "Scan" };
var colorLabels   = { bw: "Hitam Putih", color: "Berwarna" };
var sizeLabels    = { a4: "A4", f4: "F4", a3: "A3" };

// --- Select service card ---
function selectService(el, service) {
  document.querySelectorAll(".serviceCard").forEach(function (c) {
    c.classList.remove("active");
  });
  el.classList.add("active");
  calcState.service = service;

  // Disable color step for scan
  var colorStep = document.getElementById("colorStep");
  if (service === "scan") {
    colorStep.classList.add("disabled");
  } else {
    colorStep.classList.remove("disabled");
  }

  calculate();
}

// --- Generic toggle group (size & colorMode) ---
function selectToggle(el, stateKey, value) {
  el.closest(".toggleGroup").querySelectorAll(".toggleBtn").forEach(function (b) {
    b.classList.remove("active");
  });
  el.classList.add("active");
  calcState[stateKey] = value;
  calculate();
}

// --- +/− number buttons ---
function adjustValue(field, delta) {
  var input = document.getElementById(field);
  var val = parseInt(input.value) || 1;
  val = Math.max(1, val + delta);
  input.value = val;
  calcState[field] = val;
  calculate();
}

// --- Mutually exclusive jilid checkboxes ---
function onJilid(selected) {
  var other = selected === "jilid_spiral" ? "jilid_soft" : "jilid_spiral";
  if (document.getElementById(selected).checked) {
    document.getElementById(other).checked = false;
  }
  calculate();
}

// --- Format Rupiah ---
function formatRupiah(amount) {
  return "Rp " + amount.toLocaleString("id-ID");
}

// --- Main calculation ---
function calculate() {
  var pages  = parseInt(document.getElementById("pages").value)  || 1;
  var copies = parseInt(document.getElementById("copies").value) || 1;
  calcState.pages  = pages;
  calcState.copies = copies;

  var service   = calcState.service;
  var size      = calcState.size;
  var colorMode = calcState.colorMode;

  // Base price per lembar
  var basePrice;
  if (service === "scan") {
    basePrice = pricingTable.scan.bw[size];
  } else {
    basePrice = pricingTable[service][colorMode][size];
  }

  var printCost = basePrice * pages * copies;

  // Finishing
  var finishingCost = 0;
  var finishingRows = [];

  if (document.getElementById("jilid_spiral").checked) {
    var c1 = finishingPricing.jilid_spiral.price * copies;
    finishingCost += c1;
    finishingRows.push({ label: "Jilid Spiral \u00d7 " + copies + " rangkap", price: c1 });
  }
  if (document.getElementById("jilid_soft").checked) {
    var c2 = finishingPricing.jilid_soft.price * copies;
    finishingCost += c2;
    finishingRows.push({ label: "Jilid Softcover \u00d7 " + copies + " rangkap", price: c2 });
  }
  if (document.getElementById("laminating").checked) {
    var totalSheets = pages * copies;
    var c3 = finishingPricing.laminating.price * totalSheets;
    finishingCost += c3;
    finishingRows.push({ label: "Laminating \u00d7 " + totalSheets + " lembar", price: c3 });
  }

  var total = printCost + finishingCost;

  // Description string
  var serviceDesc = service === "scan"
    ? serviceLabels[service] + " (" + sizeLabels[size] + ")"
    : serviceLabels[service] + " " + colorLabels[colorMode] + " (" + sizeLabels[size] + ")";

  // Build breakdown HTML
  var html = "";

  html += '<div class="breakdownRow">'
        + '<span class="brow-label">' + serviceDesc
        + ' \u00d7 ' + pages + ' hal \u00d7 ' + copies + ' rangkap</span>'
        + '<span class="brow-price">' + formatRupiah(printCost) + '</span>'
        + '</div>';

  html += '<div class="breakdownRow">'
        + '<span class="brow-sub">@ ' + formatRupiah(basePrice) + ' / lembar</span>'
        + '<span></span>'
        + '</div>';

  finishingRows.forEach(function (row) {
    html += '<div class="breakdownRow">'
          + '<span class="brow-label">' + row.label + '</span>'
          + '<span class="brow-price">' + formatRupiah(row.price) + '</span>'
          + '</div>';
  });

  document.getElementById("resultBreakdown").innerHTML = html;

  // Animate total amount
  var totalEl = document.getElementById("totalAmount");
  totalEl.style.transform = "scale(1.12)";
  totalEl.textContent = formatRupiah(total);
  setTimeout(function () {
    totalEl.style.transform = "scale(1)";
  }, 160);
}

// Initialize calculator on page load
calculate();
