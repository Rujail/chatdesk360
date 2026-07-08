$(function () {
    var chatarea = $('#chat');

    $('#chat .message-center a').on('click', function () {
        var name = $(this).find('.mail-contnet h5').text();
        var img = $(this).find('.user-img img').attr('src');
        var id = $(this).attr('data-user-id');
        var status = $(this).find('.profile-status').attr('data-status');

        if ($(this).hasClass('active')) {
            $(this).toggleClass('active');
            $('.chat-windows #user-chat' + id).hide();
        } else {
            $(this).toggleClass('active');
            if ($('.chat-windows #user-chat' + id).length) {
                $('.chat-windows #user-chat' + id)
                    .removeClass('mini-chat')
                    .show();
            } else {
                var msg = msg_receive('I watched the storm, so beautiful yet terrific.');
                msg += msg_sent('That is very deep indeed!');
                var html = "<div class='user-chat' id='user-chat" + id + "' data-user-id='" + id + "'>";
                html +=
                    "<div class='chat-head'><img src='" +
                    img +
                    "' data-user-id='" +
                    id +
                    "'><span class='status " +
                    status +
                    "'></span><span class='name'>" +
                    name +
                    "</span><span class='opts'><i class='material-icons closeit' data-user-id='" +
                    id +
                    "'>clear</i><i class='material-icons mini-chat' data-user-id='" +
                    id +
                    "'>remove</i></span></div>";
                html += "<div class='chat-body'><ul class='chat-list'>" + msg + '</ul></div>';
                html +=
                    "<div class='chat-footer'><input type='text' data-user-id='" +
                    id +
                    "' placeholder='Type & Enter' class='form-control'></div>";
                html += '</div>';
                $('.chat-windows').append(html);
            }
        }
    });
});

// Global variable to hold file data for sending
let attachedFile = null;

// *******************************************************************
// Chat Application
// *******************************************************************

$('.search-chat').on('keyup', function () {
    var value = $(this).val().toLowerCase();
    $('.chat-users li').filter(function () {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
});

$('.app-chat .chat-user ').on('click', function (event) {
    if ($(this).hasClass('.active')) {
        return false;
    } else {
        var findChat = $(this).attr('data-user-id');
        var personName = $(this).find('.chat-title').text();
        var personImage = $(this).find('img').attr('src');
        var hideTheNonSelectedContent = $(this)
            .parents('.chat-application')
            .find('.chat-not-selected')
            .hide()
            .siblings('.chatting-box')
            .show();
        var showChatInnerContent = $(this)
            .parents('.chat-application')
            .find('.chat-container .chat-box-inner-part')
            .show();

        if (window.innerWidth <= 767) {
            $('.chat-container .current-chat-user-name .name').html(personName.split(' ')[0]);
        } else if (window.innerWidth > 767) {
            $('.chat-container .current-chat-user-name .name').html(personName);
        }
        $('.chat-container .current-chat-user-name img').attr('src', personImage);
        $('.chat').removeClass('active-chat');
        $('.user-chat-box .chat-user').removeClass('bg-light-subtle');
        $(this).addClass('bg-light-subtle');
        $('.chat[data-user-id = ' + findChat + ']').addClass('active-chat');

        // Hide any active preview/emoji picker when switching chats
        $('.file-preview-container').empty().addClass('d-none');
        $('.emoji-picker-container').addClass('d-none');
        attachedFile = null;
    }
    if ($(this).parents('.user-chat-box').hasClass('user-list-box-show')) {
        $(this).parents('.user-chat-box').removeClass('user-list-box-show');
    }
    $('.chat-meta-user').addClass('chat-active');
    $('.chat-send-message-footer').addClass('chat-active');
});





// Remove file attachment logic
$(document).on('click', '.remove-file-btn', function () {
    attachedFile = null;
    $('#fileAttachmentInput').val(''); // Clear file input
    $('.file-preview-container').empty().addClass('d-none');
});


// Handler for the "Generate & Send Link" button in the modal
$('#generatePaymentLink').on('click', function () {
    var amount = $('#amount').val();
    var description = $('#description').val() || 'Payment Request';
    var currency = $('#currency').val();

    if (!amount) {
        alert('Please enter an amount.');
        return;
    }

    // Close the modal
    $('#paymentLinkModal').modal('hide');

    // Simulate message generation and append to active chat
    var now = new Date();
    var hh = now.getHours();
    var min = now.getMinutes();
    var ampm = hh >= 12 ? 'pm' : 'am';
    hh = hh % 12;
    hh = hh ? hh : 12;
    hh = hh < 10 ? '0' + hh : hh;
    min = min < 10 ? '0' + min : min;
    var time = hh + ':' + min + ' ' + ampm;

    var $messageHtml =
        '<div class="hstack gap-3 align-items-start mb-7 justify-content-end">' +
        '  <div class="text-end">' +
        '    <h6 class="fs-2 text-muted">' +
        time +
        '</h6>' +
        '    <div class="p-3 bg-primary text-white d-inline-block rounded-1 payment-link-message">' +
        '      <div class="d-flex flex-column align-items-start">' +
        '        <div class="d-flex align-items-center mb-1">' +
        '          <i class="ti ti-wallet fs-4 me-2"></i>' +
        '          <span class="fw-semibold">Payment Link Sent</span>' +
        '        </div>' +
        '        <p class="mb-1 fs-3">Amount: ' +
        currency +
        ' ' +
        amount +
        ' (' +
        description +
        ')</p>' +
        '        <a href="#" class="text-white text-decoration-underline fs-2" target="_blank">View Link</a>' +
        '      </div>' +
        '    </div>' +
        '  </div>' +
        '</div>';

    $('.chat-application').find('.active-chat').append($messageHtml);

    // Optional: clear the modal form
    $('#payment-link-form')[0].reset();
});

// *******************************************************************
// Email Application
// *******************************************************************

$(document).ready(function () {
    $('.back-btn').click(function () {
        $('.app-email-chatting-box').hide();
    });
    $('.chat-user').click(function () {
        $('.app-email-chatting-box').show();
    });
});

// *******************************************************************
// chat Offcanvas
// *******************************************************************

$('body').on('click', '.chat-menu', function () {
    $('.parent-chat-box').toggleClass('app-chat-right');
    $(this).toggleClass('app-chat-active');
    $(this).parents('li').find('i').toggleClass('ti-x');
});




