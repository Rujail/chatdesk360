@extends('layouts.app')

@section('title', 'Country Restrictions')


@section('content')
<div class="body-wrapper mb-0 pg-country-restrictions">
    <div class="container-fluid mw-100 pb-0">
        <x-breadcrumb title="Country Restrictions" />

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div>
                        <h4 class="fw-bold mb-1 d-flex align-items-center gap-2">
                            <i class="ti ti-world text-primary fs-4"></i> Country Restrictions
                        </h4>
                        <p class="text-muted mb-0 fs-3">Toggle countries to block visitors from seeing the chat widget.</p>
                    </div>
                    <div class="position-relative" style="min-width: 260px;">
                        <input type="text" class="form-control ps-5 border-light bg-light" id="country-search" placeholder="Search country..." style="border-radius: 10px;" />
                        <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y ms-4 text-muted fs-5"></i>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div id="country-list-container" class="country-grid">
                    <div class="empty-state">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                        Loading countries...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';
    const LIST_URL = '{{ route("country-restrictions.list") }}';
    const TOGGLE_URL = '{{ route("country-restrictions.toggle") }}';
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    const ALL_COUNTRIES = [
        {code: "AF", name: "Afghanistan"}, {code: "AL", name: "Albania"}, {code: "DZ", name: "Algeria"}, {code: "AD", name: "Andorra"}, {code: "AO", name: "Angola"}, {code: "AR", name: "Argentina"}, {code: "AM", name: "Armenia"}, {code: "AU", name: "Australia"}, {code: "AT", name: "Austria"}, {code: "AZ", name: "Azerbaijan"}, {code: "BH", name: "Bahrain"}, {code: "BD", name: "Bangladesh"}, {code: "BY", name: "Belarus"}, {code: "BE", name: "Belgium"}, {code: "BZ", name: "Belize"}, {code: "BJ", name: "Benin"}, {code: "BT", name: "Bhutan"}, {code: "BO", name: "Bolivia"}, {code: "BA", name: "Bosnia and Herzegovina"}, {code: "BW", name: "Botswana"}, {code: "BR", name: "Brazil"}, {code: "BN", name: "Brunei"}, {code: "BG", name: "Bulgaria"}, {code: "BF", name: "Burkina Faso"}, {code: "BI", name: "Burundi"}, {code: "KH", name: "Cambodia"}, {code: "CM", name: "Cameroon"}, {code: "CA", name: "Canada"}, {code: "CV", name: "Cape Verde"}, {code: "CF", name: "Central African Republic"}, {code: "TD", name: "Chad"}, {code: "CL", name: "Chile"}, {code: "CN", name: "China"}, {code: "CO", name: "Colombia"}, {code: "KM", name: "Comoros"}, {code: "CG", name: "Congo"}, {code: "CR", name: "Costa Rica"}, {code: "HR", name: "Croatia"}, {code: "CU", name: "Cuba"}, {code: "CY", name: "Cyprus"}, {code: "CZ", name: "Czech Republic"}, {code: "DK", name: "Denmark"}, {code: "DJ", name: "Djibouti"}, {code: "DO", name: "Dominican Republic"}, {code: "EC", name: "Ecuador"}, {code: "EG", name: "Egypt"}, {code: "SV", name: "El Salvador"}, {code: "GQ", name: "Equatorial Guinea"}, {code: "ER", name: "Eritrea"}, {code: "EE", name: "Estonia"}, {code: "ET", name: "Ethiopia"}, {code: "FJ", name: "Fiji"}, {code: "FI", name: "Finland"}, {code: "FR", name: "France"}, {code: "GA", name: "Gabon"}, {code: "GM", name: "Gambia"}, {code: "GE", name: "Georgia"}, {code: "DE", name: "Germany"}, {code: "GH", name: "Ghana"}, {code: "GR", name: "Greece"}, {code: "GT", name: "Guatemala"}, {code: "GN", name: "Guinea"}, {code: "GW", name: "Guinea-Bissau"}, {code: "GY", name: "Guyana"}, {code: "HT", name: "Haiti"}, {code: "HN", name: "Honduras"}, {code: "HK", name: "Hong Kong"}, {code: "HU", name: "Hungary"}, {code: "IS", name: "Iceland"}, {code: "IN", name: "India"}, {code: "ID", name: "Indonesia"}, {code: "IR", name: "Iran"}, {code: "IQ", name: "Iraq"}, {code: "IE", name: "Ireland"}, {code: "IL", name: "Israel"}, {code: "IT", name: "Italy"}, {code: "JM", name: "Jamaica"}, {code: "JP", name: "Japan"}, {code: "JO", name: "Jordan"}, {code: "KZ", name: "Kazakhstan"}, {code: "KE", name: "Kenya"}, {code: "KP", name: "North Korea"}, {code: "KR", name: "South Korea"}, {code: "KW", name: "Kuwait"}, {code: "KG", name: "Kyrgyzstan"}, {code: "LA", name: "Laos"}, {code: "LV", name: "Latvia"}, {code: "LB", name: "Lebanon"}, {code: "LS", name: "Lesotho"}, {code: "LR", name: "Liberia"}, {code: "LY", name: "Libya"}, {code: "LI", name: "Liechtenstein"}, {code: "LT", name: "Lithuania"}, {code: "LU", name: "Luxembourg"}, {code: "MO", name: "Macao"}, {code: "MK", name: "Macedonia"}, {code: "MG", name: "Madagascar"}, {code: "MW", name: "Malawi"}, {code: "MY", name: "Malaysia"}, {code: "MV", name: "Maldives"}, {code: "ML", name: "Mali"}, {code: "MT", name: "Malta"}, {code: "MR", name: "Mauritania"}, {code: "MU", name: "Mauritius"}, {code: "MX", name: "Mexico"}, {code: "MD", name: "Moldova"}, {code: "MC", name: "Monaco"}, {code: "MN", name: "Mongolia"}, {code: "ME", name: "Montenegro"}, {code: "MA", name: "Morocco"}, {code: "MZ", name: "Mozambique"}, {code: "MM", name: "Myanmar"}, {code: "NA", name: "Namibia"}, {code: "NP", name: "Nepal"}, {code: "NL", name: "Netherlands"}, {code: "NZ", name: "New Zealand"}, {code: "NI", name: "Nicaragua"}, {code: "NE", name: "Niger"}, {code: "NG", name: "Nigeria"}, {code: "NO", name: "Norway"}, {code: "OM", name: "Oman"}, {code: "PK", name: "Pakistan"}, {code: "PS", name: "Palestine"}, {code: "PA", name: "Panama"}, {code: "PG", name: "Papua New Guinea"}, {code: "PY", name: "Paraguay"}, {code: "PE", name: "Peru"}, {code: "PH", name: "Philippines"}, {code: "PL", name: "Poland"}, {code: "PT", name: "Portugal"}, {code: "QA", name: "Qatar"}, {code: "RO", name: "Romania"}, {code: "RU", name: "Russia"}, {code: "RW", name: "Rwanda"}, {code: "SA", name: "Saudi Arabia"}, {code: "SN", name: "Senegal"}, {code: "RS", name: "Serbia"}, {code: "SC", name: "Seychelles"}, {code: "SL", name: "Sierra Leone"}, {code: "SG", name: "Singapore"}, {code: "SK", name: "Slovakia"}, {code: "SI", name: "Slovenia"}, {code: "SO", name: "Somalia"}, {code: "ZA", name: "South Africa"}, {code: "SS", name: "South Sudan"}, {code: "ES", name: "Spain"}, {code: "LK", name: "Sri Lanka"}, {code: "SD", name: "Sudan"}, {code: "SR", name: "Suriname"}, {code: "SE", name: "Sweden"}, {code: "CH", name: "Switzerland"}, {code: "SY", name: "Syria"}, {code: "TW", name: "Taiwan"}, {code: "TJ", name: "Tajikistan"}, {code: "TZ", name: "Tanzania"}, {code: "TH", name: "Thailand"}, {code: "TL", name: "Timor-Leste"}, {code: "TG", name: "Togo"}, {code: "TT", name: "Trinidad and Tobago"}, {code: "TN", name: "Tunisia"}, {code: "TR", name: "Turkey"}, {code: "TM", name: "Turkmenistan"}, {code: "UG", name: "Uganda"}, {code: "UA", name: "Ukraine"}, {code: "AE", name: "United Arab Emirates"}, {code: "GB", name: "United Kingdom"}, {code: "US", name: "United States"}, {code: "UY", name: "Uruguay"}, {code: "UZ", name: "Uzbekistan"}, {code: "VE", name: "Venezuela"}, {code: "VN", name: "Vietnam"}, {code: "YE", name: "Yemen"}, {code: "ZM", name: "Zambia"}, {code: "ZW", name: "Zimbabwe"}
    ];

    let restrictedCodes = new Set();

    function loadRestricted() {
        fetch(LIST_URL)
            .then(r => r.json())
            .then(data => {
                restrictedCodes = new Set(data.restricted.map(c => c.country_code));
                renderCountries();
            });
    }

    function renderCountries() {
        const search = document.getElementById('country-search').value.toLowerCase();
        const container = document.getElementById('country-list-container');

        const filtered = ALL_COUNTRIES.filter(c => 
            c.name.toLowerCase().includes(search) || c.code.toLowerCase().includes(search)
        );

        if (!filtered.length) {
            container.innerHTML = `
                <div class="empty-state">
                    <i class="ti ti-search fs-1 d-block mb-2 opacity-50"></i>
                    <h6 class="fw-semibold">No countries found</h6>
                    <p class="fs-3">Try a different search term.</p>
                </div>
            `;
            return;
        }

        container.innerHTML = filtered.map(c => `
            <div class="country-card">
                <div class="country-info">
                    <img src="https://flagcdn.com/40x30/${c.code.toLowerCase()}.png" alt="${c.code}" class="country-flag">
                    <div>
                        <div class="country-name">${c.name}</div>
                        <div class="country-meta">${c.code} &middot; ${restrictedCodes.has(c.code) ? 'Blocked' : 'Allowed'}</div>
                    </div>
                </div>
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" role="switch" 
                        data-code="${c.code}" data-name="${c.name}" 
                        ${restrictedCodes.has(c.code) ? 'checked' : ''}>
                </div>
            </div>
        `).join('');

        container.querySelectorAll('input[type="checkbox"]').forEach(input => {
            input.addEventListener('change', function() {
                toggleCountry(this.dataset.code, this.dataset.name, this.checked, this);
            });
        });
    }

    function toggleCountry(code, name, isRestricted, checkboxEl) {
        // Update UI immediately for responsiveness
        const metaEl = checkboxEl.closest('.country-card').querySelector('.country-meta');
        
        fetch(TOGGLE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                country_code: code,
                country_name: name,
                is_restricted: isRestricted
            })
        }).then(r => r.json()).then(data => {
            if (isRestricted) {
                restrictedCodes.add(code);
                if (metaEl) metaEl.innerHTML = `${code} &middot; Blocked`;
            } else {
                restrictedCodes.delete(code);
                if (metaEl) metaEl.innerHTML = `${code} &middot; Allowed`;
            }
        }).catch(() => {
            // Revert on failure
            checkboxEl.checked = !isRestricted;
        });
    }

    document.getElementById('country-search').addEventListener('input', renderCountries);
    loadRestricted();

})();
</script>
@endpush