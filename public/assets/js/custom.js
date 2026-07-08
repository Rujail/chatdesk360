document.addEventListener('DOMContentLoaded', function () {
    'use strict';
    // =================================
    // Tooltip
    // =================================
    // Initialize all tooltips except .custom-tooltip
    const tooltipTriggerList = Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]:not(.custom-tooltip)'));
    tooltipTriggerList.forEach((tooltipTriggerEl) => {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // =================================
    // Popover
    // =================================
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    // =================================
    // Hide preloader
    // =================================
    var preloader = document.querySelector('.preloader');
    if (preloader) {
        preloader.style.display = 'none';
    }
    // =================================
    // Increment & Decrement
    // =================================
    var quantityButtons = document.querySelectorAll('.minus, .add');
    if (quantityButtons) {
        quantityButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var qtyInput = this.closest('div').querySelector('.qty');
                var currentVal = parseInt(qtyInput.value);
                var isAdd = this.classList.contains('add');

                if (!isNaN(currentVal)) {
                    qtyInput.value = isAdd ? ++currentVal : currentVal > 0 ? --currentVal : currentVal;
                }
            });
        });
    }
});


$(document).ready(function () {
    $('.data-grid-row').on('click', function () {
        // Example: dynamically fill sidebar data from grid
        var name = $(this).find('.grid-item').eq(0).text().trim() || 'Unnamed';
        var email = $(this).find('.grid-item').eq(1).text().trim() || '-';
        var country = $(this).find('.grid-item').eq(6).text().trim() || 'Unknown';
        var city = $(this).find('.grid-item').eq(8).text().trim() || '';
        var lastSeen = $(this).find('.grid-item').eq(11).text().trim() || 'N/A';

        // Update sidebar details
        $('#customer-name').text(name);
        $('#customer-location').text(city + ', ' + country);
        $('#customer-time').text('Last seen: ' + lastSeen);

        // Open sidebar
        $('.traffic-list-offcanvas ').toggleClass('active');
        $('.traffic-det .cus-info .chat-menu i').toggleClass('ti-x');
    });

    $(document).on('click', '.tab-customer', function () {
        $('button#pills-all-cus-tab').trigger('click');
    });
});



$(document).ready(function () {
    $('.agent-row').each(function () {
        var fullName = $(this).find('.agent-name').text().trim();
        var names = fullName.split(' ');

        var initials = '';
        if (names.length >= 2) {
            initials = names[0].charAt(0) + names[1].charAt(0);
        } else if (names.length === 1) {
            initials = names[0].charAt(0);
        }

        // Optional: generate a color based on name
        var colors = ['#0d6efd', '#198754', '#dc3545', '#fd7e14', '#6f42c1', '#0dcaf0'];
        var charCodeSum = fullName.charCodeAt(0) + (fullName.charCodeAt(1) || 0);
        var color = colors[charCodeSum % colors.length];

        // Insert initials and color
        $(this).find('.avatar-initials').text(initials).css('background-color', color);
    });
});

$(document).ready(function () {
    $('.accordion-button .agt-avatar').each(function () {
        var $agentName = $(this).find('.agent-name').text().trim();
        var names = $agentName.split(' ');

        var initials = '';
        if (names.length >= 2) {
            initials = names[0].charAt(0) + names[1].charAt(0);
        } else if (names.length === 1) {
            initials = names[0].charAt(0);
        }

        // Optional: deterministic color based on name
        var colors = ['#0d6efd', '#198754', '#dc3545', '#fd7e14', '#6f42c1', '#0dcaf0'];
        var charCodeSum = $agentName.charCodeAt(0) + ($agentName.charCodeAt(1) || 0);
        var color = colors[charCodeSum % colors.length];

        $(this).find('.avatar-initials').text(initials).css('background-color', color);
    });
});

