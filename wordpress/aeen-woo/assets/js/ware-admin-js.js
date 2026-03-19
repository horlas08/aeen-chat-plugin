 
    // JavaScript to handle tab switching and form submission
           document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.nav-tab a');
    const panels = document.querySelectorAll('.wp-tab-panels');
    const form = document.querySelector('form');

    // Function to show the active tab
    function showActiveTab(tab) {
        tabs.forEach(function(tabLink) {
            tabLink.parentNode.classList.remove('nav-tab-active');
        });
        panels.forEach(function(panel) {
            panel.style.display = 'none';
        });
        document.querySelector(`.nav-tab a[href="${tab}"]`).parentNode.classList.add('nav-tab-active');
        document.querySelector(tab).style.display = 'block';
    }

    // Check if an active tab is stored in local storage and show it
    const storedTab = localStorage.getItem('activeTab');
    if (storedTab) {
        showActiveTab(storedTab);
    } else {
        showActiveTab('#notification');
    }

    // Add click event to tabs
    tabs.forEach(function(tab) {
        tab.addEventListener('click', function(event) {
            event.preventDefault();
            const href = this.getAttribute('href');
            window.location.hash = href;
            showActiveTab(href);
            localStorage.setItem('activeTab', href);
        });
    });

    // Preserve active tab after form submission
    form.addEventListener('submit', function() {
        const activeTab = document.querySelector('.nav-tab-active a').getAttribute('href');
        localStorage.setItem('activeTab', activeTab);
    });
});