$(document).ready(function () {
    $('.custom-selectoption').each(function () {
        var dropdown = $('<div />').addClass('selectDropdown');
        $(this).wrap(dropdown);
        var label = $('<span />').text($(this).attr('placeholder')).insertAfter($(this));
        var list = $('<ul />');
        $(this)
            .find('option')
            .each(function () {
                list.append($('<li />').append($('<a />').text($(this).text())));
            });
        list.insertAfter($(this));
        // Set default selected
        if ($(this).find('option:selected').length) {
            var selectedText = $(this).find('option:selected').text();
            label.text(selectedText);
            list.find('li:contains(' + selectedText + ')').addClass('active');
            $(this).parent().addClass('filled');
        }
    });

    $(document).on('click', '.selectDropdown ul li a', function (e) {
        e.preventDefault();
        var dropdown = $(this).closest('.selectDropdown');
        var $li = $(this).parent();
        var active = $li.hasClass('active');
        var realSelect = dropdown.find('select');
        var label = dropdown.children('span');
        var clickedText = $(this).text().trim();

        // Already selected — this is a single-select dropdown, so clicking the
        // same option again should just close the menu, not deselect it.
        if (active) {
            dropdown.removeClass('open');
            return;
        }

        dropdown.find('li').removeClass('active');
        realSelect.find('option').prop('selected', false);

        label.text(clickedText);
        dropdown.addClass('filled');
        $li.addClass('active');
        realSelect.find('option').each(function () {
            this.selected = $(this).text().trim() === clickedText;
        });
        // ---- FIRE REAL NATIVE CHANGE EVENT ----
        realSelect[0].dispatchEvent(new Event('change', { bubbles: true }));

        dropdown.removeClass('open');
    });

    /************* Open dropdown *************/
    $(document).on('click', '.selectDropdown > span', function (e) {
        e.stopPropagation(); // prevent document click
        var current = $(this).parent();
        // CLOSE all other dropdowns
        $('.selectDropdown').not(current).removeClass('open');
        // toggle the clicked one
        current.toggleClass('open');
    });

    /************* Close when clicking outside *************/
    $(document).on('click', function (e) {
        if (!$(e.target).closest('.selectDropdown').length) {
            $('.selectDropdown').removeClass('open');
        }
    });

    $(document).on('click', '.invite-agent', function () {
        $('#invite-agentmodal').modal('show');
    });
});

$(document).ready(function () {
    var colors = ['#0d6efd', '#198754', '#dc3545', '#fd7e14', '#6f42c1', '#0dcaf0'];

    function applyStateClass(hasImage) {
        var wrapper = $('.profile-avatar-details');

        if (hasImage) {
            wrapper.removeClass('no-image').addClass('has-image');
        } else {
            wrapper.removeClass('has-image').addClass('no-image');
        }
    }

    // Get first letter & show initials with deterministic color
    function setInitials() {
        var name = $('.profile-name h3').text().trim();
        var firstLetter = name.charAt(0).toUpperCase();
        var secondLetter = name.charAt(1) ? name.charAt(1).toUpperCase() : '';

        var charCodeSum = name.charCodeAt(0) + (name.charCodeAt(1) || 0);
        var color = colors[charCodeSum % colors.length];

        var $initials = $('.profile-initials');
        $initials.text(firstLetter + secondLetter).show();
        $initials.css('background-color', color);

        $('.profile-pic').hide().attr('src', '');

        applyStateClass(false); // no image
    }

    // Initial load: if no image present
    if (!$('.profile-pic').attr('src')) {
        setInitials();
    }

    // On file upload
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('.profile-pic').attr('src', e.target.result).show();
                $('.profile-initials').hide();

                applyStateClass(true); // has image
            };

            reader.readAsDataURL(input.files[0]);
        } else {
            setInitials(); // fallback when file removed
        }
    }

    $('.file-upload').on('change', function () {
        readURL(this);
    });

    // Prevent infinite loop
    $('.file-upload').on('click', function (e) {
        e.stopPropagation();
    });

    $('.upload-button').on('click', function (e) {
        e.stopPropagation();
        $(this).find('.file-upload').trigger('click');
    });

    $('.qty-plus').each(function (index) {
        $(this).click(function (e) {
            e.preventDefault();
            var current = $(this).siblings('input.quantity').val();
            var currentVal = parseInt($(this).siblings('input.quantity').val());
            if (!isNaN(currentVal)) {
                $(this)
                    .siblings('input.quantity')
                    .val(currentVal + 1);
            } else {
                $(this).siblings('input.quantity').val(1);
                console.log('Failed!');
            }
        });
    });

    // Decrement value in quantity input

    $('.qty-minus').each(function (index) {
        $(this).click(function (e) {
            e.preventDefault();
            var currentVal = parseInt($(this).siblings('input.quantity').val());
            if (!isNaN(currentVal) && currentVal > 1) {
                $(this)
                    .siblings('input.quantity')
                    .val(currentVal - 1);
            } else {
                $(this).siblings('input.quantity').val(1);
            }
        });
    });

    $(document).on('click', '.banModalopen', function () {
        $('#banCustomerModal').modal('show');
    });

     $(function () {
            var table = $('.datatable-config').DataTable({
                responsive: true
            });
            // Custom search input
            $('.datatable-search').on('keyup', function () {
                table.search(this.value).draw();
            });
        });
});