jQuery(function ($) {
    $("textarea.ware-emoji").emojioneArea({
        pickerPosition: "bottom",
        tones: true,
        search: true
    });
    

    
    

const placeholders = {
    '{{id}}': 'Order ID',
    '{{order_key}}': 'Order Key',
    '{{order_date}}': 'Order Date',
    '{{order_link}}': 'Order Summary Link',
    '{{product}}': 'Product List',
    '{{product_name}}': 'Product Name',
    '{{order_discount}}': 'Order Discount',
    '{{cart_discount}}': 'Cart Discount',
    '{{order_tax}}': 'Tax',
    '{{currency}}': 'Currency Symbol',
    '{{order_subtotal}}': 'Subtotal Amount',
    '{{order_total}}': 'Total Amount',
    '{{billing_first_name}}': 'First Name',
    '{{billing_last_name}}': 'Last Name',
    '{{billing_company}}': 'Company',
    '{{billing_address_1}}': 'Address 1',
    '{{billing_address_2}}': 'Address 2',
    '{{billing_city}}': 'City',
    '{{billing_postcode}}': 'Postcode',
    '{{billing_country}}': 'Country',
    '{{billing_state}}': 'Province',
    '{{billing_email}}': 'Email',
    '{{billing_phone}}': 'Phone',
    '{{shop_name}}': 'Shop Name',
    '{{site_link}}': 'Site Link',
    '{{transaction_id}}': 'Transaction ID',
    '{{note}}': 'Order Note'
};

const loginplaceholders = {
    
    '{{user_name}}': 'Username',
    '{{user_first_last_name}}': 'First & last name',
    '{{wc_billing_first_name}}': 'Woo First Name',
    '{{wc_billing_last_name}}': 'Woo Last Name',
    '{{wc_billing_phone}}': 'Phone Number',
    '{{current_date_time}}': 'Time & Date',
    '{{shop_name}}': 'Shop Name',
};

const loginplaceholdersar = {
    
    '{{user_name}}': 'اسم المستخدم',
    '{{user_first_last_name}}': 'الاسم الاول والاخير',
    '{{wc_billing_first_name}}': 'الاسم الاول',
    '{{wc_billing_last_name}}': 'الاسم الاخير',
    '{{wc_billing_phone}}': 'رقم الهاتف',
    '{{current_date_time}}': 'التاريخ والوقت',
    '{{shop_name}}': 'اسم الموقع',
};


const placeholdersabcart = {
    
    '{{product}}': 'Product Name',
    '{{order_total}}': 'Total Amount',
    '{{billing_phone}}': 'Phone',
    '{{shop_name}}': 'Shop Name',
    '{{site_link}}': 'Site Link'
};


const placeholdersAr = {
    '{{id}}': 'معرف الطلب',
    '{{order_key}}': 'مفتاح الطلب',
    '{{order_date}}': 'تاريخ الطلب',
    '{{order_link}}': 'رابط ملخص الطلب',
    '{{product}}': 'قائمة المنتجات',
    '{{product_name}}': 'اسم المنتج',
    '{{order_discount}}': 'خصم الطلب',
    '{{cart_discount}}': 'خصم السلة',
    '{{order_tax}}': 'الضريبة',
    '{{currency}}': 'رمز العملة',
    '{{order_subtotal}}': 'المجموع الفرعي',
    '{{order_total}}': 'المجموع الكلي',
    '{{billing_first_name}}': 'الاسم الأول',
    '{{billing_last_name}}': 'الاسم الأخير',
    '{{billing_company}}': 'الشركة',
    '{{billing_address_1}}': 'العنوان 1',
    '{{billing_address_2}}': 'العنوان 2',
    '{{billing_city}}': 'المدينة',
    '{{billing_postcode}}': 'الرمز البريدي',
    '{{billing_country}}': 'الدولة',
    '{{billing_state}}': 'المقاطعة',
    '{{billing_email}}': 'البريد الإلكتروني',
    '{{billing_phone}}': 'الهاتف',
    '{{shop_name}}': 'اسم المتجر',
    '{{site_link}}': 'رابط الموقع',
    '{{transaction_id}}': 'الرمز الفريد للدفع',
    '{{note}}': 'ملاحظة الطلب'
};

const messageTemplates = {
'📣Heads Up: Your account, {{user_name}}, was accessed on {{current_date_time}}. 👤 User Information: Name: {{user_first_last_name}}, Phone Number: {{wc_billing_phone}} ⚠️Please confirm this activity. If it wasn’t you, take steps to secure your account right away. ❤️ We Care About Your Safety,  {{shop_name}}': 'Account Access Alert',
    'Welcome, {{billing_first_name}} 👋. We hope you enjoyed your purchasing experience from {{shop_name}}🤩. If you have any questions, do not hesitate to contact us 🌹.': 'Order Created',
    'Hi {{billing_first_name}}, your order {{id}} is currently on hold. We will notify you as soon as the status changes. Feel free to reach out if you have any questions!': 'Order on hold',
    'Hello {{billing_first_name}}, your order {{id}} is now being processed. We will update you once it\'s completed. Thanks for your patience!': 'Order processing',
    'Great news, {{billing_first_name}}! Your order {{id}} has been completed. Thank you for shopping with {{shop_name}}. We hope to see you again soon!': 'Order completed',
    'Hi {{billing_first_name}}, we\'re still waiting for the payment of your order {{id}}. Please complete the payment to proceed with your order. Let us know if you need any help!': 'Order pending payment',
    'Hello {{billing_first_name}}, unfortunately, your order {{id}} has failed. Please try again or contact us for assistance. We\'re here to help!': 'Order failed',
    'Hi {{billing_first_name}}, your order {{id}} has been refunded. If you have any questions, please don\'t hesitate to reach out to us.': 'Order refunded',
    'Hello {{billing_first_name}}, we regret to inform you that your order {{id}} has been cancelled. For more information, please contact us. We apologize for any inconvenience.': 'Order cancelled',
    'Hi {{billing_first_name}}, you have a new note regarding your order {{id}}: {{note}}. If you have any questions, feel free to ask!': 'Order notes',
    'external_link': 'Text Formatting...' // Special option for external link
};


const messageTemplatesAr = {
    'مرحبًا، {{billing_first_name}} 👋. نأمل أن تكون قد استمتعت بتجربة الشراء من {{shop_name}}🤩. إذا كانت لديك أي أسئلة، فلا تتردد في الاتصال بنا 🌹.': 'إنشاء الطلب',
    'مرحبًا {{billing_first_name}}، طلبك {{id}} معلق حاليًا. سنقوم بإبلاغك فور تغيير الحالة. لا تتردد في التواصل معنا إذا كان لديك أي استفسار!': 'الطلب معلق',
    'مرحبًا {{billing_first_name}}، طلبك {{id}} قيد المعالجة الآن. سنقوم بتحديثك بمجرد اكتماله. شكرًا لصبرك!': 'الطلب قيد المعالجة',
    'أخبار رائعة، {{billing_first_name}}! تم إكمال طلبك {{id}}. شكرًا لتسوقك مع {{shop_name}}. نأمل أن نراك مجددًا قريبًا!': 'اكتمل الطلب',
    'مرحبًا {{billing_first_name}}، ما زلنا ننتظر دفع طلبك {{id}}. يرجى إكمال الدفع لمتابعة طلبك. أخبرنا إذا كنت بحاجة إلى أي مساعدة!': 'الطلب في انتظار الدفع',
    'مرحبًا {{billing_first_name}}، للأسف، فشل طلبك {{id}}. يرجى المحاولة مرة أخرى أو الاتصال بنا للحصول على المساعدة. نحن هنا للمساعدة!': 'فشل الطلب',
    'مرحبًا {{billing_first_name}}، تم استرداد طلبك {{id}}. إذا كانت لديك أي أسئلة، فلا تتردد في التواصل معنا.': 'تم استرداد الطلب',
    'مرحبًا {{billing_first_name}}، نأسف لإبلاغك بأن طلبك {{id}} قد تم إلغاؤه. لمزيد من المعلومات، يرجى الاتصال بنا. نعتذر عن أي إزعاج.': 'تم إلغاء الطلب',
    'مرحبًا {{billing_first_name}}، لديك ملاحظة جديدة بخصوص طلبك {{id}}: {{note}}. إذا كانت لديك أي أسئلة، لا تتردد في السؤال!': 'ملاحظات الطلب',
    'external_link': 'لتنسيق النص...' // خيار خاص للرابط الخارجي
    };


function createPlaceholderDropdown(placeholders, promptText) {
    var dropdown = `<select class="placeholder-dropdown">
                        <option value="" disabled selected>${promptText}</option>`;
    
    for (const [placeholder, description] of Object.entries(placeholders)) {
        dropdown += `<option value="${placeholder}">${description}</option>`;
    }
    
    dropdown += `</select>`;
    
    return dropdown;
}

function initializePlaceholderDropdown(containerClass, placeholders, promptText) {
    $(containerClass).each(function () {
        $(this).html(createPlaceholderDropdown(placeholders, promptText));
    });

    $('.placeholder-dropdown').change(function () {
        var placeholder = $(this).val();
        if (placeholder === 'external_link') {
            window.open('https://arrivewhats.com/whatsapp-text-formatter/', '_blank'); // External link URL
            $(this).prop('selectedIndex', 0); // Reset dropdown
            return;
        }

        var textarea = $(this).closest('.notification').find('textarea.ware-emoji');
        if (placeholder && textarea.length) {
            var emojiArea = textarea[0].emojioneArea;
            var currentText = emojiArea.getText();
            emojiArea.setText(currentText + ' ' + placeholder);
            $(this).prop('selectedIndex', 0); // Reset dropdown
        }
    });
}

$(document).ready(function() {
    initializePlaceholderDropdown('.placeholder-container', placeholders, 'Placeholders');
    initializePlaceholderDropdown('.placeholder-containerlogin', loginplaceholders, 'Placeholders');
     initializePlaceholderDropdown('.placeholder-containerloginar', loginplaceholdersar, 'اختر المعرف');
    initializePlaceholderDropdown('.placeholder-containerab', placeholdersabcart, 'Placeholders');
    initializePlaceholderDropdown('.placeholder-messageTemplatesar', messageTemplatesAr, 'القوالب الجاهزة للإستخدام');
     initializePlaceholderDropdown('.placeholder-container-ar', placeholdersAr, 'اختر المعرف');
     
    initializePlaceholderDropdown('.message-template-container', messageTemplates, 'Select Template');
});

    function initializeEmojiPicker() {
        $("textarea.ware-emoji").emojioneArea({
            pickerPosition: "bottom",
            tones: false,
            search: false
        });
    }

    initializeEmojiPicker();
    initializePlaceholderDropdown();

    if ($("#ware_test_number").length) {
    var iti_ware = window.intlTelInput(document.querySelector("#ware_test_number"), {
        initialCountry: "auto",
        geoIpLookup: function (success, failure) {
            $.ajax({
                url: "https://ipapi.co/country/",
                type: "GET",
                dataType: "text",
                success: function (countryCode) {
                    success(countryCode);
                },
                error: function () {
                    failure();
                }
            });
        },
        utilsScript: "<?php echo esc_url(plugin_dir_url(__FILE__) . 'assets/js/utils.js'); ?>"
    });

    $('#ware_test_number').on('blur', function () {
        $(this).val(iti_ware.getNumber().replace('+', ''));
    });

    window.iti_ware = iti_ware;
}


if ($("#admin_number").length) {
    var iti_admin = window.intlTelInput(document.querySelector("#admin_number"), {
        initialCountry: "auto",
        geoIpLookup: function (success, failure) {
            $.ajax({
                url: "https://ipapi.co/country/",
                type: "GET",
                dataType: "text",
                success: function (countryCode) {
                    success(countryCode);
                },
                error: function () {
                    failure();
                }
            });
        },
        utilsScript: "<?php echo esc_url(plugin_dir_url(__FILE__) . 'assets/js/utils.js'); ?>"
    });

    $('#admin_number').on('blur', function () {
        $(this).val(iti_admin.getNumber().replace('+', ''));
    });

    window.iti_admin = iti_admin;
}

    
    $('.nav-tab-wrapper a').click(function (event) {
        event.preventDefault();
        var context = $(this).closest('.nav-tab-wrapper').parent();
        $('.nav-tab-wrapper li', context).removeClass('nav-tab-active');
        $(this).closest('li').addClass('nav-tab-active');
        $('.wp-tab-panels', context).hide();
        $($(this).attr('href'), context).show();
    });

    $('.ware-tab-wrapper .nav-tab-wrapper').each(function () {
        if ($('.nav-tab-active', this).length)
            $('.nav-tab-active', this).click();
        else
            $('a', this).first().click();
    });

    $('.ware-panel-footer input[type=submit]').click(function (event) {
        $(this).parent().append('<img src="images/spinner-2x.gif">');
    });

    $('#ware-sortable-items')
        .accordion({
            header: "> li > header",
            active: false,
            collapsible: true,
            heightStyle: "content",
            activate: function (event, ui) {
                ware_action($(this));
            }
        })
        .sortable({
            axis: "y",
            update: function (event, ui) {
                ware_action($(this));
            }
        });

    $('.ware-add-item').click(function () {
        var new_li = Date.now() / 1000 | 0;
        var ul = $('#ware-sortable-items');
        var li = `<li id="ware_item_${new_li}">
                    <header>
                        <i class="dashicons-before dashicons-arrow-down-alt2" aria-hidden="true"></i> New Rule
                    </header>
                    <div class="ware-item-body">
                        <div class="ware-body-left">
                            <p class="ware-match">
                                <label for="keyword-match-${new_li}">Keyword Match</label>
                                <select id="keyword-match-${new_li}" class="widefat" name="ware_autoresponders[items][${new_li}][item_match]">
                                    <option value="partial_all">Contain Keyword</option>
                                    <option value="match">Exact Match</option>
                                    <option value="partial">Beginning Sentence</option>
                                </select>
                            </p>
                            <p class="ware-keyword">
                                <label for="chat-keyword-${new_li}">Chat Keyword</label>
                                <input type="text" id="chat-keyword-${new_li}" class="widefat" name="ware_autoresponders[items][${new_li}][item_keyword]">
                            </p>
                        </div>
                        <div class="ware-body-right">
                            <p>
                                <label for="autoresponder-reply-${new_li}">Autoresponder Reply</label>
                                <textarea rows="5" id="autoresponder-reply-${new_li}" class="widefat" name="ware_autoresponders[items][${new_li}][item_reply]"></textarea>
                            </p>
                            <p class="ware-upload-img">
                                <input type="button" name="upload-btn" class="upload-btn button-secondary" data-id="item-img-${new_li}" value="Upload Image">
                                <input type="text" name="ware_autoresponders[items][${new_li}][item_img]" placeholder="Image URL (Max 1 MB)" class="image_url regular-text item-img-${new_li}">
                            </p>
                        </div>
                        <div class="ware-item-controls">
                            <a href="#" class="ware-remove-item">Delete</a>
                        </div>
                    </div>
                </li>`;
        ul.prepend(li);
        $('#ware-sortable-items').accordion("refresh");
        ware_action('#ware-sortable-items');
        return false;
    });

    $("#log-search").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("table tr:not(.header-row)").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    function ware_action(el) {
        var items_sort = $(el).sortable('serialize', { key: 'sort' });
        $('#ware-items-order').val(items_sort);
        $('.ware-remove-item').click(function () {
            $('#ware-sortable-items').accordion('option', { active: false });
            $(this).parents('li').remove();
            return false;
        });
    }

    $('.ware-tab-wrapper').on("click", '.upload-btn', function (e) {
        e.preventDefault();
        localStorage.setItem("upload-btn-class", $(this).data('id'));
        var input_id = localStorage.getItem("upload-btn-class");
        var image = wp.media({
            title: 'Upload Image',
            multiple: false
        }).open()
            .on('select', function (e) {
                var uploaded_image = image.state().get('selection').first();
                var image_url = uploaded_image.toJSON().url;
                $("." + input_id).val(image_url);
            });
    });

    $("#ware_broadcast_target").change(function () {
        if ($(this).val() === "custom") {
            $(".broadcast-list-wrapper").show();
        } else {
            $(".broadcast-list-wrapper").hide();
        }
    });

    $('.instance-desc > strong').click(function () {
        $(this).toggleClass('active');
        $('.instance-desc > div').toggle();
    });

    let token = $('#access_token').val();
    let instance = $('#instance_id').val();

    $('.ins-action').click(function (e) {
        let $this = $(this);
        let actionData = $(this).data('action');
        let controlPopup = '';
        if (actionData == 'reconnect') {
            controlPopup += '<h2>Are you sure you want to reconnect instance?</h2>';
            controlPopup += '<div class="ins-btn-wrapper"><a href="#" class="button button-primary" id="ins-btn" data-button="reconnect">Reconnect</a> <a href="#" class="button button-secondary" rel="modal:close">Cancel</a></div>';
            controlPopup += '<div class="ins-results"></div>';
        }
        if (actionData == 'reboot') {
            controlPopup += '<h2>Are you sure you want to reboot instance?</h2>';
            controlPopup += '<div class="ins-btn-wrapper"><a href="#" class="button button-primary" id="ins-btn" data-button="reboot">Reboot</a> <a href="#" class="button button-secondary" rel="modal:close">Cancel</a></div>';
            controlPopup += '<div class="ins-results"></div>';
        }
        if (actionData == 'status') {
            controlPopup += '<h2>Connection Status</h2>';
            controlPopup += '<div class="ins-results"><div class="loader"></div></div>';
            $.getJSON('https://app.arrivewhats.com/api/reconnect?instance_id=' + instance + '&access_token=' + token, function (data) {
                let deviceStatus = '';
                if (data.data.avatar.includes('whatsapp')) {
                    deviceStatus = 'Connected';
                } else {
                    deviceStatus = 'Disconnected';
                }
                $('#control-modal').find('.ins-results').html('<div class="response">Phone ' + deviceStatus + '</div>');
            });
        }
        if (actionData == 'connectionButtons') {
            controlPopup += '<h2>Message Sending Status</h2>';
            controlPopup += '<div class="ins-results"><div class="loader"></div></div>';

            $.getJSON('https://app.arrivewhats.com/api/send?instance_id=' + instance + '&access_token=' + token + '&number=249119543168' + '&type=text' + '&message=ArriveWoo+Notification+work', function (data) {

                $('#control-modal').find('.ins-results').html('<div class="response">' + JSON.stringify(data) + '</div>');
            });
        }
        if (actionData == 'reset') {
            controlPopup += '<h2>Are you sure you want to reset instance?</h2>';
            controlPopup += '<div class="ins-btn-wrapper"><a href="#" class="button button-primary" id="ins-btn" data-button="reset">Reset</a> <a href="#" class="button button-secondary" rel="modal:close">Cancel</a></div>';
            controlPopup += '<div class="ins-results"></div>';
        }
        if (actionData == 'webhook') {
            controlPopup += '<h2>Set new webhook url below:</h2>';
            controlPopup += '<div class="ins-btn-wrapper"><input type="url" id="ins-webhook" placeholder="https://webhook.site/sample.php"><a href="#" class="button button-primary" id="ins-btn" data-button="webhook">Submit</a></div>';
            controlPopup += '<div class="ins-results"></div>';
        }
        $('#control-modal').html('<div class="controlPopup">' + controlPopup + '</div>');
        $('#control-modal').modal();
    });

    $('#control-modal').on("click", '#ins-btn', function (e) {
        let $this = $(this);
        $this.parent().hide();
        $this.parents('.modal').find('.ins-results').html('<div class="loader"></div>');
        if ($this.data('button') == 'reconnect') {
            $.getJSON('https://app.arrivewhats.com/api/reconnect?instance_id=' + instance + '&access_token=' + token, function (data) {
                $this.parents('.modal').find('.ins-results').html('<div class="response">Reconnect ' + data.message + '</div>');
            });
        }
        if ($this.data('button') == 'reboot') {
            $.getJSON('https://app.arrivewhats.com/api/reboot?instance_id=' + instance + '&access_token=' + token, function (data) {
                $this.parents('.modal').find('.ins-results').html('<div class="response-reboot">' + data.message + '. Please click "Generate QR Code" button and scan in 30 seconds<br><a href="#" class="button button-primary" id="ins-btn" data-button="generate">Generate QR Code</a></div></div>');
            });
        }
        if ($this.data('button') == 'generate') {
            $.getJSON('https://app.arrivewhats.com/api/get_qrcode?instance_id=' + instance + '&access_token=' + token, function (data) {
                $('#control-modal').find('.ins-results').html('<div class="response-qr"><img id="qr-code" src="' + data.base64 + '"></div>');
                setTimeout(function (e) {
                    $('#control-modal').find('.response-qr').html('Close this popup if you have successfully scanned the qr code or retry the process again if you haven\'t');
                }, 30 * 1000);
            });
        }
        if ($this.data('button') == 'reset') {
            $.getJSON('https://app.arrivewhats.com/api/reset_instance?instance_id=' + instance + '&access_token=' + token, function (data) {
                $this.parents('.modal').find('.ins-results').html('<div class="response">' + data.message + '. Please check your new Instance ID on <a href="https://app.arrivewhats.com/whatsapp">WaTrend Dashboard Page</a> and update your old one on Device Settings tab.</div>');
            });
        }
        if ($this.data('button') == 'webhook') {
            let webhookUrl = $this.parents('.modal').find('#ins-webhook').val();
            $.getJSON('https://app.arrivewhats.com/api/set_webhook?webhook_url=' + webhookUrl + '&enable=true&instance_id=' + instance + '&access_token=' + token, function (data) {
                console.log(data);
                $this.parents('.modal').find('.ins-results').html('<div class="response">' + data.message + '</div>');
            });
        }
    });
    var editorTabs = document.querySelectorAll('.editor-tab');
    var editorContents = document.querySelectorAll('.editor-content');
    
    // Set default language based on text direction
    var defaultLang = document.documentElement.getAttribute('dir') === 'rtl' ? 'arabic' : 'english';
    
    // Show the default language editor by default
    setActiveTab(defaultLang);
    showContent(defaultLang);
    
    editorTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            var lang = this.getAttribute('data-lang');
            setActiveTab(lang);
            showContent(lang);
        });
    });
    
    function setActiveTab(lang) {
        // Remove "active" class from all tabs
        editorTabs.forEach(function (tab) {
            tab.classList.remove('active');
        });
    
        // Add "active" class to the selected tab
        var selectedTab = document.querySelector('.editor-tab[data-lang="' + lang + '"]');
        if (selectedTab) {
            selectedTab.classList.add('active');
        }
    }
    
    function showContent(lang) {
        // Hide all editor contents
        editorContents.forEach(function (content) {
            content.style.display = 'none';
        });
    
        // Show the selected language's editor content
        var selectedContent = document.querySelector('.editor-content[data-lang="' + lang + '"]');
        if (selectedContent) {
            selectedContent.style.display = 'flex';
        }
    }
});





